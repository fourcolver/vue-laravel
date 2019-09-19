<?php

namespace App\Criteria\Products;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByTypeCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterByTypeCriteria implements CriteriaInterface
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
        $type = $this->request->get('type', null);
        if ($type) {
            if (!is_array($type)) {
                $type = [$type];
            }

            $model->whereIn('products.type', $type);
        }

        return $model;
    }
}
