<?php

namespace App\Models;

use App\Traits\UniqueIDFormat;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @SWG\Definition(
 *      definition="Unit",
 *      required={"name", "floor"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
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
 *          property="type",
 *          description="type",
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
 *          property="floor",
 *          description="floor",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="monthly_rent",
 *          description="monthly_rent",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="room_no",
 *          description="room_no",
 *          type="string",
 *          format="string"
 *      ),
 *      @SWG\Property(
 *          property="basement",
 *          description="basement",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="attic",
 *          description="attic",
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
class Unit extends Model
{
    use SoftDeletes, UniqueIDFormat;

    public $table = 'units';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'building_id',
        'type',
        'name',
        'description',
        'floor',
        'monthly_rent',
        'room_no',
        'basement',
        'attic',
        'unit_format',
        'sq_meter'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'building_id' => 'integer',
        'type' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'floor' => 'integer',
        'monthly_rent' => 'float',
        'room_no' => 'float',
        'basement' => 'boolean',
        'attic' => 'boolean',
        'unit_format' => 'string',
        'sq_meter' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'building_id' => 'required|integer',
        'type' => 'required|integer',
        'name' => 'required|string',
        'floor' => 'required'
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($unit) {
            $unit->unit_format = $unit->getUniqueIDFormat($unit->id);
            $unit->save();
        });
    }

        /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function tenant()
    {
        return $this->hasOne(Tenant::class, 'unit_id', 'id');
    }
}
