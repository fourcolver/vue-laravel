<?php

namespace App\Criteria\Tenants;

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
     * @param         Builder|Model     $model
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
        
        return $model;     
    }  
}
