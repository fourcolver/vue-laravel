<?php

namespace App\Models;

/**
 * @SWG\Definition(
 *      definition="CleanifyRequest",
 *      required={"content"},
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
 *      )
 * )
 */
class CleanifyRequest extends Model
{
    public $table = 'cleanify_requests';

    protected $dates = [];

    const Fillable = [
        'user_id',
        'form',
    ];
    public $fillable = self::Fillable;

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'form' => 'array',
    ];

    const templateMap = [
        'title' => 'form.title',
        'first_name' => 'form.first_name',
        'last_name' => 'form.last_name',
        'address' => 'form.address',
        'zip' => 'form.zip',
        'city' => 'form.city',
        'email' => 'form.email',
        'phone' => 'form.phone',
    ];

    /**
     * Validation rules
     *
     * @return array
     * @var array
     */
    public static function rules()
    {
        return [];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
