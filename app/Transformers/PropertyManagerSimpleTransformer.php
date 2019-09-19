<?php

namespace App\Transformers;

use App\Models\PropertyManager;

/**
 * Class PropertyManagerSimpleTransformer.
 *
 * @package namespace App\Transformers;
 */
class PropertyManagerSimpleTransformer extends BaseTransformer
{
    protected $defaultIncludes = [
//        'user',
    ];

    /**
     * Transform the PropertyManager entity.
     *
     * @param PropertyManager $model
     *
     * @return array
     */
    public function transform(PropertyManager $model)
    {
        $response = [
            'id' => $model->id,
            'property_manager_format' => $model->property_manager_format,
            'slogan' => $model->slogan,
        ];

        if ($model->user) {
            $response['user'] = (new UserTransformer)->transform($model->user);
        }

        return $response;
    }

    /**
     * Include Address
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeUser(PropertyManager $model)
    {
        return $this->item($model->user, new UserTransformer);
    }
}
