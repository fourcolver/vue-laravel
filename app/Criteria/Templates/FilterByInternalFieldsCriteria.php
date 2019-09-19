<?php

namespace App\Criteria\Templates;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByInternalFieldsCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterByInternalFieldsCriteria implements CriteriaInterface
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * FilterByInternalFieldsCriteria constructor.
     * @param Request $request
     */
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
        $showSystem = $this->request->get('showSystem', false);
        if ($showSystem) {
            $model->whereIn('system', $showSystem);
        }

        return $model;
    }
}
