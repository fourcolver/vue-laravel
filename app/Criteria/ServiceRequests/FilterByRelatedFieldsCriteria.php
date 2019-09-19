<?php

namespace App\Criteria\ServiceRequests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByRelatedFieldsCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterByRelatedFieldsCriteria implements CriteriaInterface
{
    /**
     * @var Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply criteria in query repository
     *
     * @param Builder|Model $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     * @throws \Exception
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $categoryId = $this->request->get('category_id', null);
        if ($categoryId) {
            $model->where('category_id', $categoryId);
        }

        $tenantId = $this->request->get('tenant_id', null);
        if ($tenantId) {
            $model->where('tenant_id', $tenantId);
        }

        $unitId = $this->request->get('unit_id', null);
        if ($unitId) {
            $model->where('unit_id', $unitId);

            $model->whereHas('category', function ($q) {
                $q->where('name', 'apartment');
            });
        }

        $providerId = $this->request->get('service_id', null);
        if ($providerId) {
            $model->join('request_provider', 'service_requests.id', '=', 'request_provider.request_id')
                  ->where('request_provider.provider_id', $providerId);
        }

        $assigneeId = $this->request->get('assignee_id', null);
        if ($assigneeId) {
            $model->join('request_assignee', 'service_requests.id', '=', 'request_assignee.request_id')
                  ->where('request_assignee.user_id', $assigneeId);
        }

        $buildingId = $this->request->get('building_id', null);
        if ($buildingId) {
            $model->whereHas('tenant', function ($query) use ($buildingId) {
                $query->where('building_id', $buildingId);
            });
        }

        $districtId = $this->request->get('district_id', null);
        if ($districtId) {
            $model->whereHas('tenant.building', function ($query) use ($districtId) {
                $query->where('district_id', $districtId);
            });
        }

        return $model;
    }
}
