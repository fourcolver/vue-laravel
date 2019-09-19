<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;

/**
 * @SWG\Definition(
 *      definition="PasswordReset",
 *      required={"email"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="email",
 *          description="email",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="token",
 *          description="token",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 * )
 */
class PasswordReset extends Model
{
    use Notifiable;

    public $table = 'password_resets';

    public $primaryKey = 'email';

    public $incrementing = false;

    protected $dates = ['updated_at'];

    public $fillable = [
        'email',
        'token',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'email' => 'string',
        'token' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'email' => 'required|string|email|max:255|unique:users',
        'token' => 'string',
    ];

    public static function boot()
    {
        parent::boot();
    }
}
