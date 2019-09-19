<?php

namespace App\Criteria\Districts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class ExcludeIdsCriteria
 * @package Prettus\Repository\Criteria
 */
class ExcludeIdsCriteria implements CriteriaInterface
{
    /**
     * @var Request
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
        $ids = $this->request->get('exclude_ids', null);
        if ($ids) {
            if (!is_array($ids)) {
                $ids = [$ids];
            }

            $model->whereNotIn('districts.id', $ids);
        }

        return $model;
    }
}
