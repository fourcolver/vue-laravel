<?php

namespace App\Criteria\ServiceProviders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use App\Models\Post;

/**
 * Class FilterByPostCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterByPostCriteria implements CriteriaInterface
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
        $post_id = $this->request->get('post_id', null);
        if (!$post_id) { return $model; }

        $model->join('post_service_provider', 'post_service_provider.service_provider_id', '=', 'service_providers.id')
            ->where('post_service_provider.post_id', $post_id);

        return $model;
    }
}
