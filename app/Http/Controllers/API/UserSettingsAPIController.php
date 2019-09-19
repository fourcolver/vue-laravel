<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\UserSetting\CreateRequest;
use App\Http\Requests\API\UserSetting\ShowRequest;
use App\Http\Requests\API\UserSetting\UpdateRequest;
use App\Http\Requests\API\UserSetting\UpdateLoggedInRequest;
use App\Models\User;
use App\Models\UserSettings;
use App\Repositories\UserSettingsRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class UserSettingsController
 * @package App\Http\Controllers\API
 */
class UserSettingsAPIController extends AppBaseController
{
    /** @var  UserSettingsRepository */
    private $userSettingsRepository;

    public function __construct(UserSettingsRepository $userSettingsRepo)
    {
        $this->userSettingsRepository = $userSettingsRepo;
    }

    public function index(int $user_id, Request $request)
    {
        $user = (new User)->find($user_id);
        if (empty($user)) {
            return $this->sendError('User not found');
        }

        $userSettings = $user->settings->toArray();

        return $this->sendResponse($userSettings, 'User Settings retrieved successfully');
    }

    public function store(CreateRequest $request)
    {
        $input = $request->all();

        $userSettings = $this->userSettingsRepository->create($input);

        return $this->sendResponse($userSettings->toArray(), 'User Settings saved successfully');
    }

    public function show($id, ShowRequest $request)
    {
        /** @var UserSettings $userSettings */
        $userSettings = $this->userSettingsRepository->findWithoutFail($id);

        if (empty($userSettings)) {
            return $this->sendError('User Settings not found');
        }

        return $this->sendResponse($userSettings->toArray(), 'User Settings retrieved successfully');
    }

    /**
     * @param int $user_id
     * @param UpdateRequest $request
     * @return Response
     * @throws ValidatorException
     *
     * @SWG\Put(
     *      path="/users/{user_id}",
     *      summary="Update the specified UserSettings in storage",
     *      tags={"User"},
     *      description="Update UserSettings",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UserSettings",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UserSettings that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/UserSettings")
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
     *                  ref="#/definitions/UserSettings"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($user_id, UpdateRequest $request)
    {
        $input = $request->all();

        $user = (new User)->find($user_id);
        if (empty($user)) {
            return $this->sendError('User not found');
        }

        $userSettings = $this->userSettingsRepository->update($input, $user->settings->id);

        return $this->sendResponse($userSettings->toArray(), 'UserSettings updated successfully');
    }

    /**
     * @param UpdateRequest $request
     * @return Response
     * @throws ValidatorException
     *
     * @SWG\Put(
     *      path="/users/me/settings",
     *      summary="Update the Logged In UserSettings in storage",
     *      tags={"User"},
     *      description="Update UserSettings",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UserSettings that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/UserSettings")
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
     *                  ref="#/definitions/UserSettings"
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
        $user_id = $request->user()->id;

        $user = (new User)->find($user_id);
        if (empty($user)) {
            return $this->sendError('User not found');
        }

        $userSettings = $this->userSettingsRepository->update($input, $user->settings->id);

        return $this->sendResponse($userSettings->toArray(), 'UserSettings updated successfully');
    }

    public function destroy($id)
    {
        /** @var UserSettings $userSettings */
        $userSettings = $this->userSettingsRepository->findWithoutFail($id);

        if (empty($userSettings)) {
            return $this->sendError('User Settings not found');
        }

        $userSettings->delete();

        return $this->sendResponse($id, 'User Settings deleted successfully');
    }
}
