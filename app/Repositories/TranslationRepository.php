<?php

namespace App\Repositories;

use App\Models\Translation;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TranslationRepository
 * @package App\Repositories
 * @version April 7, 2019, 12:06 pm UTC
 */
class TranslationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'object_type',
        'object_id',
        'language',
        'name',
        'value'
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
        return Translation::class;
    }
}
