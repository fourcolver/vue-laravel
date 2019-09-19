<?php

namespace App\Criteria\Tenants;

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
     * @param         Builder|Model     $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     * @throws \Exception
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $did = $this->request->get('district_id', null);
        if ($did) {
            $model->join('buildings', 'buildings.id', '=', 'tenants.building_id')
                ->where('buildings.district_id', $did);
        }

        return $model;
    }
}
