<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use BeyondCode\Comments\Comment as Comment_;

/**
 * @SWG\Definition(
 *      definition="Comment",
 *      required={"comment"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="user_id",
 *          description="user_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="type",
 *          description="type",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="comment",
 *          description="comment body",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class Comment extends Comment_
{
    protected $fillable = [
        'comment',
        'user_id',
        'is_approved',
        'parent_id',
    ];

    /**
     * Validation rules
     *
     * @var array
     * @TODO find a way to validate parent_id so that the parent and the child
     * both have the same commentable type and commentable id
     */
    public static function rules()
    {
        return [
            'comment' => 'required|string',
            'parent_id' => [
                'exists:comments,id',
                function ($attribute, $value, $fail) {
                    $c = Comment::find($value);
                    if ($c && !is_null($c->parent_id)) {
                        $fail('parent_id is invalid.');
                    }
                },
            ],
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function children()
    {
        return $this->hasMany(Comment::class, 'parent_id', 'id');
    }

    public function getChildrenCountAttribute()
    {
        if (!$this->relationLoaded('childrenCountRelation')) {
            return 0;
        }
        $related = $this->getRelation('childrenCountRelation');
        return ($related) ? (int) $related->aggregate : 0;
    }

    public function childrenCountRelation()
    {
        return $this->hasOne(Comment::class, 'parent_id')
        ->selectRaw('parent_id, count(*) as aggregate')
            ->groupBy('parent_id');
    }

    // relationExists returns whether the relation named $key exists, is loaded
    // and is not null
    public function relationExists($key)
    {
        return parent::relationLoaded($key) && isset($this->$key);
    }
}
