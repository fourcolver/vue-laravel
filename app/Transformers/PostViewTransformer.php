<?php

namespace App\Transformers;

use App\Models\PostView;

/**
 * Class PostViewTransformer.
 *
 * @package namespace App\Transformers;
 */
class PostViewTransformer extends BaseTransformer
{
    protected $defaultIncludes = [];

    /**
     * Transform the PostView entity.
     *
     * @param \App\Models\PostView $model
     *
     * @return array
     */
    public function transform(PostView $model)
    {
        $ut = new UserTransformer();
        $ret = [
            'id' => $model->id,
            'views' => $model->views,
            'created_at' => $model->created_at->toDateTimeString(),
            'updated_at' => $model->updated_at->toDateTimeString(),
            'user_id' => $model->user_id,
        ];

        if ($model->relationExists('user')) {
            $ret['user'] = $ut->transform($model->user);
        }

        return $ret;
    }
}
