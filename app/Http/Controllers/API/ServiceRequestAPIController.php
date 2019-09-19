<?php

namespace App\Http\Controllers\API;

use App\Criteria\Common\RequestCriteria;
use App\Criteria\ServiceRequests\FilterByInternalFieldsCriteria;
use App\Criteria\ServiceRequests\FilterByPermissionsCriteria;
use App\Criteria\ServiceRequests\FilterByRelatedFieldsCriteria;
use App\Criteria\ServiceRequests\FilterPublicCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\ServiceRequest\AssignRequest;
use App\Http\Requests\API\ServiceRequest\ChangePriorityRequest;
use App\Http\Requests\API\ServiceRequest\ChangeStatusRequest;
use App\Http\Requests\API\ServiceRequest\ConversationRequest;
use App\Http\Requests\API\ServiceRequest\CreateRequest;
use App\Http\Requests\API\ServiceRequest\DeleteRequest;
use App\Http\Requests\API\ServiceRequest\ListRequest;
use App\Http\Requests\API\ServiceRequest\NotifyProviderRequest;
use App\Http\Requests\API\ServiceRequest\UpdateRequest;
use App\Models\ServiceRequest;
use App\Repositories\ServiceProviderRepository;
use App\Repositories\ServiceRequestRepository;
use App\Repositories\TemplateRepository;
use App\Repositories\UserRepository;
use App\Transformers\ServiceRequestTransformer;
use App\Transformers\TemplateTransformer;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;

/**
 * Class ServiceRequestController
 * @package App\Http\Controllers\API
 */
class ServiceRequestAPIController extends AppBaseController
{
    /** @var  ServiceRequestRepository */
    private $serviceRequestRepository;

    /**
     * ServiceRequestAPIController constructor.
     * @param ServiceRequestRepository $serviceRequestRepo
     */
    public function __construct(ServiceRequestRepository $serviceRequestRepo)
    {
        $this->serviceRequestRepository = $serviceRequestRepo;
    }

    /**
     * @param ListRequest $request
     * @return Response
     * @throws Exception
     *
     * @SWG\Get(
     *      path="/requests",
     *      summary="Get a listing of the ServiceRequests.",
     *      tags={"ServiceRequest"},
     *      description="Get all ServiceRequests",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/ServiceRequest")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(ListRequest $request)
    {
        $this->serviceRequestRepository->pushCriteria(new RequestCriteria($request));
        $this->serviceRequestRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->serviceRequestRepository->pushCriteria(new FilterByPermissionsCriteria($request));
        $this->serviceRequestRepository->pushCriteria(new FilterByInternalFieldsCriteria($request));
        $this->serviceRequestRepository->pushCriteria(new FilterPublicCriteria($request));
        $this->serviceRequestRepository->pushCriteria(new FilterByRelatedFieldsCriteria($request));

        $getAll = $request->get('get_all', false);
        if ($getAll) {
            $serviceRequests = $this->serviceRequestRepository
                ->with('category')->get();
            $response = (new ServiceRequestTransformer)->transformCollection($serviceRequests);
            return $this->sendResponse($response, 'Service Requests retrieved successfully');
        }
        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $serviceRequests = $this->serviceRequestRepository
            ->with([
                'media',
                'tenant.user',
                'tenant.building.address',
                'category',
                'comments.user',
                'providers.address',
                'providers.user',
                'assignees',
            ])->paginate($perPage);

        $serviceRequests->getCollection()->loadCount('allComments');
        $response = (new ServiceRequestTransformer)->transformPaginator($serviceRequests);
        return $this->sendResponse($response, 'Service Requests retrieved successfully');
    }

    /**
     * @param CreateRequest $request
     * @return Response
     * @throws Exception
     *
     * @SWG\Post(
     *      path="/requests",
     *      summary="Store a newly created ServiceRequest in storage",
     *      tags={"ServiceRequest"},
     *      description="Store ServiceRequest",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ServiceRequest that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ServiceRequest")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ServiceRequest"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateRequest $request)
    {
        $input = $request->all();

        $serviceRequest = $this->serviceRequestRepository->create($input);
        $this->serviceRequestRepository->notifyNewRequest($serviceRequest);
        if (isset($input['due_date'])) {
            $this->serviceRequestRepository->notifyDue($serviceRequest);
        }

        $response = (new ServiceRequestTransformer)->transform($serviceRequest);
        return $this->sendResponse($response, 'Service Request saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/requests/{id}",
     *      summary="Display the specified ServiceRequest",
     *      tags={"ServiceRequest"},
     *      description="Get ServiceRequest",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ServiceRequest",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ServiceRequest"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var ServiceRequest $serviceRequest */
        $serviceRequest = $this->serviceRequestRepository->findWithoutFail($id);
        if (empty($serviceRequest)) {
            return $this->sendError('Service Request not found');
        }

