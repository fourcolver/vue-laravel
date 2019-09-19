<?php

namespace App\Http\Controllers\API;

use App\Criteria\Posts\FeedCriteria;
use App\Criteria\Posts\FilterByBuildingCriteria;
use App\Criteria\Posts\FilterByDistrictCriteria;
use App\Criteria\Posts\FilterByLocationCriteria;
use App\Criteria\Posts\FilterByPinnedCriteria;
use App\Criteria\Posts\FilterByStatusCriteria;
use App\Criteria\Posts\FilterByTypeCriteria;
use App\Criteria\Posts\FilterByUserCriteria;
use App\Criteria\Posts\FilterByTenantCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\Post\AssignRequest;
use App\Http\Requests\API\Post\CreateRequest;
use App\Http\Requests\API\Post\DeleteRequest;
use App\Http\Requests\API\Post\PublishRequest;
use App\Http\Requests\API\Post\ShowRequest;
use App\Http\Requests\API\Post\UpdateRequest;
use App\Http\Requests\API\Post\ListViewsRequest;
use App\Models\Post;
use App\Models\User;
use App\Notifications\NewTenantPost;
use App\Notifications\PostLiked;
use App\Repositories\BuildingRepository;
use App\Repositories\DistrictRepository;
use App\Repositories\PostRepository;
use App\Repositories\RealEstateRepository;
use App\Repositories\ServiceProviderRepository;
use App\Repositories\TemplateRepository;
use App\Transformers\PostTransformer;
use App\Transformers\PostViewTransformer;
use App\Transformers\UserTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Notification;

/**
 * Class PostController
 * @package App\Http\Controllers\API
 */
class PostAPIController extends AppBaseController
{
    /** @var  PostRepository */
    private $postRepository;
    /**
     * @var PostTransformer
     */
    private $transformer;
    /**
     * @var UserTransformer
     */
    private $uTransformer;

