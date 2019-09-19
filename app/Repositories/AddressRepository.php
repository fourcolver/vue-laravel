<?php

namespace App\Repositories;

use App\Models\Address;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AddressRepository
 * @package App\Repositories
 * @version January 27, 2019, 7:35 pm UTC
 *
 * @method Address findWithoutFail($id, $columns = ['*'])
 * @method Address find($id, $columns = ['*'])
 * @method Address first($columns = ['*'])
*/
class AddressRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'city',
        'street',
        'street_nr',
        'zip'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Address::class;
    }
    public function create(array $attributes)
    {
        if (isset($attributes['state'])) {
            $attributes['state_id'] = $attributes['state']['id'];
            unset($attributes['state']);
        }
        unset($attributes['country']);

        // Have to skip presenter to get a model not some data
        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);
        $model = parent::create($attributes);
        $this->skipPresenter($temporarySkipPresenter);

        $model = $this->updateRelations($model, $attributes);
        $model->save();

        return $this->parserResult($model);
    }

    public function update(array $attributes, $id)
    {
        if (isset($attributes['state'])) {
            $attributes['state_id'] = $attributes['state']['id'];
            unset($attributes['state']);
        }
        unset($attributes['country']);

        // Have to skip presenter to get a model not some data
        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);
        $model = parent::update($attributes, $id);
        $this->skipPresenter($temporarySkipPresenter);

        $model = $this->updateRelations($model, $attributes);
        $model->save();

        return $this->parserResult($model);
    }
}