        $serviceRequest->load([
            'media', 'tenant.user', 'tenant.building', 'category', 'assignees',
            'comments.user', 'providers.address', 'providers.user',
        ]);
        $response = (new ServiceRequestTransformer)->transform($serviceRequest);
        return $this->sendResponse($response, 'Service Request retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateRequest $request
     * @return Response
     * @throws Exception
     *
     * @SWG\Put(
     *      path="/requests/{id}",
     *      summary="Update the specified ServiceRequest in storage",
     *      tags={"ServiceRequest"},
     *      description="Update ServiceRequest",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ServiceRequest",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ServiceRequest that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ServiceRequest")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ServiceRequest"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateRequest $request)
    {
        $input = $request->only(ServiceRequest::Fillable);

        /** @var ServiceRequest $serviceRequest */
        $serviceRequest = $this->serviceRequestRepository->findWithoutFail($id);
        if (empty($serviceRequest)) {
            return $this->sendError('Service Request not found');
        }

        $oldStatus = $serviceRequest->status;
        if (!$this->serviceRequestRepository->checkStatusPermission($input, $oldStatus)) {
            return $this->sendError('You are not allowed to change status');
        }

        $attr = $this->serviceRequestRepository->getPutAttributes($input, $oldStatus);
        $updatedServiceRequest = $this->serviceRequestRepository->update($attr, $id);

        $this->serviceRequestRepository->notifyStatusChange($serviceRequest, $updatedServiceRequest);
        if ($updatedServiceRequest->due_date && $updatedServiceRequest->due_date != $serviceRequest->due_date) {
            $this->serviceRequestRepository->notifyDue($updatedServiceRequest);
        }

        $updatedServiceRequest->load([
            'media', 'tenant.user', 'category', 'assignees',
            'comments.user', 'providers.address', 'providers.user',
        ]);
        $response = (new ServiceRequestTransformer)->transform($updatedServiceRequest);
        return $this->sendResponse($response, 'ServiceRequest updated successfully');
    }

    /**
     * @param int $id
     * @param ChangeStatusRequest $request
     * @return Response
     * @throws Exception
     *
     * @SWG\Post(
     *      path="/requests/{id}/changeStatus",
     *      summary="Change status on ServiceRequest",
     *      tags={"ServiceRequest"},
     *      description="Change status on ServiceRequest",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ServiceRequest that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ServiceRequest")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ServiceRequest"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function changeStatus(int $id, ChangeStatusRequest $request)
    {
        /** @var ServiceRequest $serviceRequest */
        $serviceRequest = $this->serviceRequestRepository->findWithoutFail($id);
        if (empty($serviceRequest)) {
            return $this->sendError('Service Request not found');
        }

        $input = [
            'status' => $request->get('status', '')
        ];

        if (!$this->serviceRequestRepository->checkStatusPermission($input, $serviceRequest->status)) {
            return $this->sendError('You are not allowed to change status');
        }

        $serviceRequest = $this->serviceRequestRepository->update($input, $id);

        $response = (new ServiceRequestTransformer)->transform($serviceRequest);
        return $this->sendResponse($response, 'Status Changed successfully');
    }

