<?php

namespace App\Http\Controllers\API;

use App\Criteria\Common\RequestCriteria;
use App\Criteria\Users\FilterByRolesCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\User\ChangePasswordRequest;
use App\Http\Requests\API\User\CreateRequest;
use App\Http\Requests\API\User\DeleteRequest;
use App\Http\Requests\API\User\ListRequest;
use App\Http\Requests\API\User\ShowRequest;
use App\Http\Requests\API\User\UpdateLoggedInRequest;
use App\Http\Requests\API\User\UpdateRequest;
use App\Http\Requests\API\User\UploadImageRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Transformers\UserTransformer;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;

/**
 * Class UserController
 * @package App\Http\Controllers\API
 */
class UserAPIController extends AppBaseController
{
    /** @var  UserRepository */
    private $userRepository;

    /**
     * UserAPIController constructor.
     * @param UserRepository $userRepo
     */
    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws /Exception
     *
     * @SWG\Get(
     *      path="/users",
     *      summary="Get a listing of the Users.",
     *      tags={"User"},
     *      description="Get all Users",
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
     *                  @SWG\Items(ref="#/definitions/User")
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
        $this->userRepository->pushCriteria(new RequestCriteria($request));
        $this->userRepository->pushCriteria(new FilterByRolesCriteria($request));
        $this->userRepository->pushCriteria(new LimitOffsetCriteria($request));

        $getAll = $request->get('get_all', false);
        if ($getAll) {
            $users = $this->userRepository->get();
            return $this->sendResponse($users->toArray(), 'Users retrieved successfully');
        }
        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $users = $this->userRepository->with('roles')->paginate($perPage);

        return $this->sendResponse($users->toArray(), 'Users retrieved successfully');
    }

    /**
     * @param ListRequest $request
     * @return Response
     * @throws /Exception
     *
     * @SWG\Get(
     *      path="/users/requestManagers",
     *      summary="Get a listing of the requestManagers Users.",
     *      tags={"User"},
     *      description="Get all requestManagers Users",
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
     *                  @SWG\Items(ref="#/definitions/User")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function requestManagers(ListRequest $request)
    {
        $request->request->set('roles', ['administrator', 'super_admin', 'manager']);
        $this->userRepository->pushCriteria(new RequestCriteria($request));
        $this->userRepository->pushCriteria(new FilterByRolesCriteria($request));
        $this->userRepository->pushCriteria(new LimitOffsetCriteria($request));

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $users = $this->userRepository
            ->with('roles')
            ->withCount([
                'requestsReceived',
                'requestsInProcessing',
                'requestsAssigned',
                'requestsDone',
                'requestsReactivated',
                'requestsArchived',
            ])->paginate($perPage);

        return $this->sendResponse($users->toArray(), 'Users retrieved successfully');
    }

    /**
     * @param ListRequest $request
     * @return Response
     * @throws /Exception
     *
     * @SWG\Get(
     *      path="/services",
     *      summary="Get a listing of the Service Users.",
     *      tags={"Service"},
     *      description="Get all services Users",
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
     *                  @SWG\Items(ref="#/definitions/User")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function services(ListRequest $request)
    {
        $request->request->set('role', 'service');
        $this->userRepository->pushCriteria(new RequestCriteria($request));
        $this->userRepository->pushCriteria(new FilterByRolesCriteria($request));
        $this->userRepository->pushCriteria(new LimitOffsetCriteria($request));

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $users = $this->userRepository->with('roles')->paginate($perPage);

        return $this->sendResponse($users->toArray(), 'Users retrieved successfully');
    }

    /**
     * @param CreateRequest $request
     * @return Response
     * @throws /Exception
     *
     * @SWG\Post(
     *      path="/users",
     *      summary="Store a newly created User in storage",
     *      tags={"User"},
     *      description="Store User",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="User that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/User")
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
     *                  ref="#/definitions/User"
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

        $users = $this->userRepository->create($input);
        $users->load('settings')->load('roles');

        return $this->sendResponse($users->toArray(), 'User saved successfully');
    }

    /**
     * @param int $id
     * @param ShowRequest $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/users/{id}",
     *      summary="Display the specified User",
     *      tags={"User"},
     *      description="Get User",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of User",
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
     *                  ref="#/definitions/User"
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
        /** @var User $user */
        $user = $this->userRepository->findWithoutFail($id);
        if (empty($user)) {
            return $this->sendError('User not found');
        }

