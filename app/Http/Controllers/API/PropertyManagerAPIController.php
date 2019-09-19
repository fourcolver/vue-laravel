<?php

namespace App\Http\Controllers\API;

use App\Criteria\Common\RequestCriteria;
use App\Criteria\PropertyManagers\FilterByRelatedFieldsCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\PropertyManager\AssignRequest;
use App\Http\Requests\API\PropertyManager\BatchDeleteRequest;
use App\Http\Requests\API\PropertyManager\CreateRequest;
use App\Http\Requests\API\PropertyManager\DeleteRequest;
use App\Http\Requests\API\PropertyManager\ListRequest;
use App\Http\Requests\API\PropertyManager\UpdateRequest;
use App\Http\Requests\API\PropertyManager\ViewRequest;
use App\Models\PropertyManager;
use App\Models\User;
use App\Repositories\BuildingRepository;
use App\Repositories\DistrictRepository;
use App\Repositories\PropertyManagerRepository;
use App\Repositories\UserRepository;
use App\Transformers\PropertyManagerTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Validator;

/**
 * Class PropertyManagerController
 * @package App\Http\Controllers\API
 */
class PropertyManagerAPIController extends AppBaseController
{
    /** @var  PropertyManagerRepository */
    private $propertyManagerRepository;

    /** @var  UserRepository */
    private $userRepository;

