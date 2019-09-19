<?php

namespace App\Transformers;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;
use League\Fractal\Resource\Collection as FCollection;
use League\Fractal\Manager;

/**
 * Class CommentTransformer.
 *
 * @package namespace App\Transformers;
 */
class CommentTransformer extends BaseTransformer
{
    protected $defaultIncludes = [];

    /**
     * Transform the Comment entity.
     *
     * @param \App\Models\Comment $model
     *
     * @return array
     */
    public function transform(Comment $model)
    {
        $ret = [
            'id' => $model->id,
            'user_id' => $model->user_id,
            'comment' => $model->comment,
            'created_at' => $model->created_at->toDateTimeString(),
            'updated_at' => $model->updated_at->toDateTimeString(),
            'children_count' => $model->children_count,
        ];

        if ($model->relationExists('user')) {
            $ret['user'] = (new UserTransformer())->transform($model->user);
        }

        return $ret;
    }
}
