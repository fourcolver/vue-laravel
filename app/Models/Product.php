<?php

namespace App\Models;

use App\Traits\HasComments;
use Cog\Contracts\Love\Likeable\Models\Likeable as LikeableContract;
use Cog\Laravel\Love\Likeable\Models\Traits\Likeable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

/**
 * @SWG\Definition(
 *      definition="Product",
 *      required={"content", "contact"},
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
 *          property="status",
 *          description="status",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="visibility",
 *          description="visibility",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="content",
 *          description="content",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="price",
 *          description="price",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="title",
 *          description="title",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contact",
 *          description="contact",
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
class Product extends Model implements HasMedia, LikeableContract, Auditable
{
    use SoftDeletes;
    use HasMediaTrait;
    use Likeable;
    use HasComments;
    use \OwenIt\Auditing\Auditable;

    public $table = 'products';

    const TypeSell = 1;
    const TypeLend = 2;
    const TypeService = 3;
    const TypeGiveAway = 4;

    const StatusUnpublished = 1;
    const StatusPublished = 2;

    const VisibilityAddress = 1;
    const VisibilityDistrict = 2;
    const VisibilityAll = 3;

    const Type = [
        self::TypeSell => 'sell',
        self::TypeLend => 'lend',
        self::TypeService => 'service',
        self::TypeGiveAway => 'giveaway',
    ];
    const Status = [
        self::StatusUnpublished => 'unpublished',
        self::StatusPublished => 'published',
    ];
    const Visibility = [
        self::VisibilityAddress => 'address',
        self::VisibilityDistrict => 'district',
        self::VisibilityAll => 'all',
    ];

    protected $dates = ['deleted_at', 'published_at'];

    const Fillable = [
        'user_id',
        'type',
        'status',
        'visibility',
        'content',
        'contact',
        'title',
        'price',
    ];
    public $fillable = self::Fillable;

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'type' => 'integer',
        'status' => 'integer',
        'visibility' => 'integer',
        'content' => 'string',
        'contact' => 'string',
        'title' => 'string',
        'price' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static function rules()
    {
        return [
            'content' => 'required|string',
            'contact' => 'required|string',
            'title' => 'required|string',
            'price' => 'nullable|string|numeric',
            'visibility' => ['required', Rule::in(array_keys(self::Visibility))],
            'type' => ['required', Rule::in(array_keys(self::Type))],
        ];
    }

    /**
     * Publish validation rules
     *
     * @var array
     */
    public static function publishRules()
    {
        return [
            'status' => ['required', Rule::in(array_keys(self::Status))]
        ];
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    public function registerMediaCollections()
    {
        $this->addMediaCollection('media');
    }
}