    public function __construct(PropertyManagerRepository $propertyManagerRepo, UserRepository $userRepo)
    {
        $this->propertyManagerRepository = $propertyManagerRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * @param ListRequest $request
     * @return Response
     * @throws \Exception
     *
     * @SWG\Get(
     *      path="/propertyManagers",
     *      summary="Get a listing of the PropertyManagers.",
     *      tags={"PropertyManager"},
     *      description="Get all PropertyManagers",
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
     *                  @SWG\Items(ref="#/definitions/PropertyManager")
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
        $this->propertyManagerRepository->pushCriteria(new RequestCriteria($request));
        $this->propertyManagerRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->propertyManagerRepository->pushCriteria(new FilterByRelatedFieldsCriteria($request));

        $getAll = $request->get('get_all', false);
        $reqCount = $request->get('req_count');
        if ($reqCount) {
            $this->propertyManagerRepository->with([
                'user' => function ($q) {
                    $q->withCount([
                        'requestsReceived',
                        'requestsInProcessing',
                        'requestsAssigned',
                        'requestsDone',
                        'requestsReactivated',
                        'requestsArchived',
                    ]);
                }
            ]);
        } else {
            $this->propertyManagerRepository->with([
                'user',
            ]);
        }

        if ($getAll) {
            $propertyManagers = $this->propertyManagerRepository->get();
            $response = (new PropertyManagerTransformer)->transformCollection($propertyManagers);
            return $this->sendResponse($response, 'Property Managers retrieved successfully');
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $propertyManagers = $this->propertyManagerRepository->paginate($perPage);
        $response = (new PropertyManagerTransformer)->transformPaginator($propertyManagers);
        return $this->sendResponse($response, 'Property Managers retrieved successfully');
    }

    /**
     * @param CreateRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/propertyManagers",
     *      summary="Store a newly created PropertyManager in storage",
     *      tags={"PropertyManager"},
     *      description="Store PropertyManager",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PropertyManager that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PropertyManager")
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
     *                  ref="#/definitions/PropertyManager"
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
        $input['role'] = 'manager';

        $input['user']['name'] = sprintf('%s %s', $input['first_name'], $input['last_name']);
        $validator = Validator::make($input['user'], User::$rules);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        try {
            $user = $this->userRepository->create($input['user']);
        } catch (\Exception $e) {
            return $this->sendError('Property Manager create error: ' . $e->getMessage());
        }

        $input['user_id'] = $user->id;
        try {
            $propertyManagers = $this->propertyManagerRepository->create($input);
        } catch (\Exception $e) {
            return $this->sendError('Property Manager create error: ' . $e->getMessage());
        }

        $propertyManagers->load('user', 'buildings', 'districts');
        $response = (new PropertyManagerTransformer)->transform($propertyManagers);
        return $this->sendResponse($response, 'Property Manager saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/propertyManagers/{id}",
     *      summary="Display the specified PropertyManager",
     *      tags={"PropertyManager"},
     *      description="Get PropertyManager",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PropertyManager",
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
     *                  ref="#/definitions/PropertyManager"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id, ViewRequest $r)
    {
        /** @var PropertyManager $propertyManager */
        $propertyManager = $this->propertyManagerRepository->find($id);
        if (empty($propertyManager)) {
            return $this->sendError('Property Manager not found');
        }

        $propertyManager->load('user', 'buildings', 'districts');
        $response = (new PropertyManagerTransformer)->transform($propertyManager);
        return $this->sendResponse($response, 'Property Manager retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/propertyManagers/{id}",
     *      summary="Update the specified PropertyManager in storage",
     *      tags={"PropertyManager"},
     *      description="Update PropertyManager",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PropertyManager",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PropertyManager that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PropertyManager")
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
     *                  ref="#/definitions/PropertyManager"
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
        $input = $request->all();
        /** @var PropertyManager $propertyManager */
        $propertyManager = $this->propertyManagerRepository->find($id);
        if (empty($propertyManager)) {
            return $this->sendError('Property Manager not found');
        }

        try {
            $propertyManager = $this->propertyManagerRepository->update($input, $id);
        } catch (\Exception $e) {
            return $this->sendError('Property Manager updated error: ' . $e->getMessage());
        }

        if (isset($input['user'])) {
            $input['user']['name'] = sprintf('%s %s', $input['first_name'], $input['last_name']);;
//            $input['user']['email'] = $propertyManager->user->email;
            $validator = Validator::make($input['user'], User::$rulesUpdate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            try {
                $this->userRepository->update($input['user'], $propertyManager->user_id);
            } catch (\Exception $e) {
                return $this->sendError('Property Manager updated error: ' . $e->getMessage());
            }
        }

        $propertyManager->load('user', 'buildings', 'districts');
        $response = (new PropertyManagerTransformer)->transform($propertyManager);
        return $this->sendResponse($response, 'PropertyManager updated successfully');
    }

    /**
     * @param int $id
     * @param DeleteRequest $r
     * @return Response
     *
     * @SWG\Delete(
     *      path="/propertyManagers/{id}",
     *      summary="Remove the specified PropertyManager from storage",
     *      tags={"PropertyManager"},
     *      description="Delete PropertyManager",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PropertyManager",
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
        /** @var PropertyManager $propertyManager */
        $propertyManager = $this->propertyManagerRepository->find($id);
        if (empty($propertyManager)) {
            return $this->sendError('Property Manager not found');
        }

        $propertyManager->delete();

        return $this->sendResponse($id, 'Property Manager deleted successfully');
    }

    /**
     * @param int $id
     * @param int $did
     * @param AssignRequest $r
     * @param DistrictRepository $dRepo
     * @return Response
     *
     * @SWG\Post(
     *      path="/propertyManagers/{id}/districts/{did}",
     *      summary="Assign the provided district to the property manager",
     *      tags={"PropertyManager"},
     *      description="Assign the provided district to the property manager",
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
     *                  ref="#/definitions/PropertyManager"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function assignDistrict(int $id, int $did,
                                   DistrictRepository $dRepo, AssignRequest $r)
    {
        $pm = $this->propertyManagerRepository->findWithoutFail($id);
        if (empty($pm)) {
            return $this->sendError('Property manager not found');
        }
        $d = $dRepo->findWithoutFail($did);
        if (empty($d)) {
            return $this->sendError('District not found');
        }

        $pm->districts()->sync($d, false);
        $pm->load('districts', 'buildings');

        return $this->sendResponse($pm, 'District assigned successfully');
    }

    /**
     * @param int $id
     * @param int $did
     * @param AssignRequest $request
     * @param DistrictRepository $dRepo
     * @return Response
     *
     * @SWG\Delete(
     *      path="/propertyManagers/{id}/districts/{did}",
     *      summary="Unassign the provided district from the property manager",
     *      tags={"PropertyManager"},
     *      description="Unassign the provided district from the property manager",
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
     *                  ref="#/definitions/PropertyManager"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function unassignDistrict(int $id, int $did,
                                     DistrictRepository $dRepo, AssignRequest $r)
    {
        $pm = $this->propertyManagerRepository->findWithoutFail($id);
        if (empty($pm)) {
            return $this->sendError('Property manager not found');
        }
        $d = $dRepo->findWithoutFail($did);
        if (empty($d)) {
            return $this->sendError('District not found');
        }

        $pm->districts()->detach($d);
        $pm->load('districts', 'buildings');

        return $this->sendResponse($pm, 'District unassigned successfully');
    }

    /**
     * @param int $id
     * @param int $bid
     * @param AssignRequest $r
     * @param BuildingRepository $bRepo
     * @return Response
     *
     * @SWG\Post(
     *      path="/propertyManagers/{id}/buildings/{bid}",
     *      summary="Assign the provided building to the property manager",
     *      tags={"PropertyManager"},
     *      description="Assign the provided building to the property manager",
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
     *                  ref="#/definitions/PropertyManager"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function assignBuilding(int $id, int $bid,
                                   BuildingRepository $bRepo, AssignRequest $r)
    {
        $pm = $this->propertyManagerRepository->findWithoutFail($id);
        if (empty($pm)) {
            return $this->sendError('Property manager not found');
        }
        $b = $bRepo->findWithoutFail($bid);
        if (empty($b)) {
            return $this->sendError('Building not found');
        }

        if ($b->district_id && $pm->districts->contains('id', $b->district_id)) {
            return $this->sendError('Building already assigned through district');
        }

        $pm->buildings()->sync($b, false);
        $pm->load('districts', 'buildings');

        return $this->sendResponse($pm, 'Building assigned successfully');
    }

    /**
     * @param int $id
     * @param int $bid
     * @param AssignRequest $r
     * @param BuildingRepository $bRepo
     * @return Response
     *
     * @SWG\Delete(
     *      path="/propertyManagers/{id}/buildings/{bid}",
     *      summary="Unassign the provided building from the property manager",
     *      tags={"PropertyManager"},
     *      description="Unassign the provided building from the property manager",
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
     *                  ref="#/definitions/PropertyManager"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function unassignBuilding(int $id, int $bid,
                                     BuildingRepository $bRepo, AssignRequest $r)
    {
        $pm = $this->propertyManagerRepository->findWithoutFail($id);
        if (empty($pm)) {
            return $this->sendError('Property manager not found');
        }
        $b = $bRepo->findWithoutFail($bid);
        if (empty($b)) {
            return $this->sendError('Building not found');
        }

        $pm->buildings()->detach($b);
        $pm->load('districts', 'buildings');

        return $this->sendResponse($pm, 'Building assigned successfully');
    }

    /**
     * @param BatchDeleteRequest $request
     * @return Response
     *
     * @SWG\Delete(
     *      path="/propertyManagers/batchDelete",
     *      summary="Remove batch PropertyManager from storage",
     *      tags={"PropertyManager"},
     *      description="Delete PropertyManager",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PropertyManager",
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
    public function batchDelete(BatchDeleteRequest $request)
    {
        $managerIds = $request->get('managerIds', []);
        $assignee = $request->get('assignee', 0);
        if (in_array($assignee, $managerIds)) {
            return $this->sendError('You cannot assign buildings to an deleted Property Manager');
        }

        $response = [
            'success' => 0,
            'fail' => 0
        ];

        $buildingIds = [];
        foreach ($managerIds as $managerId) {
            try {
                /** @var PropertyManager $propertyManager */
                $propertyManager = $this->propertyManagerRepository->find($managerId);
                if (empty($propertyManager)) {
                    return $this->sendError('Property Manager not found');
                }

                $assignedBuildingIds = $propertyManager->buildings()->pluck('buildings.id')->toArray();
                $buildingIds = array_merge($buildingIds, $assignedBuildingIds);

                $this->propertyManagerRepository->delete($managerId);
            } catch (\Exception $e) {
                $response['fail']++;
                continue;
            }
            $response['success']++;
        }

        if ($assignee) {
            $propertyManager = $this->propertyManagerRepository->find($assignee);
            if (empty($propertyManager)) {
                return $this->sendError('Property Manager not found');
            }
            $propertyManager->buildings()->sync($buildingIds, false);
        }

        return $this->sendResponse($response, 'Property Manager deleted successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     * @throws \Exception
     *
     * @SWG\Get(
     *      path="/requests/{id}/assignments",
     *      summary="Get a listing of the ServiceProvider assigned buildings and districts.",
     *      tags={"ServiceRequest"},
     *      description="Get a listing of the ServiceProvider assigned buildings and districts.",
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
    public function getAssignments(int $id, Request $request)
    {
        /** @var PropertyManager $propertyManager */
        $propertyManager = $this->propertyManagerRepository->find($id);
        if (empty($propertyManager)) {
            return $this->sendError('Property Manager not found');
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $assignments = $this->propertyManagerRepository->assignments($propertyManager)->paginate($perPage);
        return $this->sendResponse($assignments, 'Assignments retrieved successfully');
    }

    public function getIDsAssignmentsCount(Request $request)
    {
        /** @var PropertyManager $propertyManager */
        $propertyManagerArray = $this->propertyManagerRepository->find($request->post('ids'));
        if (empty($propertyManagerArray)) {
            return $this->sendError('Property Managers not found');
        }
        $assignments = $this->propertyManagerRepository->assignmentsWithIds($propertyManagerArray->pluck('id')->all())->count();
        return $this->sendResponse($assignments, 'Assignments retrieved successfully');
    }
}