    /**
     * @param int $id
     * @param ChangePriorityRequest $request
     * @return Response
     * @throws Exception
     *
     * @SWG\Post(
     *      path="/requests/{id}/changePriority",
     *      summary="Change status on ServiceRequest",
     *      tags={"ServiceRequest"},
     *      description="Change priority on ServiceRequest",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ServiceRequest that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ServiceRequest")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ServiceRequest"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function changePriority(int $id, ChangePriorityRequest $request)
    {
        /** @var ServiceRequest $serviceRequest */
        $serviceRequest = $this->serviceRequestRepository->findWithoutFail($id);
        if (empty($serviceRequest)) {
            return $this->sendError('Service Request not found');
        }

        $input = [
            'priority' => $request->get('priority', '')
        ];

        $serviceRequest = $this->serviceRequestRepository->update($input, $id);

        $response = (new ServiceRequestTransformer)->transform($serviceRequest);
        return $this->sendResponse($response, 'Priority Changed successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/requests/{id}",
     *      summary="Remove the specified ServiceRequest from storage",
     *      tags={"ServiceRequest"},
     *      description="Delete ServiceRequest",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ServiceRequest",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id, DeleteRequest $r)
    {
        /** @var ServiceRequest $serviceRequest */
        $serviceRequest = $this->serviceRequestRepository->findWithoutFail($id);
        if (empty($serviceRequest)) {
            return $this->sendError('Service Request not found');
        }

        $serviceRequest->delete();

        return $this->sendResponse($id, 'Service Request deleted successfully');
    }

    /**
     * @param int $id
     * @param NotifyProviderRequest $request
     * @param ServiceProviderRepository $spRepo
     * @return Response
     *
     * @SWG\Post(
     *      path="/requests/{id}/notify",
     *      summary="Notify the provided service provider",
     *      tags={"ServiceRequest"},
     *      description="Notify the provided service provider",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ServiceRequest",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="provider_id",
     *          in="body",
     *          description="ServiceProvider id",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ServiceProvider")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ServiceProvider"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function notifyProvider(int $id, NotifyProviderRequest $request,
                                   ServiceProviderRepository $spRepo,
                                   UserRepository $uRepo)
    {
        $sr = $this->serviceRequestRepository->findWithoutFail($id);
        if (empty($sr)) {
            return $this->sendError('Service Request not found');
        }
        $sp = $spRepo->findWithoutFail($request->provider_id);
        if (empty($sp)) {
            return $this->sendError('Service Provider not found');
        }

        $assignees = $uRepo->findWhereIn('id', $request->assignee_ids ?? []);

        $mailDetails = $request->only(['title', 'to', 'cc', 'bcc', 'body']);
        $this->serviceRequestRepository->notifyProvider($sr, $sp, $assignees, $mailDetails);

        $a = $this->newRequestAudit($sr->id);
        $a->event = 'provider_notified';
        $a->new_values = [
            'provider_id' => $sp->id,
            'provider_name' => $sp->name,
            'email_title' => $mailDetails['title'],
            'emai_cc' => $mailDetails['cc'],
            'emai_bcc' => $mailDetails['bcc'],
            'emai_to' => $mailDetails['to'],
        ];
        $a->save();

        return $this->sendResponse($sr, 'Email notification sent successfully');
    }

    /**
     * @param int $id
     * @param int $pid
     * @param AssignRequest $request
     * @param ServiceProviderRepository $spRepo
     * @return Response
     *
     * @SWG\Post(
     *      path="/requests/{id}/providers/{pid}",
     *      summary="Assign the provided service provider to the request",
     *      tags={"ServiceRequest"},
     *      description="Assign the provided service provider to the request",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ServiceRequest"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function assignProvider(int $id, int $pid,
                                   ServiceProviderRepository $spRepo, AssignRequest $r)
    {
        $sr = $this->serviceRequestRepository->findWithoutFail($id);
        if (empty($sr)) {
            return $this->sendError('Service Request not found');
        }
        $sp = $spRepo->findWithoutFail($pid);
        if (empty($sp)) {
            return $this->sendError('Service Provider not found');
        }

        $sr->providers()->sync($sp, false);
        $sr->load('media', 'tenant.user', 'category', 'comments.user',
            'providers.address', 'providers.user', 'assignees');

        $a = $this->newRequestAudit($sr->id);
        $a->event = 'provider_assigned';
        $a->new_values = [
            'provider_id' => $pid,
            'provider_name' => $sp->name,
        ];
        $a->save();

        foreach ($sr->assignees as $a) {
            $sr->conversationFor($a, $sp->user);
        }

        $sr->conversationFor(Auth::user(), $sp->user);

        return $this->sendResponse($sr, 'Service provider assigned successfully');
    }

    /**
     * @param int $id
     * @param int $pid
     * @param AssignRequest $request
     * @param ServiceProviderRepository $spRepo
     * @return Response
     *
     * @SWG\Delete(
     *      path="/requests/{id}/providers/{pid}",
     *      summary="Unassign the provided service provider from the request",
     *      tags={"ServiceRequest"},
     *      description="Unassign the provided service provider from the request",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ServiceRequest"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function unassignProvider(int $id, int $pid,
                                     ServiceProviderRepository $spRepo, AssignRequest $r)
    {
        $sr = $this->serviceRequestRepository->findWithoutFail($id);
        if (empty($sr)) {
            return $this->sendError('Service Request not found');
        }
        $sp = $spRepo->findWithoutFail($pid);
        if (empty($sp)) {
            return $this->sendError('Service Provider not found');
        }

        $sr->providers()->detach($sp);
        $sr->load('media', 'tenant.user', 'category', 'comments.user',
            'providers.address', 'providers.user', 'assignees');

        $a = $this->newRequestAudit($sr->id);
        $a->event = 'provider_unassigned';
        $a->old_values = [
            'provider_id' => $pid,
            'provider_name' => $sp->name,
        ];
        $a->save();

        return $this->sendResponse($sr, 'Service provider unassigned successfully');
    }

    /**
     * @param int $id
     * @param int $uid
     * @param UserRepository $uRepo
     * @param AssignRequest $r
     * @return Response
     *
     * @SWG\Post(
     *      path="/requests/{id}/assignees/{uid}",
     *      summary="Assign the provided user to the request",
     *      tags={"ServiceRequest"},
     *      description="Assign the provided user to the request",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ServiceRequest"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function assignUser(int $id, int $uid, UserRepository $uRepo, AssignRequest $r)
    {
        $sr = $this->serviceRequestRepository->findWithoutFail($id);
        if (empty($sr)) {
            return $this->sendError('Service Request not found');
        }
        $u = $uRepo->findWithoutFail($uid);
        if (empty($u)) {
            return $this->sendError('User not found');
        }

        $sr->assignees()->sync($u, false);
        $sr->load('media', 'tenant.user', 'category', 'comments.user',
            'providers.address', 'providers.user', 'assignees');

        $a = $this->newRequestAudit($sr->id);
        $a->event = 'user_assigned';
        $a->new_values = [
            'user_id' => $uid,
            'user_name' => $u->name,
        ];
        $a->save();

        foreach ($sr->providers as $p) {
            $sr->conversationFor($p->user, $u);
        }

        return $this->sendResponse($sr, 'User assigned successfully');
    }

    /**
     * @param int $id
     * @param int $uid
     * @param AssignRequest $r
     * @param UserRepository $uRepo
     * @return Response
     *
     * @SWG\Delete(
     *      path="/requests/{id}/assignees/{uid}",
     *      summary="Unassign the provided user to the request",
     *      tags={"ServiceRequest"},
     *      description="Unassign the provided user to the request",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ServiceRequest"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function unassignUser(int $id, int $uid, UserRepository $uRepo, AssignRequest $r)
    {
        $sr = $this->serviceRequestRepository->findWithoutFail($id);
        if (empty($sr)) {
            return $this->sendError('Service Request not found');
        }
        $u = $uRepo->findWithoutFail($uid);
        if (empty($u)) {
            return $this->sendError('User not found');
        }

        $sr->assignees()->detach($u);
        $sr->load('media', 'tenant.user', 'category', 'comments.user',
            'providers.address', 'providers.user', 'assignees');

        $a = $this->newRequestAudit($sr->id);
        $a->event = 'user_unassigned';
        $a->old_values = [
            'user_id' => $uid,
            'user_name' => $u->name,
        ];
        $a->save();

        return $this->sendResponse($sr, 'User unassigned successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     * @throws Exception
     *
     * @SWG\Get(
     *      path="/requests/{id}/assignees",
     *      summary="Get a listing of the ServiceRequest assignees.",
     *      tags={"ServiceRequest"},
     *      description="Get a listing of the ServiceRequest assignees.",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/ServiceRequest")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function getAssignees(int $id, Request $request)
    {
        $sr = $this->serviceRequestRepository->findWithoutFail($id);
        if (empty($sr)) {
            return $this->sendError('Service Request not found');
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $assignees = $this->serviceRequestRepository->assignees($sr)->paginate($perPage);
        return $this->sendResponse($assignees, 'Assignees retrieved successfully');
    }

    /**
     * @param int $id
     * @param TemplateRepository $tempRepo
     *
     * @return Response
     *
     * @SWG\Get(
     *      path="/requests/{id}/comunicationTemplates",
     *      summary="Display the list of Comunication templates filled with request data",
     *      tags={"ServiceRequest"},
     *      description="Get CommunicationTemplates",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ServiceRequest",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *
     *
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ServiceRequest"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function getCommunicationTemplates($id, TemplateRepository $tempRepo)
    {
        /** @var ServiceRequest $serviceRequest */
        $serviceRequest = $this->serviceRequestRepository->findWithoutFail($id);
        if (empty($serviceRequest)) {
            return $this->sendError('Service Request not found');
        }

        $serviceRequest->load([
            'media', 'tenant.user', 'tenant.building', 'category',
        ]);

        $templates = $tempRepo->getParsedCommunicationTemplates($serviceRequest, Auth::user());

        $response = (new TemplateTransformer)->transformCollection($templates);
        return $this->sendResponse($response, 'Communication Templates retrieved successfully');
    }

