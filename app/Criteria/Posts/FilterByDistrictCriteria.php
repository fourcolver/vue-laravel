<?php

namespace App\Criteria\Posts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByDistrictCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterByDistrictCriteria implements CriteriaInterface
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
        $district_id = $this->request->get('district_id', null);
        if (!$district_id) {
            return $model;
        }

        $u = \Auth::user();
        if (!$u->can('list-post') && $u->tenant) {
            $district_id = $u->tenant->building->district_id;
        }

        $model->whereHas('districts', function ($query) use ($district_id) {
            $query->where('id', $district_id);
        });

        return $model;
    }
}
