<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateInternalNoticeAPIRequest;
use App\Http\Requests\API\UpdateInternalNoticeAPIRequest;
use App\Models\InternalNotice;
use App\Repositories\InternalNoticeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class InternalNoticeController
 * @package App\Http\Controllers\API
 */

class InternalNoticeAPIController extends AppBaseController
{
    /** @var  InternalNoticeRepository */
    private $internalNoticeRepository;

    public function __construct(InternalNoticeRepository $internalNoticeRepo)
    {
        $this->internalNoticeRepository = $internalNoticeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/internalNotices",
     *      summary="Get a listing of the InternalNotices.",
     *      tags={"InternalNotice"},
     *      description="Get all InternalNotices",
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
     *                  @SWG\Items(ref="#/definitions/InternalNotice")
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
        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $internalNotices = $this->internalNoticeRepository->paginate($perPage);
        return $this->sendResponse($internalNotices->toArray(), 'Internal Notices retrieved successfully');
    }

    /**
     * @param CreateInternalNoticeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/internalNotices",
     *      summary="Store a newly created InternalNotice in storage",
     *      tags={"InternalNotice"},
     *      description="Store InternalNotice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="InternalNotice that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/InternalNotice")
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
     *                  ref="#/definitions/InternalNotice"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateInternalNoticeAPIRequest $request)
    {
        $input = $request->all();

        $internalNotice = $this->internalNoticeRepository->create($input);

        return $this->sendResponse($internalNotice->toArray(), 'Internal Notice saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/internalNotices/{id}",
     *      summary="Display the specified InternalNotice",
     *      tags={"InternalNotice"},
     *      description="Get InternalNotice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of InternalNotice",
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
     *                  ref="#/definitions/InternalNotice"
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
        /** @var InternalNotice $internalNotice */
        $internalNotice = $this->internalNoticeRepository->find($id);

        if (empty($internalNotice)) {
            return $this->sendError('Internal Notice not found');
        }

        return $this->sendResponse($internalNotice->toArray(), 'Internal Notice retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateInternalNoticeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/internalNotices/{id}",
     *      summary="Update the specified InternalNotice in storage",
     *      tags={"InternalNotice"},
     *      description="Update InternalNotice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of InternalNotice",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="InternalNotice that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/InternalNotice")
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
     *                  ref="#/definitions/InternalNotice"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateInternalNoticeAPIRequest $request)
    {
        $input = $request->all();

        /** @var InternalNotice $internalNotice */
        $internalNotice = $this->internalNoticeRepository->find($id);

        if (empty($internalNotice)) {
            return $this->sendError('Internal Notice not found');
        }

        $internalNotice = $this->internalNoticeRepository->update($input, $id);

        return $this->sendResponse($internalNotice->toArray(), 'InternalNotice updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/internalNotices/{id}",
     *      summary="Remove the specified InternalNotice from storage",
     *      tags={"InternalNotice"},
     *      description="Delete InternalNotice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of InternalNotice",
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
    public function destroy($id)
    {
        /** @var InternalNotice $internalNotice */
        $internalNotice = $this->internalNoticeRepository->find($id);

        if (empty($internalNotice)) {
            return $this->sendError('Internal Notice not found');
        }

        $internalNotice->delete();

        return $this->sendResponse($id, 'Internal Notice deleted successfully');
    }
}
