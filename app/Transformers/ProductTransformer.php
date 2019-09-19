<?php

namespace App\Transformers;

use App\Models\Product;

/**
 * Class ProductTransformer.
 *
 * @package namespace App\Transformers;
 */
class ProductTransformer extends BaseTransformer
{
    protected $defaultIncludes = [];

    /**
     * Transform the Product entity.
     *
     * @param \App\Models\Product $model
     *
     * @return array
     */
    public function transform(Product $model)
    {
        $ut = new UserTransformer();
        $mt = new MediaTransformer();
        $lt = new LikeTransformer();
        return [
            'id' => $model->id,
            'type' => $model->type,
            'status' => $model->status,
            'visibility' => $model->visibility,
            'content' => $model->content,
            'contact' => $model->contact,
            'title' => $model->title,
            'price' => $model->price,
            'created_at' => $model->created_at->toDateTimeString(),
            'updated_at' => $model->updated_at->toDateTimeString(),
            'published_at' => $model->published_at ? $model->published_at->toDateTimeString() : null,
            'user_id' => $model->user_id,
            'user' => $ut->transform($model->user),
            'media' => $mt->transformCollection($model->media),
            'liked' => $model->liked,
            'likes' => $lt->transformCollection($model->likes),
            'likes_count' => $model->likesCount,
            'comments_count' => $model->all_comments_count,
        ];
    }
}
