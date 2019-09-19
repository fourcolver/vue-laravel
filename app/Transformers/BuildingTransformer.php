<?php

namespace App\Transformers;

use App\Models\Building;

/**
 * Class BuildingTransformer.
 *
 * @package namespace App\Transformers;
 */
class BuildingTransformer extends BaseTransformer
{
    protected $defaultIncludes = [
        'address'
    ];

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
            'name' => $model->name,
            'building_format' => $model->building_format,
            'label' => $model->label,
            'description' => $model->description,
            'floor_nr' => $model->floor_nr,
            'basement' => $model->basement,
            'attic' => $model->attic,
            'created_at' => $model->created_at->format('Y-m-d'),
            'district_id' => $model->district_id,

            'units_count' => $model->units_count,
            'tenants_count' => 0,
            'property_managers_count' => 0,
            'requests_count' => 0,
            'requests_received_count' => 0,
            'requests_in_processing_count' => 0,
            'requests_assigned_count' => 0,
            'requests_done_count' => 0,
            'requests_reactivated_count' => 0,
            'requests_archived_count' => 0,
        ];

        if ($model->relationExists('address')) {
            $response['address'] = (new AddressTransformer)->transform($model->address);
        }

        if ($model->relationExists('district')) {
            $response['district'] = (new DistrictTransformer)->transform($model->district);
        }

        if ($model->relationExists('tenants')) {
            $response['tenants'] = (new TenantSimpleTransformer)->transformCollection($model->tenants);
            $response['tenants_last'] = (new TenantSimpleTransformer)->transformCollection($model->lastTenants);
            if ($model->tenants_count > 2) {
                $response['tenants_count'] = $model->tenants_count - 2;
            }

            $response['requests_count'] = $model->requests_count;
            $response['requests_received_count'] = $model->requests_received_count;
            $response['requests_in_processing_count'] = $model->requests_in_processing_count;
            $response['requests_assigned_count'] = $model->requests_assigned_count;
            $response['requests_done_count'] = $model->requests_done_count;
            $response['requests_reactivated_count'] = $model->requests_reactivated_count;
            $response['requests_archived_count'] = $model->requests_archived_count;
        }

        if ($model->relationExists('serviceProviders')) {
            $response['service_providers'] = (new ServiceProviderTransformer)->transformCollection($model->serviceProviders);
        }

        if ($model->relationExists('propertyManagers')) {
            $response['managers'] = (new PropertyManagerSimpleTransformer)->transformCollection($model->propertyManagers);
            $response['managers_last'] = (new PropertyManagerSimpleTransformer)->transformCollection($model->lastPropertyManagers);
            if ($model->property_managers_count > 2) {
                $response['property_managers_count'] = $model->property_managers_count - 2;
            }
        }

        if ($model->relationExists('media')) {
            $response['media'] = (new MediaTransformer)->transformCollection($model->media);
        }

        return $response;
    }

    /**
     * Transform Request to Address entity.
     *
     * @param array $input
     *
     * @return array
     */
    public function transformRequest(array $input)
    {
        if (!isset($input['address'])) {
            $input['address'] = [];
        }

        return $input;
    }

    /**
     * Include Address
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeAddress(Building $building)
    {
        $address = $building->address;

        return $this->item($address, new AddressTransformer);
    }
}
