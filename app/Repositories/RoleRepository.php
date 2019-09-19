<?php

namespace App\Repositories;

use App\Models\Role;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class RoleRepository
 * @package App\Repositories
 * @version January 11, 2019, 12:27 pm UTC
 *
 * @method Role findWithoutFail($id, $columns = ['*'])
 * @method Role find($id, $columns = ['*'])
 * @method Role first($columns = ['*'])
*/
class RoleRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'email',
        'phone'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Role::class;
    }

    /**
     * Configure role by name
     **/
    public function getRoleByName(string $name)
    {
        $role = $this->model::where('name',  $name)->first();
        if (!$role) {
            return  $this->model::where('name',  'registered')->first();
        }

        return $role;
    }
}
