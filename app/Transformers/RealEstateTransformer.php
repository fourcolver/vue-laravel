<?php

namespace App\Transformers;

use App\Models\RealEstate;
use League\Fractal\TransformerAbstract;

/**
 * Class RealEstateTransformer.
 *
 * @package namespace App\Transformers;
 */
class RealEstateTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'user',
        'address',
    ];

    /**
     * Transform the RealEstate entity.
     *
     * @param \App\Models\RealEstate $model
     *
     * @return array
     */
    public function transform(RealEstate $model)
    {
        $response = [
            'id' => $model->id,
            'name' => $model->name,
            'email' => $model->email,
            'phone' => $model->phone,
            'language' => $model->language,
            'logo' => $model->logo,
            'blank_pdf' => $model->blank_pdf,
            'district_enable' => $model->district_enable,
            'contact_enable' => $model->contact_enable,
            'marketplace_approval_enable' => $model->marketplace_approval_enable,
            'news_approval_enable' => $model->news_approval_enable,
            'comment_update_timeout' => $model->comment_update_timeout,
            'free_apartments_enable' => $model->free_apartments_enable,
            'free_apartments_url' => $model->free_apartments_url,
            'opening_hours' => $model->opening_hours,
            'iframe_url' => $model->iframe_url,
            'iframe_enable' => $model->iframe_enable,
            'cleanify_email' => $model->cleanify_email,
            'news_receiver_ids' => $model->news_receiver_ids,
        ];

        if ($model->address) {
            $response['address'] = (new AddressTransformer)->transform($model->address);
        }
        if (isset($model->news_receivers)) {
            $response['news_receivers'] = (new UserTransformer)->transformCollection($model->news_receivers);
        }

        return $response;
    }
}
