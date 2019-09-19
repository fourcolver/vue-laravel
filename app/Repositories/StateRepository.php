<?php

namespace App\Repositories;

use App\Models\State;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class StateRepository
 * @package App\Repositories
 * @version January 26, 2019, 9:48 pm UTC
 *
 * @method State findWithoutFail($id, $columns = ['*'])
 * @method State find($id, $columns = ['*'])
 * @method State first($columns = ['*'])
*/
class StateRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'code',
        'name',
        'name_de',
        'name_fr',
        'name_it',
        'name_rm'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return State::class;
    }
}
