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
class FilterByUnitCriteria implements CriteriaInterface
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
        $unit_id = $this->request->get('unit_id', null);
        if ($unit_id) {
            return $model->where('unit_id', (int)$unit_id);
        }
        
        return $model;     
    }  
}
