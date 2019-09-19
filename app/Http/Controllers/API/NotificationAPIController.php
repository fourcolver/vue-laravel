<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Transformers\NotificationTransformer;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Criteria\Notifications\FilterByUserCriteria;

/**
 * Class NotificationController
 * @package App\Http\Controllers\API
 */

class NotificationAPIController extends AppBaseController
{
    private $transformer;

    public function __construct(NotificationTransformer $nTrans)
    {
        $this->transformer = $nTrans;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/notifications",
     *      summary="Get a listing of the notifications.",
     *      tags={"Notification"},
     *      description="Get all Notifications",
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
     *                  @SWG\Items()
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
        $notifications = $request->user()
                                 ->notifications()
                                 ->paginate($perPage);

        $out = $this->transformer->transformPaginator($notifications);
        return $this->sendResponse($out, 'Notifications retrieved successfully');
    }

    /**
     * @param MarkNotificationAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/notifications/{id}",
     *      summary="Mark a notification as read",
     *      tags={"Notification"},
     *      description="Update notification",
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
     *                  property="data"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function markAsRead($id, Request $request)
    {
        $request->user()->unreadNotifications()
            ->where('id', $id)
            ->get()->markAsRead();
        return $this->sendResponse($id, 'Notification marked successfully');
    }


    /**
     * @return Response
     *
     * @SWG\Post(
     *      path="/notifications",
     *      summary="Mark all notifications as read",
     *      tags={"Notification"},
     *      description="Update notifications",
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
     *                  property="data"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function markAllAsRead(Request $request)
    {
        $marked = $request->user()->unreadNotifications()
            ->get()->markAsRead();
        return $this->sendResponse($marked, 'Notifications marked successfully');
    }
}
