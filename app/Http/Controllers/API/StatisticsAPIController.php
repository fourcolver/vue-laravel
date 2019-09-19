<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\Building;
use App\Models\LoginDevice;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestCategory;
use App\Models\Tenant;
use App\Models\Product;
use App\Models\Post;
use App\Repositories\BuildingRepository;
use App\Repositories\ServiceRequestRepository;
use App\Repositories\TenantRepository;
use App\Repositories\UnitRepository;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * @TODO authorize each request
 *
 * Class StatisticsAPIController
 * @package App\Http\Controllers\API
 */
class StatisticsAPIController extends AppBaseController
{
    const YEAR = 'year';
    const MONTH = 'month';
    const WEEK = 'week';
    const DAY = 'day';
    const DEFAULT_PERIOD = self::DAY;
    const PERMITTED_PERIODS = [
        self::DAY,
        self::WEEK,
        self::MONTH,
        self::YEAR,
    ];

    const PERMITTED_HEAT_PERIODS = [
        self::WEEK,
        self::YEAR,
    ];

    const QUERY_PARAMS = [
        'year' => 'year',
        'period' => 'period',
        'date' => 'date',
        'start_date' => 'start_date',
        'end_date' => 'end_date',
        'table' => 'table',
        'column' => 'column'
    ];

    /**
     * table,column config for make DonutChart
     *
     * this is use for permit correct table and column value
     * also according this config can get default values
     *
     * if not set table optional parameter or write not permitted table name that case must be get data
     * for this table and must be group by first value of self::PERMITTED_TABLES_GROUP[self::DEFAULT_TABLE]['column']

     */
    const PERMITTED_TABLES_GROUP = [
        'service_requests' => [
            'class' => ServiceRequest::class,
            'columns' => [
                'status'
            ]
        ],
        'tenants' => [
            'class' => Tenant::class,
            'columns' => [
                'status'
            ]
        ],
        'products' => [
            'class' => Product::class,
            'columns' => [
                'status',
                'type'
            ]
        ],
        'posts' => [
            'class' => Post::class,
            'columns' => [
                'status',
                'type'
            ]
        ],
    ];

    const PERMITTED_TABLES_FOR_CREATED_DATE = [
        'products' => [
            'class' => Product::class,
            'columns' => [
                'status',
            ]
        ],
        'tenants' => [
            'class' => Tenant::class,
            'columns' => [
                'status',
            ]
        ],
        'posts' => [
            'class' => Post::class,
            'columns' => [
                'status',
            ]
        ],
    ];

    /** @var  BuildingRepository */
    private $buildingRepo;

    /** @var  UnitRepository */
    private $unitRepo;

    /** @var  TenantRepository */
    private $tenantRepo;

    /** @var  ServiceRequestRepository */
    private $serviceRequestRepo;