    /**
     * @param int $id
     * @param TemplateRepository $tempRepo
     *
     * @return Response
     *
     * @SWG\Get(
     *      path="/requests/{id}/serviceComunicationTemplates",
     *      summary="Display the list of Service Comunication templates filled with request data",
     *      tags={"ServiceRequest"},
     *      description="Get Service Communication Templates",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ServiceRequest",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *
     *
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ServiceRequest"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function getServiceCommunicationTemplates($id, TemplateRepository $tempRepo)
    {
        /** @var ServiceRequest $serviceRequest */
        $serviceRequest = $this->serviceRequestRepository->findWithoutFail($id);
        if (empty($serviceRequest)) {
            return $this->sendError('Service Request not found');
        }

        $serviceRequest->load([
            'media', 'tenant.user', 'tenant.building', 'category',
        ]);

        $templates = $tempRepo->getParsedServiceCommunicationTemplates($serviceRequest, Auth::user());

        $response = (new TemplateTransformer)->transformCollection($templates);
        return $this->sendResponse($response, 'Service Communication Templates retrieved successfully');
    }

    /**
     * @param int $id
     * @param TemplateRepository $tempRepo
     *
     * @return Response
     *
     * @SWG\Get(
     *      path="/requests/{id}/serviceemailTemplates",
     *      summary="Display the list of Service Email templates filled with request data",
     *      tags={"ServiceRequest"},
     *      description="Get Service Email Templates",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ServiceRequest",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *
     *
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ServiceRequest"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function getServiceEmailTemplates($id, TemplateRepository $tempRepo)
    {
        /** @var ServiceRequest $serviceRequest */
        $serviceRequest = $this->serviceRequestRepository->findWithoutFail($id);
        if (empty($serviceRequest)) {
            return $this->sendError('Service Request not found');
        }

        $serviceRequest->load([
            'media', 'tenant.user', 'tenant.building', 'category',
        ]);

        $templates = $tempRepo->getParsedEmailTemplates($serviceRequest, Auth::user());

        $response = (new TemplateTransformer)->transformCollection($templates);
        return $this->sendResponse($response, 'Service Email Templates retrieved successfully');
    }
}
