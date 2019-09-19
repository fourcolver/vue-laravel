<?php

namespace App\Criteria\Posts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FeedCriteria
 * @package Prettus\Repository\Criteria
 */
class FeedCriteria implements CriteriaInterface
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
        if ($this->request->get('feed')) {
            return $model
                ->whereRaw("(posts.pinned = ? or (posts.pinned_to is not null and posts.pinned_to > now()))", false)
                ->orderBy('posts.pinned', 'desc')
                ->orderBy('posts.execution_start', 'asc')
                ->orderBy('posts.published_at', 'desc')
                ->orderBy('posts.created_at', 'desc');
        }

        return $model->orderBy('posts.created_at', 'desc');
    }
}
