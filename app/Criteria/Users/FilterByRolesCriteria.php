<?php

namespace App\Criteria\Users;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByRolesCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterByRolesCriteria implements CriteriaInterface
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
        $role = $this->request->get('role', null);
        $roles = $this->request->roles;

        if ($role) {
            $model->withRole($role);
        }

        if (is_array($roles)) {
            $model->withRoles($roles);
        }

        return $model;
    }
}
