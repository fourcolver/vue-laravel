<?php

namespace App\Transformers;

use App\Models\Conversation;

/**
 * Class ConversationTransformer.
 *
 * @package namespace App\Transformers;
 */
class ConversationTransformer extends BaseTransformer
{
    protected $defaultIncludes = [];

    /**
     * Transform the Conversation entity.
     *
     * @param \App\Models\Conversation $model
     *
     * @return array
     */
    public function transform(Conversation $model)
    {
        $ut = new UserTransformer();
        $ret = [
            'id' => $model->id,
        ];

        if ($model->relationExists('users')) {
            foreach ($model->users as $u) {
                if ($u->id != \Auth::id()) {
                    $ret['user'] = $ut->transform($u);
                }
            }
        }

        return $ret;
    }
}

