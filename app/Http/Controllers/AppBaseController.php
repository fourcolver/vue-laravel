<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use InfyOm\Generator\Utils\ResponseUtil;
use OwenIt\Auditing\Models\Audit;

/**
 * @SWG\Swagger(
 *   basePath="/api/v1",
 *   @SWG\Info(
 *     title="Propify API",
 *     version="1.0.0",
 *   )
 * )
 * This class should be parent class for other API controllers
 * Class AppBaseController
 */
/**
 *   @SWG\SecurityScheme(
 *     securityDefinition="passport-swaggervel_auth",
 *     description="OAuth2 grant provided by Laravel Passport",
 *     type="oauth2",
 *     authorizationUrl="/oauth/authorize",
 *     tokenUrl="/oauth/token",
 *     flow="accessCode",
 *     scopes={
 *       *
 *     }
 *   ),
 */
class AppBaseController extends Controller
{
    /**
     * @param $result
     * @param $message
     * @return mixed
     */
    public function sendResponse($result, $message)
    {
        return Response::json(ResponseUtil::makeResponse($message, $result));
    }

    /**
     * @param $error
     * @param int $code
     * @return mixed
     */
    public function sendError($error, $code = 404)
    {
        return Response::json(ResponseUtil::makeError($error), $code);
    }

    /**
     * @param int $id
     * @return Audit
     */
    protected function newRequestAudit(int $id)
    {
        $aType = array_flip(Relation::$morphMap)[ServiceRequest::class];
        $a = $this->newAudit();
        $a->auditable_type = $aType ?? ServiceRequest::class;
        $a->auditable_id = $id;
        return $a;
    }

    /**
     * @return Audit
     */
    protected function newAudit()
    {
        return new Audit([
            'user_type' => \App\Models\User::class,
            'user_id' => \Auth::id(),
            'url' => Request::fullUrl(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
            'old_values' => [],
            'new_values' => [],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
