<?php

namespace App\Http\Controllers\API;

use App\Criteria\Common\RequestCriteria;
use App\Criteria\ServiceProviders\FilterByPostCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\ServiceProvider\AssignRequest;
use App\Http\Requests\API\ServiceProvider\CreateRequest;
use App\Http\Requests\API\ServiceProvider\DeleteRequest;
use App\Http\Requests\API\ServiceProvider\ShowRequest;
use App\Http\Requests\API\ServiceProvider\UpdateRequest;
use App\Models\ServiceProvider;
use App\Models\User;
use App\Repositories\AddressRepository;
use App\Repositories\DistrictRepository;
use App\Repositories\BuildingRepository;
use App\Repositories\ServiceProviderRepository;
use App\Repositories\UserRepository;
use App\Transformers\ServiceProviderTransformer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Validator;

/**
 * Class ServiceProviderController
 * @package App\Http\Controllers\API
 */
class ServiceProviderAPIController extends AppBaseController
{
    /** @var  ServiceProviderRepository */
    private $serviceProviderRepository;
    private $addressRepository;
    private $userRepository;

    public function __construct(ServiceProviderRepository $serviceProviderRepo, AddressRepository $addressRepo, UserRepository $userRepo)
    {
        $this->serviceProviderRepository = $serviceProviderRepo;
        $this->addressRepository = $addressRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws /Exception
     *
     * @SWG\Get(
     *      path="/service",
     *      summary="Get a listing of the ServiceProviders.",
     *      tags={"ServiceProvider"},
     *      description="Get all ServiceProviders",
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
     *                  @SWG\Items(ref="#/definitions/ServiceProvider")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->serviceProviderRepository->pushCriteria(new RequestCriteria($request));
        $this->serviceProviderRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->serviceProviderRepository->pushCriteria(new FilterByPostCriteria($request));

        $getAll = $request->get('get_all', false);
        if ($getAll) {
            $request->merge(['limit' => env('APP_PAGINATE', 10)]);
            $this->serviceProviderRepository->pushCriteria(new LimitOffsetCriteria($request));
            $serviceProviders = $this->serviceProviderRepository->with([
                'user',
            ])->get();
            return $this->sendResponse($serviceProviders->toArray(), 'Service Providers successfully');
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $query = $this->serviceProviderRepository->with([
            'user', 'address'
        ]);

        $reqCount = $request->get('req_count');
        if ($reqCount) {
            $query = $query->withCount([
                'requestsReceived',
                'requestsInProcessing',
                'requestsAssigned',
                'requestsDone',
                'requestsReactivated',
                'requestsArchived',
            ]);
        }
        $serviceProviders = $query->paginate($perPage);

        return $this->sendResponse($serviceProviders->toArray(), 'Service Providers retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     *
     * @SWG\Get(
     *      path="/service/category",
     *      summary="Get a listing of the ServiceProviders by category.",
     *      tags={"ServiceProvider"},
     *      description="Get all ServiceProviders  by category",
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
     *                  @SWG\Items(ref="#/definitions/ServiceProvider")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function indexByCategory(Request $request)
    {
        $this->serviceProviderRepository->pushCriteria(new RequestCriteria($request));
        $this->serviceProviderRepository->pushCriteria(new LimitOffsetCriteria($request));

        $serviceProviders = $this->serviceProviderRepository->get();

        $response = (new ServiceProviderTransformer)->transformByCategoryCollection($serviceProviders);

        return $this->sendResponse($response, 'Service Providers retrieved successfully');
    }

    /**
     * @param CreateRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/service",
     *      summary="Store a newly created ServiceProvider in storage",
     *      tags={"ServiceProvider"},
     *      description="Store ServiceProvider",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ServiceProvider that should be stored",
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
    public function store(CreateRequest $request)
    {
        $input = $request->all();

        if (!isset($input['user']['email'])) {
            $input['user']['email'] = $input['email'];
        }

        if (!isset($input['user']['name'])) {
            $input['user']['name'] = $input['name'];
        }

        if (!isset($input['user']['phone'])) {
            $input['user']['phone'] = $input['phone'];
        }
        $input['user']['role'] = 'service';

        $validator = Validator::make($input['user'], User::$rules);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        try {
            $user = $this->userRepository->create($input['user']);
        } catch (Exception $e) {
            return $this->sendError('Service Provider create error: ' . $e->getMessage());
        }
        $input['user_id'] = $user->id;

        try {
            $address = $this->addressRepository->create($input['address']);
        } catch (Exception $e) {
            return $this->sendError('Service Provider create error: ' . $e->getMessage());
        }
        $input['address_id'] = $address->id;

        try {
            $serviceProvider = $this->serviceProviderRepository->create($input);
        } catch (Exception $e) {
            return $this->sendError('Service Provider create error: ' . $e->getMessage());
        }

        $serviceProvider->load(['user', 'address']);
        $response = (new ServiceProviderTransformer)->transform($serviceProvider);

        return $this->sendResponse($response, 'Service Provider saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/service/{id}",
     *      summary="Display the specified ServiceProvider",
     *      tags={"ServiceProvider"},
     *      description="Get ServiceProvider",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ServiceProvider",
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
    public function show($id, ShowRequest $request)
    {
        /** @var ServiceProvider $serviceProvider */
        $serviceProvider = $this->serviceProviderRepository->findWithoutFail($id);
        if (empty($serviceProvider)) {
            return $this->sendError('Service Provider not found');
        }

        $serviceProvider->load(['user', 'address']);
        $response = (new ServiceProviderTransformer)->transform($serviceProvider);

        return $this->sendResponse($response, 'Service Provider retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/service/{id}",
     *      summary="Update the specified ServiceProvider in storage",
     *      tags={"ServiceProvider"},
     *      description="Update ServiceProvider",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ServiceProvider",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ServiceProvider that should be updated",
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
    public function update($id, UpdateRequest $request)
    {
        $input = $request->all();
        /** @var ServiceProvider $serviceProvider */
        $serviceProvider = $this->serviceProviderRepository->findWithoutFail($id);
        if (empty($serviceProvider)) {
            return $this->sendError('Service Provider not found');
        }

        try {
            $serviceProvider = $this->serviceProviderRepository->update($input, $id);
        } catch (Exception $e) {
            return $this->sendError('Service Provider updated error: ' . $e->getMessage());
        }

        if (isset($input['user'])) {
            $input['user']['email'] = $input['email'];
            $input['user']['name'] = $input['name'];

            $validator = Validator::make($input['user'], User::$rulesUpdate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }
            try {
                $this->userRepository->update($input['user'], $serviceProvider->user_id);
            } catch (Exception $e) {
                return $this->sendError('Service Provider updated error: ' . $e->getMessage());
            }
        }

        if (isset($input['address'])) {
            try {
                $this->addressRepository->update($input['address'], $serviceProvider->address_id);
            } catch (Exception $e) {
                return $this->sendError('Service Provider updated error: ' . $e->getMessage());
            }
        }

        $serviceProvider->load(['user', 'address']);
        $response = (new ServiceProviderTransformer)->transform($serviceProvider);

        return $this->sendResponse($response, 'ServiceProvider updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/service/{id}",
     *      summary="Remove the specified ServiceProvider from storage",
     *      tags={"ServiceProvider"},
     *      description="Delete ServiceProvider",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ServiceProvider",
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
    public function destroy($id, DeleteRequest $request)
    {
        /** @var ServiceProvider $serviceProvider */
        $serviceProvider = $this->serviceProviderRepository->findWithoutFail($id);
        if (empty($serviceProvider)) {
            return $this->sendError('Service Provider not found');
        }

        try {
            if ($serviceProvider->user()->exists()) {
                $serviceProvider->user->delete();
            }
            if ($serviceProvider->address()->exists()) {
                $serviceProvider->address->delete();
            }
            $serviceProvider->delete();
        } catch (Exception $e) {
            return $this->sendError('Service Provider deleted error: ' . $e->getMessage());
        }

        return $this->sendResponse($id, 'Service Provider deleted successfully');
    }

    /**
     * @param int $id
     * @param int $did
     * @param AssignRequest $r
     * @param DistrictRepository $dRepo
     * @return Response
     *
     * @SWG\Post(
     *      path="/services/{id}/districts/{did}",
     *      summary="Assign the provided district to the service provider",
     *      tags={"ServiceProvider"},
     *      description="Assign the provided district to the service provider",
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
    public function assignDistrict(int $id, int $did,
                                   DistrictRepository $dRepo, AssignRequest $r)
    {
        $sp = $this->serviceProviderRepository->findWithoutFail($id);
        if (empty($sp)) {
            return $this->sendError('Service provider not found');
        }
        $d = $dRepo->findWithoutFail($did);
        if (empty($d)) {
            return $this->sendError('District not found');
        }

        $sp->districts()->sync($d, false);
        $sp->load('user', 'address', 'districts', 'buildings');
        $ret = (new ServiceProviderTransformer)->transform($sp);

        return $this->sendResponse($ret, 'District assigned successfully');
    }

    /**
     * @param int $id
     * @param int $did
     * @param AssignRequest $request
     * @param DistrictRepository $dRepo
     * @return Response
     *
     * @SWG\Delete(
     *      path="/services/{id}/districts/{did}",
     *      summary="Unassign the provided district from the service provider",
     *      tags={"ServiceProvider"},
     *      description="Unassign the provided district from the service provider",
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
    public function unassignDistrict(int $id, int $did,
                                     DistrictRepository $dRepo, AssignRequest $r)
    {
        $sp = $this->serviceProviderRepository->findWithoutFail($id);
        if (empty($sp)) {
            return $this->sendError('Service provider not found');
        }
        $d = $dRepo->findWithoutFail($did);
        if (empty($d)) {
            return $this->sendError('District not found');
        }

        $sp->districts()->detach($d);
        $sp->load('user', 'address', 'districts', 'buildings');
        $ret = (new ServiceProviderTransformer)->transform($sp);

        return $this->sendResponse($ret, 'District unassigned successfully');
    }

    /**
     * @param int $id
     * @param int $bid
     * @param AssignRequest $r
     * @param BuildingRepository $bRepo
     * @return Response
     *
     * @SWG\Post(
     *      path="/services/{id}/buildings/{bid}",
     *      summary="Assign the provided building to the service provider",
     *      tags={"ServiceProvider"},
     *      description="Assign the provided building to the service provider",
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
    public function assignBuilding(int $id, int $bid,
                                   BuildingRepository $bRepo, AssignRequest $r)
    {
        $sp = $this->serviceProviderRepository->findWithoutFail($id);
        if (empty($sp)) {
            return $this->sendError('Service provider not found');
        }
        $b = $bRepo->findWithoutFail($bid);
        if (empty($b)) {
            return $this->sendError('Building not found');
        }
        if ($b->district) {
            if ($sp->districts->contains($b->district)) {
                return $this->sendError('Building already assigned through district');
            }
        }

        $sp->buildings()->sync($b, false);
        $sp->load('user', 'address', 'districts', 'buildings');
        $ret = (new ServiceProviderTransformer)->transform($sp);

        return $this->sendResponse($ret, 'Building assigned successfully');
    }

    /**
     * @param int $id
     * @param int $bid
     * @param AssignRequest $request
     * @param BuildingRepository $bRepo
     * @return Response
     *
     * @SWG\Delete(
     *      path="/services/{id}/buildings/{bid}",
     *      summary="Unassign the provided building from the service provider",
     *      tags={"ServiceProvider"},
     *      description="Unassign the provided building from the service provider",
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
    public function unassignBuilding(int $id, int $bid,
                                     BuildingRepository $bRepo, AssignRequest $r)
    {
        $sp = $this->serviceProviderRepository->findWithoutFail($id);
        if (empty($sp)) {
            return $this->sendError('Service provider not found');
        }
        $b = $bRepo->findWithoutFail($bid);
        if (empty($b)) {
            return $this->sendError('Building not found');
        }

        $sp->buildings()->detach($b);
        $sp->load('user', 'address', 'districts', 'buildings');
        $ret = (new ServiceProviderTransformer)->transform($sp);

        return $this->sendResponse($ret, 'Building unassigned successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     * @throws \Exception
     *
     * @SWG\Get(
     *      path="/services/{id}/locations",
     *      summary="Get a listing of the service provider locations.",
     *      tags={"ServiceProvider"},
     *      description="Get a listing of the service provider locations.",
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
     *                  @SWG\Items(ref="#/definitions/ServiceProvider")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function getLocations(int $id, Request $request)
    {
        $sp = $this->serviceProviderRepository->findWithoutFail($id);
        if (empty($sp)) {
            return $this->sendError('Service provider not found');
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $locations = $this->serviceProviderRepository->locations($sp)->paginate($perPage);
        return $this->sendResponse($locations, 'Locations retrieved successfully');
    }
}
