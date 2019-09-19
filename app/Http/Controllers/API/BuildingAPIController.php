<?php

namespace App\Http\Controllers\API;

use App\Criteria\Buildings\FilterByRelatedFieldsCriteria;
use App\Criteria\Buildings\ExcludeIdsCriteria;
use App\Criteria\Common\RequestCriteria;
use App\Criteria\Posts\FilterByBuildingCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\Building\BatchAssignManagers;
use App\Http\Requests\API\Building\CreateRequest;
use App\Http\Requests\API\Building\DeleteRequest;
use App\Http\Requests\API\Building\ListRequest;
use App\Http\Requests\API\Building\UpdateRequest;
use App\Http\Requests\API\Building\ViewRequest;
use App\Http\Requests\API\PropertyManager\AssignRequest;
use App\Models\Address;
use App\Models\Building;
use App\Repositories\AddressRepository;
use App\Repositories\BuildingRepository;
use App\Repositories\PropertyManagerRepository;
use App\Repositories\UserRepository;
use App\Repositories\UnitRepository;
use App\Repositories\ServiceRequestRepository;
use App\Transformers\BuildingTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Spatie\Geocoder\Geocoder;
use Validator;

/**
 * Class BuildingController
 * @package App\Http\Controllers\API
 */
class BuildingAPIController extends AppBaseController
{
    /** @var  BuildingRepository */
    private $buildingRepository;

    /** @var  AddressRepository */
    private $addressRepository;

    /** @var  UserRepository */
    private $userRepository;

    /** @var  propertyManagerRepository */
    private $propertyManagerRepository;

    /** @var  UnitRepository */
    private $unitRepository;

    /** @var  ServiceRequestRepository */
    private $serviceRequestRepository;

    /**
     * BuildingAPIController constructor.
     * @param BuildingRepository $buildingRepo
     * @param AddressRepository $addrRepo
     * @param UserRepository $ur
     * @param PropertyManagerRepository $pm
     * @param UnitRepository $un
     * @param ServiceRequestRepository $sr
     */
    public function __construct(
        BuildingRepository $buildingRepo,
        AddressRepository $addrRepo,
        UserRepository $ur,
        PropertyManagerRepository $pm,
        UnitRepository $un,
        ServiceRequestRepository $sr
    )
    {
        $this->buildingRepository = $buildingRepo;
        $this->addressRepository = $addrRepo;
        $this->userRepository = $ur;
        $this->propertyManagerRepository = $pm;
        $this->unitRepository = $un;
        $this->serviceRequestRepository = $sr;
    }

    /**
     * @param ListRequest $request
     * @return Response
     * @throws /Exception
     *
     * @SWG\Get(
     *      path="/buildings",
     *      summary="Get a listing of the Buildings.",
     *      tags={"Building"},
     *      description="Get all Buildings",
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
     *                  @SWG\Items(ref="#/definitions/Building")
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
        $this->buildingRepository->pushCriteria(new RequestCriteria($request));
        $this->buildingRepository->pushCriteria(new FilterByRelatedFieldsCriteria($request));
        $this->buildingRepository->pushCriteria(new ExcludeIdsCriteria($request));
        $this->buildingRepository->pushCriteria(new LimitOffsetCriteria($request));

        $getAll = $request->get('get_all', false);
        if ($getAll) {
            $buildings = $this->buildingRepository->with('address.state')->get();
            return $this->sendResponse($buildings->toArray(), 'Buildings retrieved successfully');
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $buildings = $this->buildingRepository->with(
            [
                'address.state',
                'serviceProviders',
                'tenants.user',
                'propertyManagers',
            ])->withCount(
            [
                'units',
                'propertyManagers',
                'tenants',
                'requests',
                'requestsReceived',
                'requestsInProcessing',
                'requestsAssigned',
                'requestsDone',
                'requestsReactivated',
                'requestsArchived',
            ]
        )->paginate($perPage);

        $response = (new BuildingTransformer)->transformPaginator($buildings);
        return $this->sendResponse($response, 'Buildings retrieved successfully');
    }

    /**
     * @param ListRequest $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function latest(ListRequest $request)
    {
        $limit = $request->get('limit', 5);
        $model = $this->buildingRepository->getModel();
        $buildings = $model->select(['id', 'name'])->limit($limit)->withCount([
            'units',
            'tenants',
        ])->get();
        return $this->sendResponse($buildings->toArray(), 'Buildings retrieved successfully');
    }

    /**
     * @param ListRequest $request
     * @return mixed
     */
    public function map(ListRequest $request)
    {
        $model = $this->buildingRepository->getModel();
        $columns = [
            'id',
            'address_id',
            'name',
            'latitude',
            'longitude'
        ];

        $buildings = $model->select($columns)->with(
            [
                'address' => function ($q) {
                    $q->select('id', 'country_id', 'state_id', 'city', 'street', 'street_nr', 'zip')
                        ->with(['state', 'country']);
                },
            ])->withCount(
            [
                'units',
                'propertyManagers',
                'tenants',
                'requests',
                'requestsReceived',
                'requestsInProcessing',
                'requestsAssigned',
                'requestsDone',
                'requestsReactivated',
                'requestsArchived',
            ]
        )->get();
        return $this->sendResponse($buildings->toArray(), 'Buildings retrieved successfully');
    }

