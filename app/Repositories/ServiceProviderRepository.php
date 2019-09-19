<?php

namespace App\Repositories;

use App\Models\ServiceProvider;
use App\Models\Building;
use App\Models\District;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ServiceProviderRepository
 * @package App\Repositories
 * @version February 14, 2019, 9:18 pm UTC
 *
 * @method ServiceProvider findWithoutFail($id, $columns = ['*'])
 * @method ServiceProvider find($id, $columns = ['*'])
 * @method ServiceProvider first($columns = ['*'])
 */
class ServiceProviderRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'category' => 'like',
        'name' => 'like',
        'email' => 'like',
        'phone' => 'like',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ServiceProvider::class;
    }

    public function create(array $attributes)
    {
        if (isset($attributes['user'])) {
            unset($attributes['user']);
        }

        if (isset($attributes['address'])) {
            unset($attributes['address']);
        }

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
        if (isset($attributes['unit_id'])) {
            $unit = Unit::with('building')->find($attributes['unit_id']);
            if ($unit) {
                $attributes['building_id'] = $unit->building_id;
                $attributes['unit_id'] = $unit->id;
                $attributes['address_id'] = $unit->building->address_id;
            }
            unset($attributes['unit']);
        }

        if (isset($attributes['address'])) {
            unset($attributes['address']);
        }

        if (isset($attributes['building'])) {
            unset($attributes['building']);
        }

        if (isset($attributes['user'])) {
            unset($attributes['user']);
        }

        // Have to skip presenter to get a model not some data
        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);

        $model = parent::update($attributes, $id);
        $this->skipPresenter($temporarySkipPresenter);

        $model = $this->updateRelations($model, $attributes);
        $model->save();

        return $this->parserResult($model);
    }

    public function locations(ServiceProvider $sp)
    {
        // Cannot use $sp->buildings() and $sp->districts() because of a bug
        // related to different number of columns in union
        $spbs = Building::select(\DB::raw('id, name, "building" as type'))
            ->join('building_service_provider', 'building_service_provider.building_id', '=', 'id')
            ->where('building_service_provider.service_provider_id', $sp->id);
        $spds = District::select(\DB::raw('id, name, "district" as type'))
            ->join('district_service_provider', 'district_service_provider.district_id', '=', 'id')
            ->where('district_service_provider.service_provider_id', $sp->id);

        return $spbs->union($spds);
    }
}
