<?php

namespace App\Models;

use App\Traits\UniqueIDFormat;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PDF;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Storage;
use Hashids\Hashids;

/**
 * @SWG\Definition(
 *      definition="Tenant",
 *      required={"first_name", "birthdate"},
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
 *          property="building_id",
 *          description="building_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="unit_id",
 *          description="unit_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="title",
 *          description="title",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="company",
 *          description="company",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="first_name",
 *          description="first_name",
 *          type="string"
 *      ),
 *     @SWG\Property(
 *          property="last_name",
 *          description="last_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="birth_date",
 *          description="birth_date",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="mobile_phone",
 *          description="mobile_phone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="private_phone",
 *          description="private_phone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="work_phone",
 *          description="work_phone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="rent_start",
 *          description="rent_start",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="rent_end",
 *          description="rent_end",
 *          type="string",
 *          format="date"
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
class Tenant extends Model implements HasMedia
{
    use HasMediaTrait, UniqueIDFormat;

    const Title = [
        'mr',
        'mrs',
        'company',
    ];
    const StatusActive = 1;
    const StatusNotActive = 2;
    const Status = [
        self::StatusActive => 'active',
        self::StatusNotActive => 'not_active',
    ];
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'title' => 'required|string',
        'first_name' => 'required|string',
        'last_name' => 'required|string',
        'birth_date' => 'date',
        'rent_start' => 'date',
        'rent_end' => 'date|after_or_equal:rent_start',
        'status' => 'digits_between:1,2|numeric'
    ];
    public $table = 'tenants';
    public $fillable = [
        'user_id',
        'address_id',
        'building_id',
        'unit_id',
        'title',
        'company',
        'first_name',
        'last_name',
        'birth_date',
        'mobile_phone',
        'private_phone',
        'work_phone',
        'status',
        'rent_start',
        'rent_end',
        'tenant_format',
        'review',
        'rating'
    ];
    protected $dates = ['deleted_at', 'rent_start', 'rent_end'];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'address_id' => 'integer',
        'building_id' => 'integer',
        'unit_id' => 'integer',
        'title' => 'string',
        'company' => 'string',
        'first_name' => 'string',
        'last_name' => 'string',
        'birth_date' => 'date',
        'rent_start' => 'date',
        'rent_end' => 'date',
        'mobile_phone' => 'string',
        'private_phone' => 'string',
        'work_phone' => 'string',
        'status' => 'integer',
        'tenant_format' => 'string'
    ];

    const templateMap = [
        'first_name' => 'tenant.first_name',
        'last_name' => 'tenant.last_name',
        'email' => 'user.email',
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($tenant) {
            $tenant->tenant_format = $tenant->getUniqueIDFormat($tenant->id);
            $tenant->save();
        });

        static::deleting(function ($tenant) {
            $tenant->user->settings()->forceDelete();
            $tenant->user()->forceDelete();
        });
    }

    /**
     * @return BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return BelongsTo
     **/
    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id', 'id');
    }

    /**
     * @return BelongsTo
     **/
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function registerMediaCollections()
    {
        $this->addMediaCollection('documents');
    }

    public function homeless()
    {
        return !$this->building()->exists();
    }

    /**
     * @return BelongsTo
     **/
    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id', 'id');
    }

    /**
     * @return HasMany
     **/
    public function requests()
    {
        return $this->hasMany(ServiceRequest::class, 'tenant_id', 'id');
    }

    /**
     * @return HasMany
     **/
    public function requestsReceived()
    {
        return $this->hasMany(ServiceRequest::class, 'tenant_id', 'id')
            ->where('service_requests.status', ServiceRequest::StatusReceived);
    }

    public function requestsInProcessing()
    {
        return $this->hasMany(ServiceRequest::class, 'tenant_id', 'id')
            ->where('service_requests.status', ServiceRequest::StatusInProcessing);
    }

    public function requestsAssigned()
    {
        return $this->hasMany(ServiceRequest::class, 'tenant_id', 'id')
            ->where('service_requests.status', ServiceRequest::StatusAssigned);
    }

    public function requestsDone()
    {
        return $this->hasMany(ServiceRequest::class, 'tenant_id', 'id')
            ->where('service_requests.status', ServiceRequest::StatusDone);
    }

    public function requestsReactivated()
    {
        return $this->hasMany(ServiceRequest::class, 'tenant_id', 'id')
            ->where('service_requests.status', ServiceRequest::StatusReactivated);
    }

    public function requestsArchived()
    {
        return $this->hasMany(ServiceRequest::class, 'tenant_id', 'id')
            ->where('service_requests.status', ServiceRequest::StatusArchived);
    }

    public function setCredentialsPDF($tenant_id)
    {
        $hashids = new Hashids('', 25);
        $re = RealEstate::firstOrFail();
        $pdf = PDF::loadView('pdfs.tenantCredentialsXtended', [
            'tenant' => $this,
            're' => $re,
            'url' => url('/resetpassword?token='.$hashids->encode($tenant_id))
        ]);
        Storage::disk('tenant_credentials')->put($this->pdfXFileName(), $pdf->output());
        $pdf = PDF::loadView('pdfs.tenantCredentials', [
            'tenant' => $this,
            're' => $re,
            'url' => url('/resetpassword?token='.$hashids->encode($tenant_id))
        ]);
        Storage::disk('tenant_credentials')->put($this->pdfFilename(), $pdf->output());
    }

    public function pdfXFileName(string $language = "")
    {
        if (!$language) {
            $language = \App::getLocale();
        }
        return $this->id . '-' . $language . '-X.pdf';
    }

    public function pdfFileName(string $language = "")
    {
        if (!$language) {
            $language = \App::getLocale();
        }
        return $this->id . '-' . $language . '.pdf';
    }

    public function getNameAttribute()
    {
        return $this->title . " " . $this->first_name . " " . $this->last_name;
    }
}