    /**
     * @param CreateRequest $request
     * @return Response
     * @throws /Exception
     *
     * @SWG\Post(
     *      path="/buildings",
     *      summary="Store a newly created Building in storage",
     *      tags={"Building"},
     *      description="Store Building",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Building that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Building")
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
     *                  ref="#/definitions/Building"
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
        $input = $request->only((new Building)->getFillable());
        $input['service_providers'] = $request->get('service_providers');

        $addressInput = $request->get('address');
        $validator = Validator::make($addressInput, Address::$rules);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $address = $this->addressRepository->create($addressInput);

        $input['address_id'] = $address->id;
        $input['name'] = sprintf('%s %s', $address->street, $address->street_nr);

        // @TODO pass correct argument
//        $geoData = $this->getGeoDataByAddress($input['name']);
//        $input = array_merge($input, $geoData);
        $building = $this->buildingRepository->create($input);
        $response = (new BuildingTransformer)->transform($building);

        return $this->sendResponse($response, 'Building saved successfully');
    }

    /**
     * @param int $id
     * @param ViewRequest $r
     * @return Response
     *
     * @SWG\Get(
     *      path="/buildings/{id}",
     *      summary="Display the specified Building",
     *      tags={"Building"},
     *      description="Get Building",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Building",
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
     *                  ref="#/definitions/Building"
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
        /** @var Building $building */
        $building = $this->buildingRepository->findWithoutFail($id);
        if (empty($building)) {
            return $this->sendError('Building not found');
        }

        $building->load('address.state', 'serviceProviders', 'tenants.user',
            'propertyManagers', 'media', 'district');

        $response = (new BuildingTransformer)->transform($building);
        return $this->sendResponse($response, 'Building retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateRequest $request
     * @return Response
     * @throws /Exception
     *
     * @SWG\Put(
     *      path="/buildings/{id}",
     *      summary="Update the specified Building in storage",
     *      tags={"Building"},
     *      description="Update Building",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Building",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Building that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Building")
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
     *                  ref="#/definitions/Building"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update(int $id, UpdateRequest $request)
    {
        /** @var Building $building */
        $building = $this->buildingRepository->findWithoutFail($id);
        if (empty($building)) {
            return $this->sendError('Building not found');
        }

        $input = $request->only((new Building)->getFillable());
        $input['service_providers'] = $request->get('service_providers');

        $addressInput = $request->get('address');
        if ($addressInput) {
            $validator = Validator::make($addressInput, Address::$rules);
            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }
            $address = $this->addressRepository->update($addressInput, $building->address_id);
            $input['address_id'] = $address->id;
        }

        $building = $this->buildingRepository->update($input, $id);

        $building->load(['address.state', 'media', 'serviceProviders']);
        $response = (new BuildingTransformer)->transform($building);
        return $this->sendResponse($response, 'Building updated successfully');
    }

    /**
     * @param int $id
     * @param DeleteRequest $r
     * @return Response
     *
     * @SWG\Delete(
     *      path="/buildings/{id}",
     *      summary="Remove the specified Building from storage",
     *      tags={"Building"},
     *      description="Delete Building",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Building",
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
        /** @var Building $building */
        $building = $this->buildingRepository->findWithoutFail($id);
        if (empty($building)) {
            return $this->sendError('Building not found');
        }

        try {
            $this->buildingRepository->delete($building->id);
        } catch (\Exception $e) {
            return $this->sendError('Building deleted error: ' . $e->getMessage());
        }

