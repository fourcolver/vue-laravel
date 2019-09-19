<?php

namespace App\Http\Controllers\API;

use App\Criteria\Comments\FilterChildrenCriteria;
use App\Criteria\Comments\FilterChildrenOutCriteria;
use App\Criteria\Comments\FilterIdsOutCriteria;
use App\Criteria\Comments\FilterModelCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\Comment\CreateRequest;
use App\Http\Requests\API\Comment\DestroyRequest;
use App\Http\Requests\API\Comment\ListRequest;
use App\Http\Requests\API\Comment\UpdateRequest;
use App\Notifications\PostCommented;
use App\Notifications\ProductCommented;
use App\Repositories\CommentRepository;
use App\Repositories\PostRepository;
use App\Repositories\ProductRepository;
use App\Repositories\RealEstateRepository;
use App\Repositories\ServiceRequestRepository;
use App\Transformers\CommentTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;


/**
 * Class CommentController
 * @package App\Http\Controllers\API
 */
class CommentAPIController extends AppBaseController
{
    /** @var  CommentRepository */
    private $postRepository;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var ServiceRequestRepository
     */
    private $serviceRequestRepository;
    /**
     * @var CommentRepository
     */
    private $commentRepository;
    /**
     * @var RealEstateRepository
     */
    private $reRepository;
    /**
     * @var CommentTransformer
     */
    private $transformer;

    /**
     * CommentAPIController constructor.
     * @param PostRepository $postRepo
     * @param CommentRepository $commRepo
     * @param ProductRepository $prodRepo
     * @param ServiceRequestRepository $sr
     * @param RealEstateRepository $reRepo
     * @param CommentTransformer $pt
     */
    public function __construct(
        PostRepository $postRepo,
        CommentRepository $commRepo,
        ProductRepository $prodRepo,
        ServiceRequestRepository $sr,
        RealEstateRepository $reRepo,
        CommentTransformer $pt
    )
    {
        $this->postRepository = $postRepo;
        $this->productRepository = $prodRepo;
        $this->serviceRequestRepository = $sr;
        $this->commentRepository = $commRepo;
        $this->reRepository = $reRepo;
        $this->transformer = $pt;
    }

