<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\LoginDevice;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Autologin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Jenssegers\Agent\Agent;

/**
 * Class AuthController
 * @package App\Http\Controllers\API
 */

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/auth/signup",
     *      summary="User signup",
     *      tags={"Auth"},
     *      description="User signup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="User signup",
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
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed'
        ]);

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        $user->save();

        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/auth/login",
     *      summary="User login",
     *      tags={"Auth"},
     *      description="User login",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="User login",
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
    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $userData = $request->user();
        if ($userData->tenant && $userData->tenant->status == Tenant::StatusNotActive) {
            return response()->json([
                'message' => 'Your account has been disabled'
            ], 401);
        }

        $user = User::where('email', $userData->email)
            ->where('password', $userData->password)->firstOrFail();

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addDays(1);

        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addDays(30);
        }

        $token->save();
        $this->saveLoginDevice($user);

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }

    /**
     * @param $user
     */
    protected function saveLoginDevice($user)
    {
        // @TODO this is tmp for testing purpose
        $agent = new Agent();
        $data = [
            'created_by' => now()->toDateTimeString(),
            'mobile' => $agent->isMobile()? 1 : 0,
            'desktop' => $agent->isDesktop() ? 1 : 0,
            'tablet' => $agent->isTablet() ? 1 : 0,
            'user_id' => $user->id,
            'tenant_id' => $user->tenant->id ?? null
        ];
        $user->last_login_at = now();
        $user->save();
        LoginDevice::create($data);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/auth/logout",
     *      summary="User logout",
     *      tags={"Auth"},
     *      description="User logout",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="User logout",
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
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/auth/autologin",
     *      summary="User autologin",
     *      tags={"Auth"},
     *      description="User autologin",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="User autologin",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Autologin")
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
    public function autologin(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $a = Autologin::where('token', $request->token)->firstOrFail();
        $a->used++;
        $a->save();

        $tokenResult = $a->user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addDays(30);
        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
            'redirect' => $a->redirect,
        ]);
    }
}