    public function __construct(
        BuildingRepository $br,
        UnitRepository $ur,
        TenantRepository $tr,
        ServiceRequestRepository $srr)
    {
        $this->buildingRepo = $br;
        $this->unitRepo = $ur;
        $this->tenantRepo = $tr;
        $this->serviceRequestRepo = $srr;
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/buildings/{id}/statistics",
     *      summary="Display the specified Building",
     *      tags={"Building"},
     *      description="Get Building",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Building",
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
    public function buildingStatistics(int $id)
    {
        /** @var Building $building */
        $building = $this->buildingRepo->findWithoutFail($id);
        if (empty($building)) {
            return $this->sendError('Building not found');
        }

        $tenants = $this->tenantRepo->getTotalTenantsFromBuilding($building->id);
        $units = $this->unitRepo->getTotalUnitsFromBuilding($building->id);

        $occupiedUnits = 0;
        $freeUnit = 0;
        if ($tenants > 0 && $units > 0) {
            $occupiedUnits = round($tenants * 100 / $units);
            $freeUnit = round(($units - $tenants) * 100 / $units);
        }

        $response = [
            'total_tenants' => $tenants,
            'total_units' => $units,
            'occupied_units' => $occupiedUnits,
            'free_units' => $freeUnit,
        ];

        return $this->sendResponse($response, 'Building statistics retrieved successfully');
    }

    public function allBuildingStatistics()
    {
        $tenantCount = $this->tenantRepo->count();
        $unitCount = $this->unitRepo->count();
        $buildingCount = $this->buildingRepo->count();

        $occupiedUnits = 0;
        $freeUnit = 0;
        if ($tenantCount > 0 && $unitCount> 0) {
            $occupiedUnits = round($tenantCount * 100 / $unitCount);
            $freeUnit = 100 - $occupiedUnits;
        }

        /**
         * @TODO adjust response for frontend
         */
        $response = [
            'total_building' => $buildingCount,
            'total_tenants' => $tenantCount,
            'total_units' => $unitCount,
            'occupied_units' => $occupiedUnits,
            'free_units' => $freeUnit,
        ];

        return $this->sendResponse($response, 'Building statistics retrieved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenants/{id}/statistics",
     *      summary="Display the specified Tenant statistics",
     *      tags={"Building"},
     *      description="Get Tenants statistics",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Tenant",
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
     *                  ref="#/definitions/Tenant"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function tenantStatistics(int $id)
    {
        /** @var Tenant $tenant */
        $tenant = $this->tenantRepo->withCount(
            [
                'requests',
                'requestsReceived',
                'requestsInProcessing',
                'requestsAssigned',
                'requestsDone',
                'requestsReactivated',
                'requestsArchived',
            ])->withCount(
            [
                'requests',
                'requestsReceived',
                'requestsInProcessing',
                'requestsAssigned',
                'requestsDone',
                'requestsReactivated',
                'requestsArchived',
            ])->findWithoutFail($id);

        if (empty($tenant)) {
            return $this->sendError('Tenant not found');
        }

        $response = [
            'requests_count' => $tenant->requests_count,
            'opened_requests_count' => $tenant->requests_received_count,
            'pending_requests_count' => $tenant->requests_in_processing_count,
            'done_requests_count' => $tenant->requests_done_count,
            'archived_requests_count' => $tenant->requests_archived_count,

            'requests' => $tenant->requests,
            'opened_requests' => $tenant->requestsReceived,
            'pending_requests' => $tenant->requestsInProcessing,
            'done_requests' => $tenant->requestsDone,
            'archived_requests' => $tenant->requestsArchived,
        ];

        return $this->sendResponse($response, 'Tenant statistics retrieved successfully');
    }

    /**
     * @return Response
     *
     * @SWG\Get(
     *      path="/requests/{id}/statistics",
     *      summary="Display the specified Tenant statistics",
     *      tags={"Building"},
     *      description="Get ServiceRequest statistics",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Tenant",
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
     *                  ref="#/definitions/Tenant"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function requestsStatistics()
    {
        $serviceReq = (new ServiceRequest);

        try {
            $averageRequestTime = $serviceReq->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, `created_at`, `solved_date`)) as solved')
                ->where('status', ServiceRequest::StatusDone)
                ->first();

            $response = [
                'averageRequestTime' => CarbonInterval::minutes(ceil($averageRequestTime->solved))->cascade()->forHumans(),

                'requestsCount' => $serviceReq->count(),
                'requestsReceivedCount' => $serviceReq->requestsReceived()->count(),
                'requestsInProcessingCount' => $serviceReq->requestsInProcessing()->count(),
                'requestsAssignedCount' => $serviceReq->requestsAssigned()->count(),
                'requestsDoneCount' => $serviceReq->requestsDone()->count(),
                'requestsReactivatedCount' => $serviceReq->requestsReactivated()->count(),
                'requestsArchivedCount' => $serviceReq->requestsArchived()->count(),
            ];

        } catch (\Exception $e) {
            return $this->sendError('ServiceRequest statistics error: ' . $e->getMessage());
        }

        return $this->sendResponse($response, 'Service Request statistics retrieved successfully');
    }

    /**
     * @return mixed
     */
    public function tenantsGenderStatistics()
    {
        $tenants = Tenant::selectRaw('count(id) as count, title')
            ->whereIn('title', ['mr', 'mrs'])
            ->groupBy('title')
            ->get();
        $manCount = $tenants->where('title', 'mr')->first()->count ?? 0;
        $femaleCount = $tenants->where('title', 'mrs')->first()->count ?? 0;
        if ($manCount + $femaleCount == 0) {
            $response = [
                'labels' => [
                    'mr',
                    'mrs'
                ],
                'data' => [
                    0,
                    0
                ],
                'tag_percentage' => [
                    0,
                    0
                ],
                'average_age' => [
                    'mr' => 0,
                    'mrs' => 0,
                    'both' => 0
                ]
            ];
            return $this->sendResponse($response, 'Tenants gender statistics retrieved successfully');
        }


        $femalePercentage = round($femaleCount * 100 / ($femaleCount + $manCount));


        $tenantsAge = Tenant::selectRaw('FROM_UNIXTIME(AVG(UNIX_TIMESTAMP(birth_date))) AS duration, title')
            ->whereIn('title', ['mr', 'mrs'])
            ->groupBy('title')
            ->get();
        $bothTenants = Tenant::selectRaw('FROM_UNIXTIME(AVG(UNIX_TIMESTAMP(birth_date))) AS duration')
            ->whereIn('title', ['mr', 'mrs'])
            ->value('duration');

        $femaleAvgAge = $tenantsAge->where('title', 'mrs')->first()->duration ?? 0;
        $manAvgAge = $tenantsAge->where('title', 'mr')->first()->duration ?? 0;

        $response = [
            'labels' => [
                'mr',
                'mrs'
            ],
            'data' => [
                $manCount,
                $femaleCount
            ],
            'tag_percentage' => [
                 100 - $femalePercentage,
                $femalePercentage
            ],
            'average_age' => [
                'mr' => Carbon::parse($manAvgAge)->age,
                'mrs' => Carbon::parse($femaleAvgAge)->age,
                'both' => Carbon::parse($bothTenants)->age
            ]
        ];

        return $this->sendResponse($response, 'Tenants gender statistics retrieved successfully');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function adminStats(Request $request)
    {
        [$startDate, $endDate] = $this->getStartDateEndDate($request);
        $ret = [
            'total_requests' => DB::table('service_requests')->count('id'),
            'tenants_per_day' => $this->getDayCountStatistic('tenants', $startDate, $endDate),
            'tenants_per_status' => [],

            'requests_per_status' => [],
            'requests_per_category' => [],

            'products_per_day' => $this->getDayCountStatistic('products', $startDate, $endDate),
            'products_per_status' => [],

            'posts_per_day' => $this->getDayCountStatistic('posts', $startDate, $endDate),
            'posts_per_status' => [],
        ];

        $isConvertResponse = false;
        $optionalArgs = compact('isConvertResponse', 'startDate', 'endDate');

        $ret = array_merge($ret, $this->chartRequestByCreationDate($request, $optionalArgs));
        $ret['requests_per_status'] = $this->chartRequestByColumn($request, $optionalArgs);
        $ret['tenants_per_status'] = $this->chartRequestByColumn($request, array_merge($optionalArgs, ['table' => 'tenants']));
        $ret['products_per_status'] = $this->chartRequestByColumn($request, array_merge($optionalArgs, ['table' => 'products']));
        $ret['posts_per_status'] = $this->chartRequestByColumn($request, array_merge($optionalArgs, ['table' => 'posts']));
        $categoryDayStatistics = collect($ret['requests_per_day_ydata']);

        $ret['requests_per_category']['labels'] = $categoryDayStatistics->map(function($el) {
            return $el['name'];
        });
        $ret['requests_per_category']['data'] = $categoryDayStatistics->map(function($el) {
            return array_sum($el['data']);
        });

        $avgReqFix = DB::select("select coalesce(floor(avg(time_to_sec(timediff(solved_date, created_at)))), 0) duration
            from service_requests where solved_date is not null;");
        $ret['avg_request_duration'] = $avgReqFix ? gmdate("H:i",$avgReqFix[0]->duration) : 0;

        return $this->sendResponse($ret, 'Admin statistics retrieved successfully');
    }

    /**
     * @param Request $request
     * @param array $optionalArgs
     * @return mixed
     */
    public function chartRequestByCreationDate(Request $request, $optionalArgs = [])
    {
        [$startDate, $endDate] = $this->getStartDateEndDate($request, $optionalArgs);
        $period = $this->getPeriod($request);
        [$periodValues, $raw] = $this->getPeriodRelatedData($period, $startDate, $endDate);

        $parentCategories = ServiceRequestCategory::whereNull('parent_id')->pluck('name', 'id')->toArray();
        $serviceRequests = ServiceRequest::selectRaw($raw . ', IF(cat2.id IS NULL, cat1.id, cat2.id) AS category_parent_id')
            ->join('service_request_categories AS cat1', 'service_requests.category_id', '=', 'cat1.id')
            ->leftJoin('service_request_categories AS cat2', 'cat1.parent_id', '=', 'cat2.id')
            ->whereDate('service_requests.created_at', '>=', $startDate->format('Y-m-d'))
            ->whereDate('service_requests.created_at', '<=', $endDate->format('Y-m-d'))
            ->groupBy('period')
            ->groupBy('category_parent_id')
            ->get();

        $ret = $this->formatResponseGropedPeriodAndCol($periodValues, $serviceRequests, 'category_parent_id', $parentCategories);
        $isConvertResponse = $optionalArgs['isConvertResponse'] ?? true;
        return $isConvertResponse
            ? $this->sendResponse($ret, 'Request services statistics formatted successfully')
            : $ret;
    }


    /**
     * @param Request $request
     * @param array $optionalArgs
     * @return mixed
     */
    public function chartRequestByCreationDateByColumn(Request $request, $optionalArgs = [])
    {
        [$startDate, $endDate] = $this->getStartDateEndDate($request, $optionalArgs);
        [$class, $table, $column, $columnValues] = $this->getTableColumnClassByRequest($request, self::PERMITTED_TABLES_FOR_CREATED_DATE);
        $period = $optionalArgs['period'] ?? $this->getPeriod($request);
        [$periodValues, $raw] = $this->getPeriodRelatedData($period, $startDate, $endDate, $table);

        $statistics = $class::selectRaw($raw . ',' . $column . ', count(id) `count`')
            ->whereDate('created_at', '>=', $startDate->format('Y-m-d'))
            ->whereDate('created_at', '<=', $endDate->format('Y-m-d'))
            ->groupBy('period')
            ->groupBy($column)
            ->get();

        $ret = $this->formatResponseGropedPeriodAndCol($periodValues, $statistics, $column, $columnValues);
        $isConvertResponse = $optionalArgs['isConvertResponse'] ?? true;
        return $isConvertResponse
            ? $this->sendResponse($ret, 'Request services statistics formatted successfully fo ' . $table . ' by ' . $column)
            : $ret;
    }

    /**
     * @TODO improve
     *
     * @param Request $request
     * @return mixed
     */
    public function heatRequestByCreationDate(Request $request)
    {
        $date = $request->{self::QUERY_PARAMS['date']} ?? '';
        $date = Carbon::parse($date);
        $period =  $request->{self::QUERY_PARAMS['period']} ?? '';
        $period = in_array($period, self::PERMITTED_HEAT_PERIODS) ? $period : Arr::first(self::PERMITTED_HEAT_PERIODS);

        if (self::WEEK == $period) {
            $startDate = $date->subDays(($date->dayOfWeek - 1));
            $endDate = clone $startDate;
            $endDate = $endDate->addDays(6);
            $raw = "CONCAT(DATE(created_at), ' ',  HOUR(created_at))";
        } else {
//            mean self::YEAR == $period
            $startDate = $date;
            $startDate->setDay(1);
            $startDate->setMonth(1);
            $endDate = clone $startDate;
            $endDate->setDay(31);
            $endDate->setMonth(12);
            $raw = "CONCAT(DAY(created_at), ' ', MONTH(created_at))";
        }

        $statistics = ServiceRequest::selectRaw($raw . " AS `interval`, COUNT(id) AS `count`")
            ->whereDate('created_at', '>=', $startDate->format('Y-m-d'))
            ->whereDate('created_at', '<=', $endDate->format('Y-m-d'))
            ->groupBy('interval')->get();

        if (self::WEEK == $period) {
            $hours = array_combine(range(1, 24), range(1, 24));
        } else {
            $hours = array_combine(range(1, 12), range(1, 12));
        }

        if (self::WEEK == $period) {
            $datePeriod = CarbonPeriod::create($startDate, $endDate);
            $intervalValues = [];
            foreach ($datePeriod as $date) {
                $intervalValues[$date->format('Y-m-d')] = $date->format('l');
            }
        } else {
            $intervalValues = array_combine(range(1, 31), range(1, 31));
        }

        $colStats = $this->initializeServiceRequestCategoriesForChart($hours, array_flip($intervalValues));
        foreach ($statistics as $statistic) {
            $parts = explode(' ', $statistic['interval']);
            $day = $parts[0];
            $y = $parts[1];
            $x = $intervalValues[$day];
            $colStats[$y][$x] = $statistic['count'];
        }

        $response = [];
        foreach ($colStats as $yAxis => $xAxisData) {
            $format = [];
            foreach ($xAxisData as $xAxis => $count) {
                $format[] = [
                    'x' => $xAxis,
                    'y' => $count
                ];
            }

            $response[] = [
                'name' => $yAxis,
                'data' => $format
            ];
        }

        return $this->sendResponse($response, 'Request services statistics formatted successfully');
    }

    /**
     * @param Request $request
     * @param array $optionalArgs
     * @return mixed
     */
    public function chartBuildingsByCreationDate(Request $request, $optionalArgs = [])
    {
        [$startDate, $endDate] = $this->getStartDateEndDate($request, $optionalArgs);
        $period = $optionalArgs['period'] ?? $this->getPeriod($request);
        [$periodValues, $raw] = $this->getPeriodRelatedData($period, $startDate, $endDate, 'buildings');

        $statistics = Building::selectRaw($raw . ', count(id) `count`')
            ->whereDate('created_at', '>=', $startDate->format('Y-m-d'))
            ->whereDate('created_at', '<=', $endDate->format('Y-m-d'))
            ->groupBy('period')
            ->get();


        $dayStatistic = [];
        foreach ($periodValues as $period => $__) {
            $dayStatistic[$period] = 0;
        }

        foreach ($statistics as $statistic) {
            $dayStatistic[$statistic['period']] = $statistic['count'];
        }

        $response['requests_per_day_xdata'] = array_values($periodValues);
        $response['requests_per_day_ydata'] = array_values($dayStatistic);
        $isConvertResponse = $optionalArgs['isConvertResponse'] ?? true;
        return $isConvertResponse
            ? $this->sendResponse($response, 'Building statistics formatted successfully')
            : $response;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function chartRequestByStatus(Request $request)
    {
        $request->merge([
            'table' => 'service_requests',
            'column' => 'status'
        ]);
        return $this->chartRequestByColumn($request);
    }

    /**
     * @param Request $request
     * @param array $optionalArgs
     * @return mixed
     */
    public function chartRequestByColumn(Request $request, $optionalArgs = [])
    {
        [$startDate, $endDate] = $this->getStartDateEndDate($request, $optionalArgs);
        [$class, $table, $column, $columnValues] = $this->getTableColumnClassByRequest($request, self::PERMITTED_TABLES_GROUP);

        $statistics = $class::selectRaw($column . ', count(id) `count`')
            ->whereDate('created_at', '>=', $startDate->format('Y-m-d'))
            ->whereDate('created_at', '<=', $endDate->format('Y-m-d'))
            ->groupBy($column)
            ->orderBy($column)
            ->get();

        $includePercentage = ('service_requests' == $table) ? true : false;
        $response = $this->formatForDonutChart($statistics, $column, $columnValues, $includePercentage);

        $isConvertResponse = $optionalArgs['isConvertResponse'] ?? true;
        return $isConvertResponse
            ? $this->sendResponse($response, 'Admin statistics retrieved successfully for ' . $table . ' for ' . $column)
            : $response;
    }

    /**
     * @param Request $request
     * @param array $optionalArgs
     * @return mixed
     */
    public function chartRequestByRequestStatus(Request $request, $optionalArgs = [])
    {
        [$startDate, $endDate] = $this->getStartDateEndDate($request, $optionalArgs);

        $rsPerStatus = Tenant::selectRaw('`service_requests`.`status`, count(`tenants`.`id`) `count`')
            ->join('service_requests', 'service_requests.tenant_id', 'tenants.id')
            ->whereDate('service_requests.created_at', '>=', $startDate->format('Y-m-d'))
            ->whereDate('service_requests.created_at', '<=', $endDate->format('Y-m-d'))
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        $classStatus = ServiceRequest::Status;
        $response = $this->formatForDonutChart($rsPerStatus, 'status', $classStatus);

        $isConvertResponse = $optionalArgs['isConvertResponse'] ?? true;
        return $isConvertResponse
            ? $this->sendResponse($response, 'Admin statistics retrieved successfully for tenants')
            : $response;
    }

    /**
     * @return mixed
     */
    protected function chartLoginDevice()
    {
        $loginDevices = LoginDevice::get(['mobile', 'desktop', 'tablet']);
        $mobileLoginCount = $loginDevices->where('mobile', 1)->count();
        $desktopLoginCount = $loginDevices->where('desktop', 1)->count();
        $tabletLoginCount = $loginDevices->where('tablet', 1)->count();

        $statistics = collect([
            [
                'login' => 1,
                'count' => $desktopLoginCount,
            ],
            [
                'login' => 2,
                'count' => $tabletLoginCount,
            ],
            [
                'login' => 3,
                'count' => $mobileLoginCount,
            ],
        ]);
        $values = [
            1 => 'Desktop',
            2 => 'Tablet',
            3 => 'mobile',
        ];

        return $this->formatForDonutChart($statistics, 'login', $values, true);
    }

    /**
     * @param $periodValues
     * @param $statistics
     * @param $column
     * @param $columnValues
     * @return mixed
     */
    protected function formatResponseGropedPeriodAndCol($periodValues, $statistics, $column, $columnValues)
    {
        $colStats = $this->initializeServiceRequestCategoriesForChart($columnValues, $periodValues);
        foreach ($statistics as $statistic) {
            $value = $columnValues[$statistic[$column]] ?? '';
            $colStats[$value][$statistic['period']] = $statistic['count'];
        }

        $formattedReqStatistics = [];
        foreach($colStats as $key=>$value){
            $formattedReqStatistics[] = [
                'name' => $key,
                'data' => array_values($value)
            ];
        }

        $ret['requests_per_day_xdata'] = array_values($periodValues);
        $ret['requests_per_day_ydata'] = $formattedReqStatistics;
        return $ret;
    }

    /**
     * @param $statistics
     * @param $column
     * @param $columnValues
     * @param bool $includePercentage
     * @return mixed
     */
    protected function formatForDonutChart($statistics, $column, $columnValues, $includePercentage = false)
    {
        $existingStatuses = $statistics->pluck($column)->all();
        foreach ($columnValues as $value => $__) {
            if (! in_array($value, $existingStatuses)) {
                $stat[$column] = $value;
                $stat['count'] = 0;
                $statistics->push($stat);
            }
        }

        $response['labels'] = $statistics->map(function($el) use ($columnValues, $column) {
            return $columnValues[$el[$column]];
        });

        $response['ids'] = $statistics->map(function($el) use ($columnValues, $column) {
            return $el[$column];
        });

        $response['data'] = $statistics->map(function($el) {
            return $el['count'];
        });

        if ($includePercentage) {
            $sum = $response['data']->sum();
            $response['tag_percentage'] = $this->getTagPercentage($statistics, $sum);
        }

        return $response;
    }

    /**
     * @param $rsPerStatus
     * @param $sum
     * @return mixed
     */
    protected function getTagPercentage($rsPerStatus, $sum)
    {
        if (0 == $sum) {
            return 0;
        }
        
        $tagPercentages = $rsPerStatus->map(function($el) use ($sum) {
            return round($el['count']  * 100 / $sum);
        });

        $sumPercentage = $tagPercentages->sum();

        if ($sumPercentage != 100) {
            // @TODO improve this logic if need for make round max correct way
            $diff = $rsPerStatus->map(function($el, $index) use ($sum, $tagPercentages) {
                return $el['count']  * 100 / $sum - $tagPercentages[$index];
            });
            $diff = $diff->sort();

            $difference = abs(100 - $sumPercentage);
            $sign = (100 - $sumPercentage > 0) ? 1 : -1;

            for ($i = 0; $i < $difference; $i++) {
                $key = $diff->keys()->last();
                $tagPercentages[$key] = $tagPercentages[$key] + $sign * 1;
                $diff->pop();
            }
        }

        return $tagPercentages;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function chartRequestByCategory(Request $request)
    {
        [$startDate, $endDate] = $this->getStartDateEndDate($request);
        $parentCategories = ServiceRequestCategory::whereNull('parent_id')->pluck('name', 'id');
        $serviceRequests = ServiceRequest::selectRaw('count(service_requests.id) as count, IF(cat2.id IS NULL, cat1.id, cat2.id) AS category_parent_id')
            ->join('service_request_categories AS cat1', 'service_requests.category_id', '=', 'cat1.id')
            ->leftJoin('service_request_categories AS cat2', 'cat1.parent_id', '=', 'cat2.id')
            ->whereDate('service_requests.created_at', '>=', $startDate->format('Y-m-d'))
            ->whereDate('service_requests.created_at', '<=', $endDate->format('Y-m-d'))
            ->groupBy('category_parent_id')
            ->get();

        $statisticData = $parentCategories->values()->flip();
        foreach ($statisticData as $category => $__) {
            $statisticData[$category] = 0;
        }

        foreach ($serviceRequests as $serviceRequest) {
            $category = $parentCategories[$serviceRequest->category_parent_id];
            $statisticData[$category] = $serviceRequest->count;
        }

        $response = [
            'labels' => $statisticData->keys(),
            'data' => $statisticData->values()
        ];

        return $this->sendResponse($response, 'Admin statistics retrieved successfully');
    }

    /**
     * @param $table
     * @param null $startDate
     * @param null $endDate
     * @return mixed
     */
    public function getDayCountStatistic($table, $startDate = null, $endDate = null)
    {
        return \DB::table($table)->selectRaw ('date(created_at) `x`, count(id) `y`')
            ->whereDate('created_at', '>=', $startDate->format('Y-m-d'))
            ->whereDate('created_at', '<=', $endDate->format('Y-m-d'))
            ->groupBy('x')
            ->orderBy('x')
            ->get();
    }

    /**
     * @param $parentCategories
     * @param $periodValues
     * @return array
     */
    public function initializeServiceRequestCategoriesForChart($parentCategories, $periodValues)
    {
        $categoryDayStatistic = [];

        foreach($parentCategories as $category){
            foreach ($periodValues as $period => $__) {
                $categoryDayStatistic[$category][$period] = 0;
            }
        }

        return $categoryDayStatistic;
    }

    /**
     * @param $period
     * @param $startDate
     * @param $endDate
     * @param string $table
     * @return array
     */
    protected function getPeriodRelatedData($period, $startDate, $endDate, $table = 'service_requests')
    {
        $periodValues = [];

        if (self::YEAR == $period) {
            $part = "YEAR(" . $table . ".created_at)";
            $startDate->setMonth(1)->setDay(1);
            $endDate->setMonth(12)->setDay(31);
            $currentDate = clone $startDate;

            while ($currentDate < $endDate) {
                $periodValues[$currentDate->year] = $currentDate->year;
                $currentDate->addYear();
            }

        } elseif (self::MONTH == $period) {
            $part = "CONCAT(YEAR(" . $table . ".created_at), ' ', MONTH(" . $table . ".created_at))";
            $startDate->setDay(1);
            $endDate->addMonth()->setDay(1)->subDay();

            $currentDate = clone $startDate;
            while ($currentDate < $endDate) {
                $yearMonth = $currentDate->year . ' ' . $currentDate->month;
                $periodValues[$yearMonth] = $currentDate->format('M Y');
                $currentDate->addMonth();
            }
        } elseif (self::WEEK == $period) {

            if ($startDate->dayOfWeek) {
                $startDate = $startDate->subDays($startDate->dayOfWeek);
            }
            if (6 != $endDate->dayOfWeek) {
                $endDate = $endDate->addDays(6 - $endDate->dayOfWeek);
            }
            // @TODO check statistics when WEEK(created_at) = 1, 52, 53 maybe can income some incorrect data
            $part = "CONCAT(YEAR(" . $table . ".created_at), ' ', WEEK(" . $table . ".created_at))";
            $currentDate = clone $startDate;
            $today = now();

            while ($currentDate < $endDate) {
                $yearWeek = $currentDate->year . ' ' . $currentDate->week;
                $periodValues[$yearWeek] = ($currentDate->year != $today->year)
                    ? $currentDate->week . ' ' . $currentDate->year
                    : $currentDate->week;
                $currentDate->addWeek();
            }

        } else {
            $part = "DATE(" . $table . ".created_at)";
            $datePeriod = CarbonPeriod::create($startDate, $endDate);
            foreach ($datePeriod as $date) {
                $periodValues[$date->format('Y-m-d')] = $date->format('Y-m-d');
            }
        }

        $raw = sprintf("count(" . $table . ".id) as count, %s as period", $part);


        return [$periodValues, $raw];
    }

    /**
     * @param $request
     * @param array $optionalArgs
     * @return array
     */
    protected function getStartDateEndDate($request, $optionalArgs = [])
    {
        if (key_exists('startDate', $optionalArgs) && key_exists('endDate', $optionalArgs)) {
            return [$optionalArgs['startDate'], $optionalArgs['endDate']];
        }

        $requestData = $request->all();
        $startDate = $requestData[self::QUERY_PARAMS['start_date']] ?? '';
        $endDate = $requestData[self::QUERY_PARAMS['end_date']] ?? '';

        if (empty($startDate) && empty($endDate)) {
            $endDate = now();
            $startDate = now()->subMonth();
        } elseif (empty($startDate)) {
            $endDate = Carbon::parse($endDate);
            $startDate = clone $endDate;
            $startDate->subMonth();
        } elseif (empty($endDate)) {
            $startDate = Carbon::parse($startDate);
            $endDate = now();
        } else {
            $endDate = Carbon::parse($endDate);
            $startDate = Carbon::parse($startDate);
        }

        return [$startDate, $endDate];
    }

    /**
     * @param $request
     * @return string
     */
    protected function getPeriod($request)
    {
        $period = $request->{self::QUERY_PARAMS['period']} ?? self::DEFAULT_PERIOD;
        return in_array($period, self::PERMITTED_PERIODS) ? $period : self::DEFAULT_PERIOD;
    }

    /**
     * @TODO rename
     * @param $request
     * @param $permissions
     * @return array
     */
    protected function getTableColumnClassByRequest($request, $permissions)
    {
        $table = $optionalArgs['table'] ?? null;
        $table = $table ?? $request->{self::QUERY_PARAMS['table']};
        $table = key_exists($table, $permissions) ? $table : Arr::first(array_keys($permissions));

        $permittedColumns = $permissions[$table]['columns'];
        $column = $optionalArgs['column'] ?? null;
        $column = $column ?? $request->{self::QUERY_PARAMS['column']};
        $column = in_array($column, $permittedColumns) ? $column : Arr::first($permittedColumns);
        $class = $permissions[$table]['class'];
        $columnValues = constant($class . "::" . ucfirst($column));

        return [$class, $table, $column, $columnValues];
    }
}
