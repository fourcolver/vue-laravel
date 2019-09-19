<?php

namespace App\Transformers;

use App\Models\ServiceRequestCategory;

/**
 * Class ServiceRequestCategoryTransformer.
 *
 * @package namespace App\Transformers;
 */
class ServiceRequestCategoryTransformer extends BaseTransformer
{
    /**
     * Transform the ServiceRequestCategory entity.
     *
     * @param \App\Models\ServiceRequestCategory $model
     *
     * @return array
     */
    public function transform(ServiceRequestCategory $model)
    {
        $response = [
            'id' => $model->id,
            'parent_id' => $model->parent_id,
            'name' => $model->name,
            'description' => $model->description,
            'has_qualifications' => $model->has_qualifications,
        ];

        if ($model->categories) {
            $response['categories'] = $this->transformCollection($model->categories);
        }

        return $response;
    }
}
