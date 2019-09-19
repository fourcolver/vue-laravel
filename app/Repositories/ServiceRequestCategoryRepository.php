<?php

namespace App\Repositories;

use App\Models\ServiceRequestCategory;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ServiceRequestCategoryRepository
 * @package App\Repositories
 * @version February 27, 2019, 2:18 pm UTC
 *
 * @method ServiceRequestCategory findWithoutFail($id, $columns = ['*'])
 * @method ServiceRequestCategory find($id, $columns = ['*'])
 * @method ServiceRequestCategory first($columns = ['*'])
*/
class ServiceRequestCategoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id' => 'like',
        'parent_id' => 'like',
        'name' => 'like',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ServiceRequestCategory::class;
    }
}
