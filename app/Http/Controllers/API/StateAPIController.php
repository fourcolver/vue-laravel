<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Repositories\StateRepository;
use App\Transformers\StateTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class StateController
 * @package App\Http\Controllers\API
 */

class StateAPIController extends AppBaseController
{
    /** @var  StateRepository */
    private $stateRepository;

    /**
     * StateAPIController constructor.
     * @param StateRepository $stateRepo
     */
    public function __construct(StateRepository $stateRepo)
    {
        $this->stateRepository = $stateRepo;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws RepositoryException
     *
     * @SWG\Get(
     *      path="/states",
     *      summary="Get a listing of the States.",
     *      tags={"Location"},
     *      description="Get all States",
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
     *                  @SWG\Items(ref="#/definitions/State")
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
        $this->stateRepository->pushCriteria(new RequestCriteria($request));
        $this->stateRepository->pushCriteria(new LimitOffsetCriteria($request));

        $states = $this->stateRepository->get();

        $response = (new StateTransformer)->transformCollection($states);
        return $this->sendResponse($response, 'States retrieved successfully');
    }
}
