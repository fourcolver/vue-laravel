<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @SWG\Definition(
 *      definition="Address",
 *      required={"city", "address", "address_nr", "zip"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="country_id",
 *          description="country_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="state_id",
 *          description="state_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="city",
 *          description="city",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="street",
 *          description="street",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="street_nr",
 *          description="street_nr",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="zip",
 *          description="zip",
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
class Address extends Model
{
    use SoftDeletes;

    public $table = 'loc_addresses';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'country_id',
        'state_id',
        'city',
        'street',
        'street_nr',
        'zip'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'country_id' => 'integer',
        'state_id' => 'integer',
        'city' => 'string',
        'street' => 'string',
        'street_nr' => 'string',
        'zip' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'city' => 'required',
        'street' => 'required',
        'street_nr' => 'required',
        'zip' => 'required'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }
}
