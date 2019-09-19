<?php
namespace App\Models;

use Cog\Contracts\Love\Like\Models\Like as LikeContract;
use Cog\Laravel\Love\Like\Models\Like as CogLike;

class Like extends CogLike implements LikeContract
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