        $user->load('settings')->load('roles');
        return $this->sendResponse($user->toArray(), 'User retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/users/me",
     *      summary="Display the Logged In User",
     *      tags={"User"},
     *      description="Get Logged In User",
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
     *                  ref="#/definitions/User"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function showLoggedIn(Request $request)
    {
        /** @var User $user */
        $user = $this->userRepository->findWithoutFail($request->user()->id);
        if (empty($user)) {
            return $this->sendError('User not found');
        }

        $user->load(['settings', 'roles.perms', 'tenant.media']);
        $user->unread_notifications_count = $user->unreadNotifications()->count();
        return $this->sendResponse($user->toArray(), 'User retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateRequest $request
     * @return Response
     * @throws /Exception
     *
     * @SWG\Put(
     *      path="/users/{id}",
     *      summary="Update the specified User in storage",
     *      tags={"User"},
     *      description="Update User",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of User",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="User that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/User")
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
     *                  ref="#/definitions/User"
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
        $input = $request->all();

        /** @var User $user */
        $user = $this->userRepository->findWithoutFail($id);
        if (empty($user)) {
            return $this->sendError('User not found');
        }

        // image upload
        $fileData = base64_decode($request->get('image_upload', ''));
        if ($fileData) {
            try {
                $input['avatar'] = $this->userRepository->uploadImage($fileData, $user);
            } catch (\Exception $e) {
                return $this->sendError('User image upload: ' . $e->getMessage());
            }
        }

        $user = $this->userRepository->update($input, $id);

        $user->load('settings')->load('roles');

        $response = (new UserTransformer)->transform($user);
        return $this->sendResponse($response, 'User updated successfully');
    }

    /**
     * @param UpdateLoggedInRequest $request
     * @return Response
     * @throws /Exception
     *
     * @SWG\Put(
     *      path="/users/me",
     *      summary="Update the Logged In UserSettings in storage",
     *      tags={"User"},
     *      description="Update the Logged In UserSettings",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UserSettings that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/User")
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
     *                  ref="#/definitions/User"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function updateLoggedIn(UpdateLoggedInRequest $request)
    {
        $input = $request->all();
        $id = $request->user()->id;

        /** @var User $user */
        $user = $this->userRepository->findWithoutFail($id);
        if (empty($user)) {
            return $this->sendError('User not found');
        }

        if (isset($input['password_old']) && !Hash::check($input['password_old'], $user->password)) {
            return $this->sendError('User password incorrect');
        }

        // image upload
        $fileData = base64_decode($request->get('image_upload', ''));
        if ($fileData) {
            try {
                $input['avatar'] = $this->userRepository->uploadImage($fileData, $user);
            } catch (\Exception $e) {
                return $this->sendError('User image upload: ' . $e->getMessage());
            }
        }

        $user = $this->userRepository->update($input, $id);

        $user->load('settings')->load('roles');
        $response = (new UserTransformer)->transform($user);
        return $this->sendResponse($response, 'User updated successfully');
    }

    /**
     * @param ChangePasswordRequest $request
     * @return Response
     * @throws /Exception
     *
     * @SWG\Put(
     *      path="/users/me/change_password",
     *      summary="Change password for Logged In User",
     *      tags={"User"},
     *      description="Change password for Logged In User",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UserSettings that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/User")
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
     *                  ref="#/definitions/User"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $input = $request->all();
        $id = $request->user()->id;

        /** @var User $user */
        $user = $this->userRepository->findWithoutFail($id);
        if (empty($user)) {
            return $this->sendError('User not found');
        }

        if (!isset($input['password_old']) || !Hash::check($input['password_old'], $user->password)) {
            return $this->sendError('User password incorrect');
        }

        $user = $this->userRepository->with(['settings'])->update($input, $id);

        $response = (new UserTransformer)->transform($user);
        return $this->sendResponse($response, 'User updated successfully');
    }

    /**
     * @param int $id
     * @param UploadImageRequest $request
     * @return Response
     * @throws /Exception
     *
     * @SWG\Put(
     *      path="/users/{id}/upload_image",
     *      summary="Change profile image for selected User",
     *      tags={"User"},
     *      description="Change profile image for selected User",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UserSettings that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/User")
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
     *                  ref="#/definitions/User"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function uploadImage(int $id, UploadImageRequest $request)
    {
        $input = $request->all();

        /** @var User $user */
        $user = $this->userRepository->findWithoutFail($id);
        if (empty($user)) {
            return $this->sendError('User not found');
        }

        // image upload
        $fileData = base64_decode($request->get('image_upload', ''));
        if ($fileData) {
            try {
                $input['avatar'] = $this->userRepository->uploadImage($fileData, $user);
            } catch (\Exception $e) {
                return $this->sendError('User image upload: ' . $e->getMessage());
            }
        }

        $user = $this->userRepository->with(['settings'])->update($input, $id);

        $response = (new UserTransformer)->transform($user);
        return $this->sendResponse($response, 'User updated successfully');
    }

    /**
     * @param UploadImageRequest $request
     * @return Response
     * @throws /Exception
     *
     * @SWG\Put(
     *      path="/users/me/upload_image",
     *      summary="Change profile image for Logged In User",
     *      tags={"User"},
     *      description="Change profile image for Logged In User",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UserSettings that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/User")
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
     *                  ref="#/definitions/User"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function uploadImageLoggedIn(UploadImageRequest $request)
    {
        $input = $request->all();
        $id = $request->user()->id;

        /** @var User $user */
        $user = $this->userRepository->findWithoutFail($id);
        if (empty($user)) {
            return $this->sendError('User not found');
        }

        // image upload
        $fileData = base64_decode($request->get('image_upload', ''));
        if ($fileData) {
            try {
                $input['avatar'] = $this->userRepository->uploadImage($fileData, $user);
            } catch (\Exception $e) {
                return $this->sendError('User image upload: ' . $e->getMessage());
            }
        }

        $user = $this->userRepository->with(['settings'])->update($input, $id);

        $response = (new UserTransformer)->transform($user);
        return $this->sendResponse($response, 'User updated successfully');
    }

    /**
     * @param int $id
     * @param DeleteRequest $request
     * @return Response
     *
     * @SWG\Delete(
     *      path="/users/{id}",
     *      summary="Remove the specified User from storage",
     *      tags={"User"},
     *      description="Delete User",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of User",
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
        /** @var User $user */
        $user = $this->userRepository->findWithoutFail($id);
        if (empty($user)) {
            return $this->sendError('User not found');
        }

        $user->forceDelete();
        return $this->sendResponse($id, 'User deleted successfully');
    }
}
