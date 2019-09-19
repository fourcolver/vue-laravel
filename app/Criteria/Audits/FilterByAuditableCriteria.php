<?php

namespace App\Criteria\Audits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByAuditableCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterByAuditableCriteria implements CriteriaInterface
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
        if ($t = $this->request->get('auditable_type', null)) {
            $model->where('auditable_type', $t);
        }
        if ($i = $this->request->get('auditable_id', null)) {
            $model->where('auditable_id', $i);
        }
        return $model;
    }
}