    /**
     * PostAPIController constructor.
     * @param PostRepository $postRepo
     * @param PostTransformer $pt
     * @param UserTransformer $ut
     */
    public function __construct(PostRepository $postRepo, PostTransformer $pt, UserTransformer $ut)
    {
        $this->postRepository = $postRepo;
        $this->transformer = $pt;
        $this->uTransformer = $ut;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws /Exception
     *
     * @SWG\Get(
     *      path="/posts",
     *      summary="Get a listing of the Posts.",
     *      tags={"Listing"},
     *      description="Get all Posts",
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
     *                  @SWG\Items(ref="#/definitions/Post")
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
        $this->postRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->postRepository->pushCriteria(new FeedCriteria($request));
        $this->postRepository->pushCriteria(new FilterByStatusCriteria($request));
        $this->postRepository->pushCriteria(new FilterByTypeCriteria($request));
        $this->postRepository->pushCriteria(new FilterByLocationCriteria($request));
        $this->postRepository->pushCriteria(new FilterByUserCriteria($request));
        $this->postRepository->pushCriteria(new FilterByDistrictCriteria($request));
        $this->postRepository->pushCriteria(new FilterByBuildingCriteria($request));
        $this->postRepository->pushCriteria(new FilterByPinnedCriteria($request));
        $this->postRepository->pushCriteria(new FilterByTenantCriteria($request));

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $posts = $this->postRepository->with([
            'media',
            'user.tenant',
            'likesCounter',
            'likes' => function($q){$q->take(3);},
            'likes.user',
            'buildings.address.state',
            'buildings.serviceProviders',
            'buildings.media',
            'providers',
        ])->paginate($perPage);
        $posts->getCollection()->loadCount('allComments');


        $out = $this->transformer->transformPaginator($posts);
        return $this->sendResponse($out, 'Posts retrieved successfully');
    }

    /**
     * @param CreateRequest $request
     * @param RealEstateRepository $reRepo
     * @param TemplateRepository $tRepo
     * @return Response
     * @throws \Exception
     *
     * @SWG\Post(
     *      path="/posts",
     *      summary="Store a newly created Post in storage",
     *      tags={"Listing"},
     *      description="Store Post",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Post that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Post")
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
     *                  ref="#/definitions/Post"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateRequest $request,
        RealEstateRepository $reRepo)
    {
        $input = $request->only(Post::Fillable);

        $input['user_id'] = \Auth::id();
        $input['status'] = Post::StatusNew;
        $input['type'] = $request->pinned ? Post::TypePinned : Post::TypeArticle;
        $input['needs_approval'] = true;
        if ($input['type'] == Post::TypeArticle) {
            $input['notify_email'] = true;
            $realEstate = $reRepo->first();
            if ($realEstate) {
                $input['needs_approval'] = $realEstate->news_approval_enable;
            }
        }

        $post = $this->postRepository->create($input);
        $this->postRepository->notifyAdmins($post);
        $data = $this->transformer->transform($post);
        return $this->sendResponse($data, 'Post saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/posts/{id}",
     *      summary="Display the specified Post",
     *      tags={"Listing"},
     *      description="Get Post",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Post",
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
     *                  ref="#/definitions/Post"
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
        /** @var Post $post */
        $post = $this->postRepository->with([
            'media',
            'user.tenant',
            'likesCounter',
            'likes' => function($q){$q->take(3);},
            'likes.user',
            'buildings.address.state',
            'buildings.serviceProviders',
            'buildings.media',
            'districts',
            'providers',
            'views',
        ])->withCount('allComments')->findWithoutFail($id);

        if (empty($post)) {
            return $this->sendError('Post not found');
        }
        $post->likers = $post->collectLikers();

        $data = $this->transformer->transform($post);
        return $this->sendResponse($data, 'Post retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateRequest $request
     * @return Response
     * @throws \Exception
     *
     * @SWG\Put(
     *      path="/posts/{id}",
     *      summary="Update the specified Post in storage",
     *      tags={"Listing"},
     *      description="Update Post",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Post",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Post that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Post")
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
     *                  ref="#/definitions/Post"
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
        $input = $request->only(Post::Fillable);
        $input['type'] = $request->pinned ? Post::TypePinned : Post::TypeArticle;
        $status = $request->get('status');

        /** @var Post $post */
        $post = $this->postRepository->findWithoutFail($id);

        if (empty($post)) {
            return $this->sendError('Post not found');
        }

        $this->postRepository->update($input, $id);
        $post = $this->postRepository->with([
            'media',
            'user.tenant',
            'likesCounter',
            'likes' => function($q){$q->take(3);},
            'likes.user',
            'buildings.address.state',
            'buildings.serviceProviders',
            'buildings.media',
            'districts',
        ])->withCount('allComments')->findWithoutFail($id);
        $post->status = $status;
        $data = $this->transformer->transform($post);
        return $this->sendResponse($data, 'Post updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/posts/{id}",
     *      summary="Remove the specified Post from storage",
     *      tags={"Listing"},
     *      description="Delete Post",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Post",
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
        /** @var Post $post */
        $post = $this->postRepository->findWithoutFail($id);

        if (empty($post)) {
            return $this->sendError('Post not found');
        }

        $post->delete();

        return $this->sendResponse($id, 'Post deleted successfully');
    }

    /**
     * @param PublishRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/posts/{id}/publish",
     *      summary="Publish a post",
     *      tags={"Listing"},
     *      description="Publish a post",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Post",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="status",
     *          in="body",
     *          type="integer",
     *          format="int32",
     *          description="The new status of the post",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/Post")
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
     *                  ref="#/definitions/Post"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function publish($id, PublishRequest $request)
    {
        $newStatus = $request->get('status');
        $post = $this->postRepository->findWithoutFail($id);
        if (empty($post)) {
            return $this->sendError('Post not found');
        }

        $post = $this->postRepository->setStatus($id, $newStatus, Carbon::now());

        return $this->sendResponse($post, 'Post published successfully');
    }

    /**
     * @param $id
     * @return Response
     *
     * @SWG\Post(
     *      path="/posts/{id}/like",
     *      summary="Like a post",
     *      tags={"Listing"},
     *      description="Like a post",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Post",
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
     *                  type="object",
     *                  description="logged in user"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function like($id)
    {
        $post = $this->postRepository->findWithoutFail($id);
        if (empty($post)) {
            return $this->sendError('Post not found');
        }

        $u = \Auth::user();
        $u->like($post);

        // if logged in user is tenant and
        // author of post is tenant and
        // author of post is different than liker
        if ($u->tenant && $post->user->tenant && $u->id != $post->user_id) {
            $post->user->notify(new PostLiked($post, $u->tenant));
        }
        return $this->sendResponse($this->uTransformer->transform($u),
            'Post liked successfully');
    }

    /**
     * @return Response
     *
     * @SWG\Post(
     *      path="/posts/{id}/unlike",
     *      summary="Unlike a post",
     *      tags={"Listing"},
     *      description="Unlike a post",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Post",
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
     *                  type="integer",
     *                  description="count of likes for the post"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function unlike($id)
    {
        $post = $this->postRepository->findWithoutFail($id);
        if (empty($post)) {
            return $this->sendError('Post not found');
        }

        $u = \Auth::user();
        $u->unlike($post);
        return $this->sendResponse($this->uTransformer->transform($u),
            'Post unliked successfully');
    }

    /**
     * @param int $id
     * @param int $bid
     * @param BuildingRepository $bRepo
     * @param AssignRequest $r
     * @return Response
     *
     * @SWG\Post(
     *      path="/posts/{id}/buildings/{bid}",
     *      summary="Assign the provided building to the post",
     *      tags={"Listing"},
     *      description="Assign the provided building to the post",
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
     *                  ref="#/definitions/Post"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function assignBuilding(int $id, int $bid, BuildingRepository $bRepo, AssignRequest $r)
    {
        $p = $this->postRepository->findWithoutFail($id);
        if (empty($p)) {
            return $this->sendError('Post not found');
        }
        $b = $bRepo->findWithoutFail($bid);
        if (empty($b)) {
            return $this->sendError('Building not found');
        }

        $p->buildings()->sync($b, false);
        $p = $this->postRepository->with([
            'media',
            'user.tenant',
            'likesCounter',
            'likes' => function($q){$q->take(3);},
            'likes.user',
            'buildings.address.state',
            'buildings.serviceProviders',
            'buildings.media',
            'districts',
        ])->withCount('allComments')->findWithoutFail($id);
        $p->likers = $p->collectLikers();


        return $this->sendResponse($p, 'Building assigned successfully');
    }

    /**
     * @param int $id
     * @param int $bid
     * @param BuildingRepository $bRepo
     * @param AssignRequest $r
     * @return Response
     *
     * @SWG\Delete(
     *      path="/posts/{id}/buildings/{bid}",
     *      summary="Unassign the provided building to the post",
     *      tags={"Listing"},
     *      description="Unassign the provided building to the post",
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
     *                  ref="#/definitions/Post"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function unassignBuilding(int $id, int $bid, BuildingRepository $bRepo, AssignRequest $r)
    {
        $p = $this->postRepository->findWithoutFail($id);
        if (empty($p)) {
            return $this->sendError('Post not found');
        }
        $b = $bRepo->findWithoutFail($bid);
        if (empty($b)) {
            return $this->sendError('Building not found');
        }

        $p->buildings()->detach($b);
        $p = $this->postRepository->with([
            'media',
            'user.tenant',
            'likesCounter',
            'likes' => function($q){$q->take(3);},
            'likes.user',
            'buildings.address.state',
            'buildings.serviceProviders',
            'buildings.media',
            'districts',
        ])->withCount('allComments')->findWithoutFail($id);
        $p->likers = $p->collectLikers();


        return $this->sendResponse($p, 'Building unassigned successfully');
    }

    /**
     * @param int $id
     * @param int $did
     * @param DistrictRepository $dRepo
     * @param AssignRequest $r
     * @return Response
     *
     * @SWG\Post(
     *      path="/posts/{id}/districts/{did}",
     *      summary="Assign the provided district to the post",
     *      tags={"Listing"},
     *      description="Assign the provided district to the post",
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
     *                  ref="#/definitions/Post"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function assignDistrict(int $id, int $did, DistrictRepository $dRepo, AssignRequest $r)
    {
        $p = $this->postRepository->findWithoutFail($id);
        if (empty($p)) {
            return $this->sendError('Post not found');
        }
        $d = $dRepo->findWithoutFail($did);
        if (empty($d)) {
            return $this->sendError('District not found');
        }

        $p->districts()->sync($d, false);
        $p = $this->postRepository->with([
            'media',
            'user.tenant',
            'likesCounter',
            'likes' => function($q){$q->take(3);},
            'likes.user',
            'buildings.address.state',
            'buildings.serviceProviders',
            'buildings.media',
            'districts',
        ])->withCount('allComments')->findWithoutFail($id);
        $p->likers = $p->collectLikers();


        return $this->sendResponse($p, 'District assigned successfully');
    }

    /**
     * @param int $id
     * @param int $did
     * @param DistrictRepository $dRepo
     * @param AssignRequest $r
     * @return Response
     *
     * @SWG\Delete(
     *      path="/posts/{id}/districts/{did}",
     *      summary="Unassign the provided district to the post",
     *      tags={"Listing"},
     *      description="Unassign the provided district to the post",
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
     *                  ref="#/definitions/Post"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function unassignDistrict(int $id, int $did, DistrictRepository $dRepo, AssignRequest $r)
    {
        $p = $this->postRepository->findWithoutFail($id);
        if (empty($p)) {
            return $this->sendError('Post not found');
        }
        $d = $dRepo->findWithoutFail($did);
        if (empty($d)) {
            return $this->sendError('Building not found');
        }

        $p->districts()->detach($d);
        $p = $this->postRepository->with([
            'media',
            'user.tenant',
            'likesCounter',
            'likes' => function($q){$q->take(3);},
            'likes.user',
            'buildings.address.state',
            'buildings.serviceProviders',
            'buildings.media',
            'districts',
        ])->withCount('allComments')->findWithoutFail($id);
        $p->likers = $p->collectLikers();


        return $this->sendResponse($p, 'District unassigned successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     * @throws \Exception
     *
     * @SWG\Get(
     *      path="/posts/{id}/locations",
     *      summary="Get a listing of the post locations.",
     *      tags={"Listing"},
     *      description="Get a listing of the post locations.",
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
     *                  @SWG\Items(ref="#/definitions/Post")
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
        $p = $this->postRepository->findWithoutFail($id);
        if (empty($p)) {
            return $this->sendError('Post not found');
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $locations = $this->postRepository->locations($p)->paginate($perPage);
        return $this->sendResponse($locations, 'Locations retrieved successfully');
    }


    /**
     * @param int $id
     * @param int $pid
     * @param ServiceProviderRepository $pRepo
     * @param AssignRequest $r
     * @return Response
     *
     * @SWG\Post(
     *      path="/posts/{id}/providers/{pid}",
     *      summary="Assign the provided service provider to the post",
     *      tags={"Listing"},
     *      description="Assign the provided service provider to the post",
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
     *                  ref="#/definitions/Post"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function assignProvider(int $id, int $pid, ServiceProviderRepository $pRepo, AssignRequest $r)
    {
        $p = $this->postRepository->findWithoutFail($id);
        if (empty($p)) {
            return $this->sendError('Post not found');
        }
        $provider = $pRepo->findWithoutFail($pid);
        if (empty($provider)) {
            return $this->sendError('Service provider not found');
        }

        $p->providers()->sync($provider, false);
        $p = $this->postRepository->with([
            'media',
            'user.tenant',
            'likesCounter',
            'likes' => function($q){$q->take(3);},
            'likes.user',
            'buildings.address.state',
            'buildings.serviceProviders',
            'buildings.media',
            'districts',
            'providers',
        ])->withCount('allComments')->findWithoutFail($id);
        $p->likers = $p->collectLikers();


        return $this->sendResponse($p, 'ServiceProvider assigned successfully');
    }

    /**
     * @param int $id
     * @param int $pid
     * @param ServiceProviderRepository $pRepo
     * @param AssignRequest $r
     * @return Response
     *
     * @SWG\Delete(
     *      path="/posts/{id}/providers/{pid}",
     *      summary="Unassign the provided service provider to the post",
     *      tags={"Listing"},
     *      description="Unassign the provided service provider to the post",
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
     *                  ref="#/definitions/Post"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function unassignProvider(int $id, int $pid, ServiceProviderRepository $pRepo, AssignRequest $r)
    {
        $p = $this->postRepository->findWithoutFail($id);
        if (empty($p)) {
            return $this->sendError('Post not found');
        }
        $provider = $pRepo->findWithoutFail($pid);
        if (empty($provider)) {
            return $this->sendError('Service provider not found');
        }

        $p->providers()->detach($provider);
        $p = $this->postRepository->with([
            'media',
            'user.tenant',
            'likesCounter',
            'likes' => function($q){$q->take(3);},
            'likes.user',
            'buildings.address.state',
            'buildings.serviceProviders',
            'buildings.media',
            'districts',
            'providers',
        ])->withCount('allComments')->findWithoutFail($id);
        $p->likers = $p->collectLikers();

        return $this->sendResponse($p, 'ServiceProvider unassigned successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Put(
     *      path="/posts/{id}/views",
     *      summary="Increment the view count of the post",
     *      tags={"Listing"},
     *      description="Increment the view count of the post",
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
     *                  ref="#/definitions/Post"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function incrementViews(int $id)
    {
        $p = $this->postRepository->findWithoutFail($id);
        if (empty($p)) {
            return $this->sendError('Post not found');
        }

        $p->incrementViews(\Auth::id());
        return $this->sendResponse($id, 'Views increased successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/posts/{id}/views",
     *      summary="List the view count of the post",
     *      tags={"Listing"},
     *      description="List the view count of the post",
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
     *                  ref="#/definitions/PostView"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function indexViews(int $id, PostViewTransformer $pvt, ListViewsRequest $req)
    {
        $p = $this->postRepository->findWithoutFail($id);
        if (empty($p)) {
            return $this->sendError('Post not found');
        }

        $perPage = $req->get('per_page', env('APP_PAGINATE', 10));
        $vs = $p->views()->with('user')->paginate($perPage);
        $ret = $pvt->transformPaginator($vs);
        return $this->sendResponse($ret, 'Views retrieved successfully');
    }
}