    /**
     * @param int $id
     * @param CreateRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/posts/{id}/comments",
     *      summary="Store a newly created comment in storage",
     *      tags={"Comment"},
     *      description="Store Comment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="comment",
     *          in="body",
     *          description="Comment that should be stored",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/Comment")
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
     *                  ref="#/definitions/Comment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function storePostComment(int $id, CreateRequest $request)
    {
        $post = $this->postRepository->findWithoutFail($id);
        if (empty($post)) {
            return $this->sendError('Post not found');
        }

        $comment = $post->comment($request->comment, $request->parent_id);
        $comment->load('user');


        // if logged in user is tenant and
        // author of post is tenant and
        // author of post is different than liker
        $u = \Auth::user();
        if ($u->tenant && $post->user->tenant && $u->id != $post->user_id) {
            $post->user->notify(new PostCommented($post, $u->tenant, $comment));
        }
        $out = $this->transformer->transform($comment);
        return $this->sendResponse($out, "Comment successfully created");
    }

    /**
     * @param int $id
     * @param CreateRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/requests/{id}/comments",
     *      summary="Store a newly created comment in storage",
     *      tags={"Comment"},
     *      description="Store Comment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="comment",
     *          in="body",
     *          description="Comment that should be stored",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/Comment")
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
     *                  ref="#/definitions/Comment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function storeRequestComment(int $id, CreateRequest $request)
    {
        $serviceRequest = $this->serviceRequestRepository->findWithoutFail($id);
        if (empty($serviceRequest)) {
            return $this->sendError('Service Request not found');
        }

        $comment = $serviceRequest->comment($request->comment, $request->parent_id);
        $comment->load('user');
        $out = $this->transformer->transform($comment);
        $this->serviceRequestRepository->notifyNewComment($serviceRequest, $comment);
        return $this->sendResponse($out, "Comment successfully created");
    }

    /**
     * @param int $id
     * @param CreateRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/products/{id}/comments",
     *      summary="Store a newly created comment in storage",
     *      tags={"Marketplace"},
     *      description="Store Comment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="comment",
     *          in="body",
     *          description="Comment that should be stored",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/Comment")
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
     *                  ref="#/definitions/Comment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function storeProductComment(int $id, CreateRequest $request)
    {
        $product = $this->productRepository->findWithoutFail($id);
        if (empty($product)) {
            return $this->sendError('Product not found');
        }

        $comment = $product->comment($request->comment, $request->parent_id);
        $comment->load('user');

        // if logged in user is tenant and
        // author of post is tenant and
        // author of post is different than liker
        $u = \Auth::user();
        if ($u->tenant && $product->user->tenant && $u->id != $product->user_id) {
            $product->user->notify(new ProductCommented($product, $u->tenant, $comment));
        }
        $out = $this->transformer->transform($comment);
        return $this->sendResponse($out, "Comment successfully created");
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     *
     * @SWG\Get(
     *      path="/comments",
     *      summary="Get a listing of the Comments.",
     *      tags={"Comment"},
     *      description="Get all Comments",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          in="query",
     *          description="The id of the commentable to retrieve comments for",
     *          required=true,
     *          type="integer"
     *      ),
     *      @SWG\Parameter(
     *          name="commentable",
     *          in="query",
     *          description="The type of the commentable to retrieve comments for",
     *          required=true,
     *          type="integer"
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
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/Comment")
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
        $this->commentRepository->pushCriteria(new RequestCriteria($request));
        $this->commentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->commentRepository->pushCriteria(new FilterChildrenOutCriteria());
        $this->commentRepository->pushCriteria(new FilterModelCriteria($request));
        $this->commentRepository->pushCriteria(new FilterIdsOutCriteria($request));

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $comments = $this->commentRepository->with([
            'user',
            'childrenCountRelation',
            // 'children' => function($q){$q->orderBy("created_at", "desc");},
            // 'children.user',
        ])->paginate($perPage);

        $out = $this->transformer->transformPaginator($comments);
        return $this->sendResponse($out, 'Comments retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     *
     * @SWG\Get(
     *      path="/comments/{id}",
     *      summary="Get a listing of the children comments.",
     *      tags={"Comment"},
     *      description="Get children comments",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          in="path",
     *          description="The id of the comment to retrieve children for",
     *          required=true,
     *          type="integer"
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
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/Comment")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function children($id, Request $request)
    {
        $this->commentRepository->pushCriteria(new RequestCriteria($request));
        $this->commentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->commentRepository->pushCriteria(new FilterChildrenCriteria($request));
        $this->commentRepository->pushCriteria(new FilterIdsOutCriteria($request));

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $comments = $this->commentRepository
            ->with([ 'user'])->paginate($perPage);

        $out = $this->transformer->transformPaginator($comments);
        return $this->sendResponse($out, 'Children comments retrieved successfully');
    }


    /**
     * @param int $id
     * @param UpdateRequest $request
     * @return Response
     * @throws \Exception
     *
     * @SWG\Put(
     *      path="/comments/{id}",
     *      summary="Update a created comment in storage",
     *      tags={"Comment"},
     *      description="Update Comment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="comment",
     *          in="body",
     *          description="Comment that should be updated",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/Comment")
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
     *                  ref="#/definitions/Comment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function updateComment(int $id, UpdateRequest $request)
    {
        $comment = $this->commentRepository->findWithoutFail($id);
        if (empty($comment)) {
            return $this->sendError('Comment not found');
        }

        $timeout = 120;
        if ($realEstate = $this->reRepository->first()) {
            $timeout = $realEstate->comment_update_timeout;
        }
        $isAdmin = $request->user()->hasRole('super_admin') ||
            $request->user()->hasRole('administrator');
        if (!$isAdmin && $comment->created_at->addMinutes($timeout)->lessThan(now())) {
            $err = "Comments can only be edited in the first %d minutes after being created.";
            return $this->sendError(sprintf($err, $timeout));
        }
        $input = ['comment' => $request->comment];
        $comment = $this->commentRepository->update($input, $id);
        $comment->load([
            'user',
            'childrenCountRelation',
        ]);

        $data = $this->transformer->transform($comment);
        return $this->sendResponse($data, 'Comment updated successfully');
    }

    /**
     * @param int $id
     * @param DeleteRequest $request
     * @return Response
     * @throws \Exception
     *
     * @SWG\Delete(
     *      path="/comments/{id}",
     *      summary="Detele a created comment in storage",
     *      tags={"Comment"},
     *      description="Delete Comment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="comment",
     *          in="body",
     *          description="Comment that should be deleted",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/Comment")
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
     *                  ref="#/definitions/Comment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroyComment(int $id, DestroyRequest $request)
    {
        $comment = $this->commentRepository->findWithoutFail($id);

        if (empty($comment)) {
            return $this->sendError('Comment not found');
        }

        return $this->sendResponse($this->commentRepository->destroy($comment), 'Comment deleted successfully');
    }
}
