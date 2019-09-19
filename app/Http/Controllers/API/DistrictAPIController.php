<?php

namespace App\Http\Controllers\API;

use App\Criteria\Districts\ExcludeIdsCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\District\CreateRequest;
use App\Http\Requests\API\District\UpdateRequest;
use App\Http\Requests\API\District\ListRequest;
use App\Http\Requests\API\District\ViewRequest;
use App\Http\Requests\API\District\DeleteRequest;
use App\Models\District;
use App\Repositories\DistrictRepository;
use App\Transformers\DistrictTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class DistrictController
 * @package App\Http\Controllers\API
 */
class DistrictAPIController extends AppBaseController
{
    /** @var  DistrictRepository */
    private $districtRepository;

    public function __construct(DistrictRepository $districtRepo)
    {
        $this->districtRepository = $districtRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/districts",
     *      summary="Get a listing of the Districts.",
     *      tags={"District"},
     *      description="Get all Districts",
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
     *                  @SWG\Items(ref="#/definitions/District")
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
        $this->districtRepository->pushCriteria(new RequestCriteria($request));
        $this->districtRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->districtRepository->pushCriteria(new ExcludeIdsCriteria($request));

        $getAll = $request->get('get_all', false);
        if ($getAll) {
            $districts = $this->districtRepository->get();
            $response = (new DistrictTransformer)->transformCollection($districts);
            return $this->sendResponse($response, 'Districts retrieved successfully');
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $districts = $this->districtRepository->paginate($perPage);

        $response = (new DistrictTransformer)->transformPaginator($districts);
        return $this->sendResponse($response, 'Districts retrieved successfully');
    }

    /**
     * @param CreateRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/districts",
     *      summary="Store a newly created District in storage",
     *      tags={"District"},
     *      description="Store District",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="District that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/District")
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
     *                  ref="#/definitions/District"
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
        $district = $this->districtRepository->create($input);
        $response = (new DistrictTransformer)->transform($district);

        return $this->sendResponse($response, 'District saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/districts/{id}",
     *      summary="Display the specified District",
     *      tags={"District"},
     *      description="Get District",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of District",
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
     *                  ref="#/definitions/District"
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
        /** @var District $district */
        $district = $this->districtRepository->findWithoutFail($id);
        if (empty($district)) {
            return $this->sendError('District not found');
        }

        $response = (new DistrictTransformer)->transform($district);

        return $this->sendResponse($response, 'District retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/districts/{id}",
     *      summary="Update the specified District in storage",
     *      tags={"District"},
     *      description="Update District",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of District",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="District that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/District")
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
     *                  ref="#/definitions/District"
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

        /** @var District $district */
        $district = $this->districtRepository->findWithoutFail($id);

        if (empty($district)) {
            return $this->sendError('District not found');
        }

        $district = $this->districtRepository->update($input, $id);

        $response = (new DistrictTransformer)->transform($district);
        return $this->sendResponse($response, 'District updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/districts/{id}",
     *      summary="Remove the specified District from storage",
     *      tags={"District"},
     *      description="Delete District",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of District",
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
        /** @var District $district */
        $district = $this->districtRepository->findWithoutFail($id);

        if (empty($district)) {
            return $this->sendError('District not found');
        }

        $district->delete();

        return $this->sendResponse($id, 'District deleted successfully');
    }
}
