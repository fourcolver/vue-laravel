<?php

namespace App\Transformers;

use App\Models\CleanifyRequest;

/**
 * Class CleanifyRequestTransformer.
 *
 * @package namespace App\Transformers;
 */
class CleanifyRequestTransformer extends BaseTransformer
{
    protected $defaultIncludes = [];

    /**
     * Transform the CleanifyRequest entity.
     *
     * @param \App\Models\CleanifyRequest $model
     *
     * @return array
     */
    public function transform(CleanifyRequest $model)
    {
        $ut = new UserTransformer();
        return [
            'id' => $model->id,
            'user_id' => $model->user_id,
            'user' => $ut->transform($model->user),
            'form' => $model->form,
        ];
    }
}
