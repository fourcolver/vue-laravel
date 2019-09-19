<?php

namespace App\Criteria\Units;

use App\Models\ServiceRequest;
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
     * @param Builder|Model $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     * @throws \Exception
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $building_id = $this->request->get('building_id', null);
        if ($building_id) {
            return $model->where('building_id', (int)$building_id);
        }

        $district_id = $this->request->get('district_id', null);
        if ($district_id) {
            return $model->whereHas('building', function ($query) use ($district_id) {
                $query->where('district_id', (int)$district_id);
            });
        }

        $state_id = $this->request->get('state_id', null);
        if ($state_id) {
            return $model->whereHas('building.address', function ($query) use ($state_id) {
                $query->where('state_id', (int)$state_id);
            });
        }

        $request = $this->request->get('request', null);
        if ($request == 1) {
            return $model->whereHas('tenant.requests', function ($query) {
                $query->where('status', '<', ServiceRequest::StatusDone);
            });
        }

        $manager_id = $this->request->get('manager_id', null);
        if ($manager_id) {
            return $model->whereHas('building.propertyManagers', function ($q) use ($manager_id) {
                $q->where('building_property_manager.id', $manager_id);
            });
        }

        return $model;
    }
}
