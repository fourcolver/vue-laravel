<?php

namespace App\Repositories;

use App\Models\Unit;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class UnitRepository
 * @package App\Repositories
 * @version January 28, 2019, 8:32 am UTC
 *
 * @method Unit findWithoutFail($id, $columns = ['*'])
 * @method Unit find($id, $columns = ['*'])
 * @method Unit first($columns = ['*'])
*/
class UnitRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name' => 'like',
        'description' => 'like',
        'floor' => 'like',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Unit::class;
    }

    public function getTotalUnitsFromBuilding(int $building_id)
    {
        return $this->model->where('building_id', $building_id)->count();
    }

    public function update(array $attrs, $id)
    {
        unset($attrs['building']);
        return parent::update($attrs, $id);
    }

    public function getUnitsIdwithBuildingIds($ids) {
        return $this->model->whereIn('building_id', $ids)->get('id');
    }

    public function deleteUnitWithBuilding($ids) {
        return $this->model->whereIn('building_id', $ids)->delete();
    }

    /**
     * @return mixed
     */
    public function count()
    {
        return $this->model->count();
    }
}
