<?php

namespace App\Models;

use App\Traits\UniqueIDFormat;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @SWG\Definition(
 *      definition="District",
 *      required={"name", "description"},
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
 *          property="description",
 *          description="description",
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
class District extends Model
{
    use SoftDeletes, UniqueIDFormat;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|string',
    ];
    public $table = 'districts';
    public $fillable = [
        'name',
        'description',
        'district_format'
    ];
    protected $dates = ['deleted_at'];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'description' => 'string',
        'district_format' => 'string'
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($district) {
            $district->district_format = $district->getUniqueIDFormat($district->id);
            $district->save();
        });
    }

    public function propertyManagers()
    {
        return $this->belongsToMany(PropertyManager::class, 'district_property_manager', 'district_id', 'property_manager_id');
    }
}