        return $this->sendResponse($id, 'Building deleted successfully');
    }

    public function destroyWithIds(Request $request)
    {
        /** @var Building $building */        
        $buildings = $this->buildingRepository->findWithoutFail($request->get('ids'));
        if (empty($buildings)) {
            return $this->sendError('Building not found');
        }        
        
        try {
            foreach($buildings as $building) {
                $this->buildingRepository->delete($building->id);
            }
            $units = $this->unitRepository->getUnitsIdwithBuildingIds($buildings->pluck('id'));

            if($request->get('is_requests')) {
                $this->serviceRequestRepository->deleteRequesetWithUnitIds($units->pluck('id'));
            }            

            if($request->get('is_units')) {                
                $this->unitRepository->deleteUnitWithBuilding($buildings->pluck('id'));                
            }            

            return $this->sendResponse('', 'Building deleted successfully');

        } catch (\Exception $e) {
            return $this->sendError('Building deleted error: ' . $e->getMessage());
        }
    }

    public function checkUnitRequest(Request $request)
    {
        $buildings = $this->buildingRepository->findWithoutFail($request->get('ids'));
        if (empty($buildings)) {
            return $this->sendError('Building not found');
        }

        try {            
            $units = $this->unitRepository->getUnitsIdwithBuildingIds($buildings->pluck('id'));
            $returnValue = [
                'isUnitExist' => false,
                'isRequestExist' => false,
            ];
            
            if(count($units) > 0) {
                $request['isUnitExist'] = true;
            }

            if($this->serviceRequestRepository->getRequestCountWithUnitIds($units->pluck('id')) > 0){
                $request['isRequestExist'] = true;
            }

            $returnValue = -1;
            if($request['isUnitExist'] && $request['isRequestExist'])
                $returnValue = 2;
            else if($request['isUnitExist'] && !$request['isRequestExist'])
                $returnValue = 0;
            else if(!$request['isUnitExist'] && $request['isRequestExist'])
                $returnValue = 1;

            return $this->sendResponse($returnValue, 'Building deleted successfully');

        } catch (\Exception $e) {
            return $this->sendError('Building deleted error: ' . $e->getMessage());
        }
    }

    /**
     * @param int $id
     * @param int $service_id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/buildings/{post_id}/service/{service_id}",
     *      summary="Remove the specified Service from storage",
     *      tags={"Building"},
     *      description="Delete Service",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Service",
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
    public function serviceRemove(int $id, int $service_id)
    {
        /** @var Building $building */
        $building = $this->buildingRepository->findWithoutFail($id);
        if (empty($building)) {
            return $this->sendError('Building not found');
        }

        try {
            $building->serviceProviders()->detach($service_id);
        } catch (\Exception $e) {
            return $this->sendError('ServiceProvider deleted error: ' . $e->getMessage());
        }

        return $this->sendResponse($id, 'Service Provider deleted successfully');
    }

    /**
     * @param int $id
     * @param BatchAssignManagers $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/buildings/{id}/propertyManagers",
     *      summary="Assign the provided propertyManagers to the Building",
     *      tags={"Building"},
     *      description="Assign the provided propertyManagers to the Building",
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
     *                  ref="#/definitions/Building"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function assignManagers(int $id, BatchAssignManagers $request)
    {
        /** @var Building $building */
        $building = $this->buildingRepository->findWithoutFail($id);
        if (empty($building)) {
            return $this->sendError('Building not found');
        }

        $managersIds = $request->get('managersIds');
        try {
            $currentManagers = $building->propertyManagers()->pluck('property_managers.id')->toArray();
            $newManagers = array_diff($managersIds, $currentManagers);

            $building->propertyManagers()->attach($newManagers);
        } catch (\Exception $e) {
            return $this->sendError('Property Managers assign to Building error: ' . $e->getMessage());
        }

        $building->load(['address.state', 'media', 'serviceProviders', 'propertyManagers']);
        $response = (new BuildingTransformer)->transform($building);
        return $this->sendResponse($response, 'Property Managers assigned successfully');
    }

    /**
     * @param int $building_id
     * @param int $manager_id
     * @param BatchAssignManagers $r
     * @return Response
     *
     * @SWG\Delete(
     *      path="/buildings/{building_id}/propertyManagers/{manager_id}",
     *      summary="Unassign the provided property managerfrom the building ",
     *      tags={"Building"},
     *      description="Unassign the provided property manager from the building",
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
     *                  ref="#/definitions/Building"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function unAssignPropertyManager(int $building_id, int $manager_id, AssignRequest $r)
    {
        $building = $this->buildingRepository->findWithoutFail($building_id);
        if (empty($building)) {
            return $this->sendError('Building not found');
        }

        $propertyManager = $this->propertyManagerRepository->findWithoutFail($manager_id);
        if (empty($propertyManager)) {
            return $this->sendError('Property manager not found');
        }

        $building->propertyManagers()->detach($propertyManager);

        $building->load(['address.state', 'media', 'serviceProviders', 'propertyManagers']);
        $response = (new BuildingTransformer)->transform($building);
        return $this->sendResponse($response, 'Property unassigned successfully');
    }

    /**
     * @param $address
     * @return array
     */
    protected function getGeoDataByAddress($address)
    {
        $client = new \GuzzleHttp\Client();

        $geocoder = new Geocoder($client);
        $geocoder->setApiKey(config('geocoder.key'));

//        $geocoder->setCountry(config('US'));
        try {
            // @TODO remove after test
            $address = 'Infinite Loop 1, Cupertino';
            $response = $geocoder->getCoordinatesForAddress($address);
        } catch (\Exception $exception) {
            $response = [
                'lat' => 0,
                'lng' => 0,
            ];
        }
        return [
            'longitude' => $response['lng'],
            'latitude' => $response['lat']
        ];
    }
}
