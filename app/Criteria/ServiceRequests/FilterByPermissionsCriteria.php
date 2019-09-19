<?php

namespace App\Criteria\ServiceRequests;

use App\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByPermissionsCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterByPermissionsCriteria implements CriteriaInterface
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply criteria in query repository
     *
     * @param         Builder|Model $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     * @throws \Exception
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $u = $this->request->user();
        $qs = [
            '(service_requests.visibility = ? and service_requests.tenant_id = ?)',
            '(service_requests.visibility = ? and units.building_id = ?)',
            '(service_requests.visibility = ? and buildings.district_id = ?)',
        ];

        if ($u->hasRole('registered') && $u->tenant) {
            $model->select('service_requests.*')
                ->join('units', 'units.id', '=', 'service_requests.unit_id')
                ->join('buildings', 'units.building_id', '=', 'buildings.id');
            $vs = [
                ServiceRequest::VisibilityTenant, $u->tenant->id,
                ServiceRequest::VisibilityBuilding, $u->tenant->building_id,
                ServiceRequest::VisibilityDistrict, $u->tenant->building->district_id,
            ];
            return $model->whereRaw('(' . implode(' or ', $qs) . ')', $vs);
        }

        if ($u->hasRole('service') && $u->serviceProvider) {
            $model->leftJoin('request_provider', 'request_provider.request_id', '=', 'service_requests.id')
                ->where('request_provider.provider_id', $u->serviceProvider->id);
        }

        return $model;
    }
}
