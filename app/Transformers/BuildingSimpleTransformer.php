<?php

namespace App\Transformers;

use App\Models\Building;

/**
 * Class BuildingSimpleTransformer.
 *
 * @package namespace App\Transformers;
 */
class BuildingSimpleTransformer extends BaseTransformer
{
    /**
     * Transform the Building entity.
     *
     * @param \App\Models\Building $model
     *
     * @return array
     */
    public function transform(Building $model)
    {
        $response = [
            'id' => $model->id,
            'building_format' => $model->building_format,
            'name' => $model->name,
            'label' => $model->label,
            'description' => $model->description,
            'floor_nr' => $model->floor_nr,
            'basement' => $model->basement,
            'attic' => $model->attic,
            'created_at' => $model->created_at->format('Y-m-d'),
        ];

        if ($model->district) {
            $response['district'] = (new DistrictTransformer)->transform($model->district);
        }

        return $response;
    }
}
