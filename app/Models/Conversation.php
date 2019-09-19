<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasComments;
use App\Notifications\RequestInternalComment;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @SWG\Definition(
 *      definition="Conversation",
 *      required={"conversation"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class Conversation extends Model
{
    use HasComments;
    protected $fillable = [];

    /**
     * Validation rules
     *
     * @var array
     */
    public static function rules()
    {
        return [];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function scopeOfLoggedInUser($query)
    {
        return $query->whereHas('users', function($q) {
            return $q->where('id', \Auth::id());
        });
    }

    public function notifyComment(Comment $comment)
    {
        $cType = array_flip(Relation::$morphMap)[ServiceRequest::class];
        if ($this->conversationable_type != $cType) {
            return;
        }

        $sr = ServiceRequest::findOrFail($this->conversationable_id);
        // If this is a service request conversation
        foreach ($this->users as $user) {
            if ($user->id != \Auth::id()) {
                $user->notify(new RequestInternalComment($sr, $comment, $user));
            }
        }
    }
}
