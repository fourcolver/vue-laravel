<?php

namespace App\Criteria\Units;

use App\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByOpenRequestsCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterByOpenRequestsCriteria implements CriteriaInterface
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
        $request = $this->request->get('request', null);
        if ($request == 1) {
            return $model->whereHas('tenant.requests', function ($query) {
                $query->where('status', '<', ServiceRequest::StatusDone);
            });
        }

        return $model;
    }
}
