<?php

namespace App\Transformers;

use App\Models\ServiceRequestCategory;

/**
 * Class ServiceRequestCategorySimpleTransformer.
 *
 * @package namespace App\Transformers;
 */
class ServiceRequestCategorySimpleTransformer extends BaseTransformer
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
        ];

        if ($model->parent_id > 0 && $model->parentCategory) {
            $response['parentCategory'] = $this->transform($model->parentCategory);
        }

        return $response;
    }
}
