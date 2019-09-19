<?php

namespace App\Transformers;

use App\Models\District;

/**
 * Class DistrictTransformer.
 *
 * @package namespace App\Transformers;
 */
class DistrictTransformer extends BaseTransformer
{
    /**
     * Transform the DistrictT entity.
     *
     * @param \App\Models\District $model
     *
     * @return array
     */
    public function transform(District $model)
    {
        $response = [
            'id' => $model->id,
            'name' => $model->name,
            'district_format' => $model->district_format,
            'description' => $model->description,
        ];

        return $response;
    }
}
