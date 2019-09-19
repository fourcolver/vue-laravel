<?php

namespace App\Transformers;

use App\Models\ServiceProvider;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class ServiceProviderTransformer
 *
 * @package namespace App\Transformers;
 */
class ServiceProviderTransformer extends BaseTransformer
{
    /**
     * Transform the ServiceProvider entity.
     *
     * @param \App\Models\ServiceProvider $model
     *
     * @return array
     */
    public function transform(ServiceProvider $model)
    {
        $response = [
            'id' => $model->id,
            'category' => $model->category,
            'name' => $model->name,
            'email' => $model->email,
            'phone' => $model->phone,
            'service_provider_format' => $model->service_provider_format,
        ];

        if ($model->relationExists('user')) {
            $response['user'] = (new UserTransformer)->transform($model->user);
        }

        if ($model->relationExists('address')) {
            $response['address'] = (new AddressTransformer)->transform($model->address);
        }

        if ($model->relationExists('districts')) {
            $response['districts'] = (new DistrictTransformer)->transformCollection($model->districts);
        }
        if ($model->relationExists('buildings')) {
            $response['buildings'] = (new BuildingTransformer)->transformCollection($model->buildings);
        }

        return $response;
    }

    /**
     * Transform the collection.
     *
     * @param \Collection $collection
     *
     * @return array
     */
    public function transformByCategoryCollection(Collection $collection)
    {
        foreach ($collection as $col) {
            $col->user_id = 0;
            $col->address_id = 0;
        }

        $services = $this->transformCollection($collection);
        $response = [];
        foreach ($services as $service) {
            if (!isset($response[$service['category']])) {
                $response[$service['category']] = [];
            }
            $response[$service['category']][] = $service;
        }

        return $response;
    }
}

