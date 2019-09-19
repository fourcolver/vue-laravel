<?php

namespace App\Transformers;

use App\Models\Post;

/**
 * Class PostTransformer.
 *
 * @package namespace App\Transformers;
 */
class PostTransformer extends BaseTransformer
{
    protected $defaultIncludes = [];

    /**
     * Transform the Post entity.
     *
     * @param \App\Models\Post $model
     *
     * @return array
     */
    public function transform(Post $model)
    {
        $ut = new UserTransformer();
        $tt = new TenantTransformer();
        $ret = [
            'id' => $model->id,
            'type' => $model->type,
            'status' => $model->status,
            'visibility' => $model->visibility,
            'category' => $model->category,
            'content' => $model->content,
            'title' => $model->title,
            'created_at' => $model->created_at->toDateTimeString(),
            'updated_at' => $model->updated_at->toDateTimeString(),
            'published_at' => $model->published_at ? $model->published_at->toDateTimeString() : null,
            'user_id' => $model->user_id,
            'user' => $ut->transform($model->user),
            'tenant' => $model->user->tenant ? $tt->transform($model->user->tenant) : null,
            'liked' => $model->liked,
            'likes_count' => $model->likesCount,
            'comments_count' => $model->all_comments_count,
            'pinned' => $model->pinned,
            'pinned_to' => $model->pinned_to ? $model->pinned_to->toDateTimeString() : null,
            'execution_start' => $model->execution_start ? $model->execution_start->toDateTimeString() : null,
            'execution_end' => $model->execution_end ? $model->execution_end->toDateTimeString() : null,
            'notify_email' => $model->notify_email,
        ];

        if ($model->relationExists('buildings')) {
            $ret['buildings'] = (new BuildingTransformer)->transformCollection($model->buildings);
        }
        if ($model->relationExists('districts')) {
            $ret['districts'] = (new DistrictTransformer)->transformCollection($model->districts);
        }
        if ($model->relationExists('providers')) {
            $ret['providers'] = (new ServiceProviderTransformer)->transformCollection($model->providers);
        }
        if ($model->relationExists('likes')) {
            $ret['likes'] = (new LikeTransformer)->transformCollection($model->likes);
        }
        if ($model->relationExists('media')) {
            $ret['media'] = (new MediaTransformer)->transformCollection($model->media);
        }
        if ($model->relationExists('views')) {
            $ret['views'] = $model->views->sum('views');
        }

        return $ret;
    }
}
