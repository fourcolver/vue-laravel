<?php

namespace App\Repositories;

use App\Models\Building;
use App\Models\District;
use App\Models\PropertyManager;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PropertyManagerRepository
 * @package App\Repositories
 * @version March 8, 2019, 9:38 pm UTC
 */
class PropertyManagerRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description' => 'like',
        'first_name' => 'like',
        'last_name' => 'like',
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
        return PropertyManager::class;
    }

    public function create(array $attributes)
    {
        // Have to skip presenter to get a model not some data
        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);
        $model = parent::create($attributes);
        $this->skipPresenter($temporarySkipPresenter);

        $model = $this->updateRelations($model, $attributes);
        $model->save();

        if (isset($attributes['buildings'])) {
            $model->buildings()->sync($attributes['buildings']);
        }

        return $this->parserResult($model);
    }

    public function update(array $attributes, $id)
    {
        unset($attributes['districts']);
        // Have to skip presenter to get a model not some data
        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);

        $model = parent::update($attributes, $id);
        $this->skipPresenter($temporarySkipPresenter);

        $model = $this->updateRelations($model, $attributes);
        $model->save();

        if (isset($attributes['buildings'])) {
            $model->buildings()->sync($attributes['buildings']);
        }

        return $this->parserResult($model);
    }

    public function delete($id)
    {
        $this->applyScope();

        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);

        $model = $this->find($id);

        $this->skipPresenter($temporarySkipPresenter);
        $this->resetModel();

        $model->buildings()->detach();

        $deleted = $model->delete();

        return $deleted;
    }

    public function assignments(PropertyManager $propertyManager)
    {
        $buildings = Building::select(\DB::raw('buildings.id, buildings.name, "building" as type'))
            ->join('building_property_manager', 'building_id', '=', 'buildings.id')
            ->where('building_property_manager.property_manager_id', $propertyManager->id);

        $districts = District::select(\DB::raw('districts.id, districts.name, "district" as type'))
            ->join('district_property_manager', 'district_id', '=', 'districts.id')
            ->where('district_property_manager.property_manager_id', $propertyManager->id);

        return $buildings->union($districts)->orderBy('name');
    }

    public function assignmentsWithIds(array $ids)
    {
        $buildings = Building::select(\DB::raw('buildings.id, buildings.name, "building" as type'))
            ->join('building_property_manager', 'building_id', '=', 'buildings.id')
            ->whereIn('building_property_manager.property_manager_id', $ids);
        return $buildings;
    }
}
