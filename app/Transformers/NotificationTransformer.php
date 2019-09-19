<?php

namespace App\Transformers;

use Illuminate\Notifications\DatabaseNotification;
use App\Notifications\PostPublished;
use App\Models\Post;

/**
 * Class NotificationTransformer.
 *
 * @package namespace App\Transformers;
 */
class NotificationTransformer extends BaseTransformer
{
    /**
     * Transform the Notification entity.
     *
     * @param \App\Models\Notification $model
     *
     * @return array
     */
    public function transform(DatabaseNotification $model)
    {
        return [
            'id' => $model->id,
            'read_at' => $model->read_at ? $model->read_at->toDateTimeString() : null,
            'created_at' => $model->created_at->toDateTimeString(),
            'data' => $model->data,
            'type' => snake_case(class_basename($model->type)),
        ];
    }
}
