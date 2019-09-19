<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @SWG\Definition(
 *      definition="UserSettings",
 *      required={"language", "summary", "news_notification", "marketplace_notification", "service_notification"},
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
 *          property="language",
 *          description="language",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="summary",
 *          description="summary",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="admin_notification",
 *          description="admin_notification",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="news_notification",
 *          description="news_notification",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="marketplace_notification",
 *          description="marketplace_notification",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="service_notification",
 *          description="service_notification",
 *          type="integer",
 *          format="int32"
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
class UserSettings extends Model
{
    use SoftDeletes;

    public $table = 'user_settings';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'user_id',
        'language',
        'summary',
        'admin_notification',
        'news_notification',
        'marketplace_notification',
        'service_notification'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'language' => 'string',
        'summary' => 'string',
        'admin_notification' => 'boolean',
        'news_notification' => 'boolean',
        'marketplace_notification' => 'boolean',
        'service_notification' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'language' => 'required',
        'summary' => 'required',
        'news_notification' => 'required',
        'marketplace_notification' => 'required',
        'service_notification' => 'required'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
