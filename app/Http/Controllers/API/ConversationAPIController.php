<?php

namespace App\Http\Controllers\API;

use App\Criteria\Common\RequestCriteria;
use App\Criteria\Conversations\FilterModelCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\Conversation\CommentRequest;
use App\Models\Conversation;
use App\Models\User;
use App\Repositories\ConversationRepository;
use App\Transformers\ConversationTransformer;
use App\Transformers\CommentTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;

/**
 * Class ConversationAPIController
 * @package App\Http\Controllers\API
 */
class ConversationAPIController extends AppBaseController
{
    private $repo;
    private $convTransf;
    private $commTransf;

    public function __construct(
        ConversationRepository $repo,
        ConversationTransformer $convTransf,
        CommentTransformer $commTransf)
    {
        $this->repo = $repo;
        $this->convTransf = $convTransf;
        $this->commTransf = $commTransf;
    }

    /**
     * @return Response
     *
     * @SWG\Get(
     *      path="/conversations",
     *      summary="Display conversations",
     *      tags={"Conversation"},
     *      description="Get conversations",
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
     *                  ref="#/definitions/Conversation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show(Request $request)
    {
        $this->repo->pushCriteria(new RequestCriteria($request));
        $this->repo->pushCriteria(new LimitOffsetCriteria($request));
        $this->repo->pushCriteria(new FilterModelCriteria($request));

        $getAll = $request->get('get_all', false);
        if ($getAll) {
            $cs = $this->repo->with('users')->get();
            $out = $this->convTransf->transformCollection($cs);
        } else {
            $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
            $cs = $this->repo->with('users')->paginate($perPage);
            $out = $this->convTransf->transformPaginator($cs);
        }

        return $this->sendResponse($out, 'Service Request conversations retrieved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Post(
     *      path="/conversations{id}",
     *      summary="Store a conversation message",
     *      tags={"Conversation"},
     *      description="Store a conversation message",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Conversation",
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
     *                  ref="#/definitions/Conversation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function storeComment($id, CommentRequest $r)
    {
        $c = Conversation::ofLoggedInUser()->find($id);
        if (empty($c)) {
            return $this->sendError('Conversation not found');
        }
        $comment = $c->comment($r->comment, null)->load('user');
        $c->notifyComment($comment);
        $out = $this->commTransf->transform($comment);
        return $this->sendResponse($out, 'Conversation comment created successfully');
    }
}
