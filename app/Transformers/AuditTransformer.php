<?php

namespace App\Transformers;

use App\Models\Post;
use App\Models\Product;
use App\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Relations\Relation;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Support\Arr;

/**
 * Class AuditTransformer.
 *
 * @package namespace App\Transformers;
 */
class AuditTransformer extends BaseTransformer
{
    /**
     * Transform the Audit entity.
     *
     * @param OwenIt\Auditing\Models\Audit $model
     *
     * @return array
     */
    public function transform(Audit $model)
    {
        $ut = new UserTransformer();
        return [
            'id' => $model->id,
            'event' => $model->event,
            'auditable_type' => $model->auditable_type,
            'auditable_id' => $model->auditable_id,
            'user_id' => $model->user_id,
            'user' => $ut->transform($model->user),
            'url' => $model->url,
            'message' => $this->getMessage($model),
            'old_values' => $model->old_values,
            'new_values' => $model->new_values,
            'ip_address' => $model->ip_address,
            'created_at' => $model->created_at->toDateTimeString(),
            'updated_at' => $model->updated_at->toDateTimeString(),
        ];
    }

    private function getMessage(Audit $a)
    {
        if ($this->getMorphedModel($a->auditable_type) == Post::class) {
            return $this->getPostMessage($a);
        }
        if ($this->getMorphedModel($a->auditable_type) == Product::class) {
            return $this->getProductMessage($a);
        }
        if ($this->getMorphedModel($a->auditable_type) == ServiceRequest::class) {
            return $this->getServiceRequestMessage($a);
        }

        return "unkown";
    }

    private function getPostMessage(Audit $a)
    {
        if ($a->event == 'created' || $a->event == 'deleted') {
            return $a->event;
        }

        $sMsgs = [
            Post::StatusNew . Post::StatusPublished => "published",
            Post::StatusUnpublished . Post::StatusPublished => "published",
            Post::StatusNotApproved . Post::StatusPublished => "published",
            Post::StatusPublished . Post::StatusUnpublished => "unpublished",
        ];
        if (Arr::has($a->new_values, 'status') &&
            Arr::has($a->old_values, 'status')) {
            return $sMsgs[$a->old_values['status'] . $a->new_values['status']] ?? $a->event;
        }

        return $a->event;
    }

    private function getProductMessage(Audit $a)
    {
        return $a->event;
    }

    private function getServiceRequestMessage(Audit $a)
    {
        if ($a->event == 'created' || $a->event == 'deleted') {
            return $a->event;
        }

        if (Arr::has($a->new_values, 'status')) {
            return ServiceRequest::Status[$a->new_values['status']];
        }

        return $a->event;
    }

    private function getMorphedModel($alias)
    {
        return Relation::getMorphedModel($alias) ?? $alias;
    }
}
