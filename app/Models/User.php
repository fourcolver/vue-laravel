<?php

namespace App\Models;

use App\Helpers\Helper;
use BeyondCode\Comments\Contracts\Commentator;
use Cog\Contracts\Love\Liker\Models\Liker as LikerContract;
use Cog\Laravel\Love\Liker\Models\Traits\Liker;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Zizaco\Entrust\Traits\EntrustUserTrait;

/**
 * @SWG\Definition(
 *      definition="User",
 *      required={"name"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="email",
 *          description="email",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="phone",
 *          description="phone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="avatar",
 *          description="avatar",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="email_verified_at",
 *          description="email_verified_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="password",
 *          description="password",
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
class User extends Authenticatable implements LikerContract, Commentator
{
    use EntrustUserTrait;
    use HasApiTokens;
    use Notifiable;
    use Liker;

    const Title = [
        'mr',
        'mrs',
        'company',
    ];

    public $table = 'users';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'email_verified_at',
        'last_login_at',
        'password'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'email' => 'string',
        'phone' => 'string',
        'password' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6|confirmed',
    ];

    /**
     * Validation rulesUpdate
     *
     * @var array
     */
    public static $rulesUpdate = [
        'name' => 'required|string|max:255',
        //'email' => 'required|string|email|max:255',
        'image_upload' => 'sometimes|string',
        'password_old' => 'sometimes|string',
        'password' => 'sometimes|required|string|min:6',
        'password_confirmation' => 'sometimes|required_with:password|same:password',
    ];

    /**
     * Validation rulesChangePassword
     *
     * @var array
     */
    public static $rulesChangePassword = [
        'password_old' => 'required|string',
        'password' => 'required|string|min:6|confirmed',
    ];

    /**
     * Validation rulesChangePassword
     *
     * @var array
     */
    public static $rulesUpload = [
        'image_upload' => 'required',
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(function($user) {
            $user->settings()->forceDelete();
        });
    }

    public function restore() {
        $this->sfRestore();
        Cache::tags(Config::get('entrust.role_user_table'))->flush();
    }

    /**
     * Check if a comment for a specific model needs to be approved.
     * @param mixed $model
     * @return bool
     */
    public function needsCommentApproval($model): bool
    {
        return false;
    }

    /**
    * @return \Illuminate\Database\Eloquent\Relations\HasOne
    **/
    public function settings()
    {
        return $this->hasOne(UserSettings::class);
    }

    /**
    * @return \Illuminate\Database\Eloquent\Relations\HasOne
    **/
    public function tenant()
    {
        return $this->hasOne(Tenant::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function serviceProvider()
    {
        return $this->hasOne(ServiceProvider::class);
    }

    /**
    * @return \Illuminate\Database\Eloquent\Relations\MorphMany
    **/
    public function notifications()
    {
        return $this->morphMany(\Illuminate\Notifications\DatabaseNotification::class, 'notifiable')
                    ->orderByRaw('-read_at','DESC')
                    ->orderBy('created_at','DESC');
    }

    public function requests()
    {
        return $this->belongsToMany(ServiceRequest::class, 'request_assignee', 'request_id', 'user_id');
    }

    public function scopeWithRoles($query, array $roles)
    {
        return $query->whereHas('roles', function ($query) use ($roles) {
            $query->whereIn('name', $roles);
        });
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class);
    }

    public function autologins()
    {
        return $this->hasMany(Autologin::class);
    }

    public function getAutologinUrlAttribute()
    {
        $a = new Autologin();

        $a->redirect = $this->redirect ?? "/dashboard";
        $a->token = Helper::randStr(100);
        $a->user_id = $this->id;
        $a->save();

        return $a->url;
    }

    public function requestsReceived()
    {
        return $this->requests()->where('service_requests.status', ServiceRequest::StatusReceived);
    }

    public function requestsInProcessing()
    {
        return $this->requests()->where('service_requests.status', ServiceRequest::StatusInProcessing);
    }

    public function requestsAssigned()
    {
        return $this->requests()->where('service_requests.status', ServiceRequest::StatusAssigned);
    }

    public function requestsDone()
    {
        return $this->requests()->where('service_requests.status', ServiceRequest::StatusDone);
    }

    public function requestsReactivated()
    {
        return $this->requests()->where('service_requests.status', ServiceRequest::StatusReactivated);
    }

    public function requestsArchived()
    {
        return $this->requests()->where('service_requests.status', ServiceRequest::StatusArchived);
    }
}
