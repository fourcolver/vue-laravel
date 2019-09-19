<?php

namespace App\Transformers;
use App\Models\ServiceRequest;

/**
 * Class ServiceRequestTransformer
 *
 * @package namespace App\Transformers;
 */
class ServiceRequestTransformer extends BaseTransformer
{
    /**
     * Transform the ServiceProvider entity.
     *
     * @param ServiceRequest $model
     *
     * @return array
     */
    public function transform(ServiceRequest $model)
    {
        $response = [
            'id' => $model->id,
            'service_request_format' => $model->service_request_format,
            'title' => $model->title,
            'description' => $model->description,
            'status' => $model->status,
            'priority' => $model->priority,
            'qualification' => $model->qualification,
            'is_public' => $model->is_public,
            'created_at' => $model->created_at->format('Y-m-d'),
            'visibility' => $model->visibility,
        ];

        if ($model->due_date) {
            $response['due_date'] = $model->due_date->format('Y-m-d');
        }

        if ($model->solved_date) {
            $response['solved_date'] = $model->solved_date->format('Y-m-d');
        }

        if ($model->relationExists('providers')) {
            $response['providers'] = (new ServiceProviderTransformer)->transformCollection($model->providers);
        }

        if ($model->relationExists('assignees')) {
            $providerUsers = collect();
            if ($model->relationExists('providers')) {
                $providerUsers = $model->providers->map(function($p) {
                    return $p->user;
                });
            }
            $response['assignedUsers'] = (new UserTransformer)
                ->transformCollection($model->assignees->merge($providerUsers));
            $response['assignees'] = (new UserTransformer)
                ->transformCollection($model->assignees);
        }

        if ($model->relationExists('category')) {
            $response['category'] = (new ServiceRequestCategorySimpleTransformer)->transform($model->category);
        }

        if ($model->relationExists('tenant')) {
            $response['tenant'] = (new TenantTransformer)->transform($model->tenant);
        }

        if ($model->relationExists('comments')) {
            $response['comments'] = (new CommentTransformer)->transformCollection($model->comments);
        }

        $response['media'] = [];
        if ($model->relationExists('media')) {
            $response['media'] = (new MediaTransformer)->transformCollection($model->media);
        }

        return $response;
    }
}
