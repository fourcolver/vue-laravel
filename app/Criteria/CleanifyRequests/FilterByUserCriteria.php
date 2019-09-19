<?php

namespace App\Criteria\CleanifyRequests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByUserCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterByUserCriteria implements CriteriaInterface
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
        $user_id = $this->request->get('user_id', null);
        if (!$this->request->user()->can('list-cleanify_request')) {
            $user_id = $this->request->user()->id;
        }
        if ($user_id) {
            $model->where('cleanify_requests.user_id', $user_id);
        }

        return $model;
    }
}
