<?php

namespace App\Models;

use App\Traits\UniqueIDFormat;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

/**
 * @SWG\Definition(
 *      definition="ServiceProvider",
 *      required={"name"},
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
 *          property="address_id",
 *          description="address_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="category",
 *          description="category",
 *          type="string"
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
class ServiceProvider extends Model
{
    use Notifiable;
    use SoftDeletes;
    use UniqueIDFormat;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255',
        'category' => 'required|string|max:255',
        'phone' => 'required|string|max:255',
        'user' => 'required',
        'address' => 'required',
    ];

    public static $rulesUpdate = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255',
        'category' => 'required|string|max:255',
        'phone' => 'required|string|max:255',
    ];

    public $table = 'service_providers';

    const ServiceProviderCategories = [
        'electrician',
        'heating_company',
        'lift',
        'sanitary',
        'key_service',
        'caretaker',
        'real_estate_service',
    ];

    public $fillable = [
        'user_id',
        'address_id',
        'category',
        'name',
        'email',
        'phone'
    ];
    protected $dates = ['deleted_at'];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'address_id' => 'integer',
        'category' => 'string',
        'name' => 'string',
        'email' => 'string',
        'phone' => 'string',
        'service_provider_format' => 'string'
    ];

    /**
     *
     */
    public static function boot()
    {
        parent::boot();

        static::created(function ($building) {
            $building->service_provider_format = $building->getUniqueIDFormat($building->id);
            $building->save();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function address()
    {
        return $this->hasOne(Address::class, 'id', 'address_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     **/
    public function requests()
    {
        return $this->belongsToMany(ServiceRequest::class, 'request_provider', 'provider_id', 'request_id');
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     **/
    public function buildings()
    {
        return $this->belongsToMany(Building::class, 'building_service_provider', 'service_provider_id', 'building_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     **/
    public function districts()
    {
        return $this->belongsToMany(District::class, 'district_service_provider', 'service_provider_id', 'district_id');
    }
}
