<?php

namespace App\Http\Controllers\API;

use App;
use App\Http\Controllers\AppBaseController;
use App\Models\Post;
use App\Models\Product;
use App\Models\PropertyManager;
use App\Models\ServiceProvider;
use App\Models\ServiceRequest;
use App\Models\TemplateCategory;
use App\Models\Tenant;
use App\Repositories\BuildingRepository;
use App\Repositories\TenantRepository;
use App\Repositories\UnitRepository;
use Config;
use Illuminate\Http\Response;

/**
 * Class UtilsAPIController
 * @package App\Http\Controllers\API
 */
class UtilsAPIController extends AppBaseController
{
    /** @var  BuildingRepository */
    private $buildingRepository;

    /** @var  UnitRepository */
    private $unitRepository;

    /** @var  TenantRepository */
    private $tenantRepository;

    /**
     * UtilsAPIController constructor.
     * @param BuildingRepository $buildingRepo
     * @param UnitRepository $unitRepo
     * @param TenantRepository $tenantRepo
     */
    public function __construct(BuildingRepository $buildingRepo, UnitRepository $unitRepo, TenantRepository $tenantRepo)
    {
        $this->buildingRepository = $buildingRepo;
        $this->unitRepository = $unitRepo;
        $this->tenantRepository = $tenantRepo;
    }

    /**
     * @return Response
     *
     * @SWG\Get(
     *      path="/constants",
     *      summary="Display the app constants",
     *      description="Get he app constants",
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
     *                  ref="#/definitions/Building"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function constants()
    {
        $app = [
            'languages' => Config::get('app.locales'),
        ];

        $response = [
            'app' => $app,
            'buildings' => [],
            'units' => [],
            'tenants' => self::getTenantConstants(),
            'service_providers' => self::getServiceProviderConstants(),
            'service_requests' => self::getServiceRequestsConstants(),
            'serviceProviders' => self::getServiceProviderConstants(),
            'serviceRequests' => self::getServiceRequestsConstants(),
            'propertyManager' => self::getPropertyManagerConstants(),
            'posts' => self::getPostConstants(),
            'products' => self::getProductConstants(),
            'templates' => self::getTemplateConstants(),
            'audits' => self::getAuditConstants(),
        ];

        return $this->sendResponse($response, 'App constants statistics retrieved successfully');
    }

    private static function getAuditConstants()
    {
        $events = [
            'created', 'updated', 'deleted',
            'provider_unassigned', 'provider_assigned', 'provider_notified',
            'user_unassigned', 'user_assigned',
            'media_uploaded', 'media_deleted',
        ];

        return array_combine($events, $events);
    }

    private function getTenantConstants()
    {
        $result = [
            'title' => Tenant::Title,
            'status' => Tenant::Status,
        ];

        return $result;
    }

    private function getServiceProviderConstants()
    {
        $result = [
            'category' => ServiceProvider::ServiceProviderCategories,
        ];

        return $result;
    }

    private function getServiceRequestsConstants()
    {
        $result = [
            'status' => ServiceRequest::Status,
            'priority' => ServiceRequest::Priority,
            'qualification' => ServiceRequest::Qualification,
            'statusByTenant' => ServiceRequest::StatusByTenant,
            'statusByService' => ServiceRequest::StatusByService,
            'statusByAgent' => ServiceRequest::StatusByAgent,
            'visibility' => ServiceRequest::Visibility,
        ];

        return $result;
    }

    private function getPropertyManagerConstants()
    {
        $result = [
            'title' => PropertyManager::Title,
        ];

        return $result;
    }

    private function getPostConstants()
    {
        $result = [
            'type' => Post::Type,
            'visibility' => Post::Visibility,
            'status' => Post::Status,
            'category' => Post::Category,
        ];

        return $result;
    }

    private function getProductConstants()
    {
        $result = [
            'type' => Product::Type,
            'visibility' => Product::Visibility,
            'status' => Product::Status,
        ];

        return $result;
    }

    private function getTemplateConstants()
    {
        $result = [
            'type' => TemplateCategory::Type,
        ];

        return $result;
    }
}
