<?php

namespace App\Transformers;

use App\Models\Tenant;

/**
 * Class TenantSimpleTransformer.
 *
 * @package namespace App\Transformers;
 */
class TenantSimpleTransformer extends BaseTransformer
{
    protected $defaultIncludes = [
//        'user',
    ];

    /**
     * Transform the Tenant entity.
     *
     * @param Tenant $model
     *
     * @return array
     */
    public function transform(Tenant $model)
    {
        $response = [
            'id' => $model->id,
            'title' => $model->title,
            'company' => $model->company,
            'first_name' => $model->first_name,
            'last_name' => $model->last_name,
            'birth_date' => $model->birth_date->format('Y-m-d'),
            'mobile_phone' => $model->mobile_phone,
            'private_phone' => $model->private_phone,
            'work_phone' => $model->work_phone,
            'status' => $model->status,
            'tenant_format' => $model->tenant_format,
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
    public function includeUser(Tenant $model)
    {
        return $this->item($model->user, new UserTransformer);
    }
}
