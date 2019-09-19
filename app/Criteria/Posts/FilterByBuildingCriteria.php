<?php

namespace App\Criteria\Posts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByBuildingCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterByBuildingCriteria implements CriteriaInterface
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
        if (!$building_id) {
            return $model;
        }

        $u = \Auth::user();
        if (!$u->can('list-post') && $u->tenant) {
            $building_id = $u->tenant->building_id;
        }

        $model->whereHas('buildings', function ($query) use ($building_id) {
            $query->where('id', $building_id);
        });

        return $model;
    }
}
