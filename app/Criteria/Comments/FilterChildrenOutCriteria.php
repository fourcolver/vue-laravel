<?php

namespace App\Criteria\Comments;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use App\Models\Comment;

/**
 * Class FilterChildrenOutCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterChildrenOutCriteria implements CriteriaInterface
{
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
        return $model->where('parent_id', null);
    }
}
