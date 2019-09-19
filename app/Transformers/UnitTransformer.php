<?php

namespace App\Transformers;

use App\Models\Unit;

/**
 * Class UnitTransformer.
 *
 * @package namespace App\Transformers;
 */
class UnitTransformer extends BaseTransformer
{
    protected $defaultIncludes = [
        'address'
    ];

    /**
     * Transform the Unit entity.
     *
     * @param Unit $model
     *
     * @return array
     */
    public function transform(Unit $model)
    {
        $response = [
            'id' => $model->id,
            'unit_format' => $model->unit_format,
            'type' => $model->type,
            'name' => $model->name,
            'description' => $model->description,
            'building_id' => $model->building_id,
            'floor' => $model->floor,
            'monthly_rent' => $model->monthly_rent,
            'room_no' => $model->room_no,
            'basement' => $model->basement,
            'attic' => $model->attic,
            'sq_meter' => $model->sq_meter,
            'tenants' => [],
        ];

        if ($model->building) {
            $response['building'] = (new BuildingSimpleTransformer)->transform($model->building);
        }

        if ($model->address) {
            $response['address'] = (new AddressTransformer)->transform($model->address);
        }

        if ($model->tenant) {
            $model->tenant->unit = null;
            $model->tenant->building = null;
            $model->tenant->address = null;
            $response['tenant'] = (new TenantTransformer)->transform($model->tenant);
            $response['tenants'][] = (new TenantTransformer)->transform($model->tenant);
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
        if (isset($input['address'])) {
            unset($input['address']);
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
