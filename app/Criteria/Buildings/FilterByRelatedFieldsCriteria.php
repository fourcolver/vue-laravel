<?php

namespace App\Criteria\Buildings;

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
        $state_id = $this->request->get('state_id', null);
        if ($state_id) {
            return $model->whereHas('address', function ($q) use ($state_id) {
                $q->where('state_id', $state_id);
            });
        }

        $district_id = $this->request->get('district_id', null);
        if ($district_id) {
            $model->where('district_id', $district_id);
        }

        $manager_id = $this->request->get('manager_id', null);
        if ($manager_id) {
            return $model->whereHas('propertyManagers', function ($q) use ($manager_id) {
                $q->where('building_property_manager.id', $manager_id);
            });
        }

        $request_status = $this->request->get('request_status', null);
        if ($request_status) {
            return $model->whereHas('tenants.requests', function ($query) use ($request_status) {
                $query->where('status', $request_status);
            });
        }

        return $model;
    }
}
