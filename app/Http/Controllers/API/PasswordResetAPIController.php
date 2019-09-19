<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\PasswordReset;
use App\Models\User;
use App\Repositories\PasswordResetRepository;
use App\Repositories\UserRepository;
use Auth;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

/**
 * Class PasswordResetController
 * @package App\Http\Controllers\API
 */
class PasswordResetController extends AppBaseController
{
    /** @var  PasswordResetRepository */
    private $passwordResetRepository;
    private $userRepository;

    public function __construct(PasswordResetRepository $passwordResetRepo, UserRepository $userRepo)
    {
        $this->passwordResetRepository = $passwordResetRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @SWG\Post(
     *      path="/auth/forgot_password",
     *      summary="Send email for reseting password",
     *      tags={"Auth"},
     *      description="PasswordReset",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Send email for reseting password",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PasswordReset")
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
     *                  ref="#/definitions/PasswordReset"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function forgot(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);

        $input = [
            'email' => $request->get('email'),
            'token' => Str::random(60)
        ];

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->sendError(
                sprintf('We can\'t find a user with that %s - email address .', $request->email)
            );
        }

        $this->passwordResetRepository->createToken($input, $user);

        return $this->sendResponse([], 'Password Reset Request send successfully');
    }

    /**
     * @param string $token
     * @return Response
     *
     * @SWG\Get(
     *      path="/auth/forgot_password/{token}",
     *      summary="Validate token for password reset",
     *      tags={"Auth"},
     *      description="Validate token for password reset",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="string",
     *          description="token",
     *          type="string",
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
     *                  ref="#/definitions/PasswordReset"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function find(string $token)
    {
        /** @var PasswordReset $passwordReset */
        $passwordReset = $this->passwordResetRepository->findToken($token);
        if (empty($passwordReset)) {
            return $this->sendError('This password reset token is invalid.');
        }

        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return $this->sendError('This password reset token is invalid.');
        }

        return $this->sendResponse($passwordReset->toArray(), 'User retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/auth/reset_password",
     *      summary="Reset password",
     *      tags={"Auth"},
     *      description="Reset password",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Reset password",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PasswordReset")
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
     *                  ref="#/definitions/PasswordReset"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed',
            'token' => 'required|string'
        ]);

        $input = $request->all();

        $passwordReset = PasswordReset::where([
            ['token', $input['token']],
            ['email', $input['email']]
        ])->first();
        if (!$passwordReset) {
            return $this->sendError('This password reset token is invalid.');
        }

        $user = User::where('email', $passwordReset->email)->first();
        if (!$user) {
            return $this->sendError('We can\'t find a user with that e-mail address.');
        }

        $this->userRepository->resetPassword($input, $user->id);
        $passwordReset->delete();

        return $this->sendResponse([], 'Password Reset Request send successfully');
    }
}
