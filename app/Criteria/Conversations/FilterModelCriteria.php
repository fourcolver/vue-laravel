<?php

namespace App\Criteria\Conversations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterModelCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterModelCriteria implements CriteriaInterface
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
        if ($this->request->id) {
            $model->where('conversationable_id', $this->request->id);
        }
        if ($this->request->conversationable) {
            $model->where('conversationable_type', $this->request->conversationable);
        }

        return $model->ofLoggedInUser();
    }
}
