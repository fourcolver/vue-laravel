<?php

namespace App\Repositories;

use App\Models\TemplateCategory;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TemplateCategoryRepository
 * @package App\Repositories
 * @version April 1, 2019, 8:45 am UTC
 */
class TemplateCategoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'name',
        'description'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TemplateCategory::class;
    }
}
