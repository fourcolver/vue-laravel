<?php

namespace App\Repositories;

use App\Models\District;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DistrictRepository
 * @package App\Repositories
 * @version February 21, 2019, 9:27 pm UTC
 *
 * @method District findWithoutFail($id, $columns = ['*'])
 * @method District find($id, $columns = ['*'])
 * @method District first($columns = ['*'])
 */
class DistrictRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name' => 'like',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return District::class;
    }
}
