<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @SWG\Definition(
 *      definition="State",
 *      required={"code", "name", "name_de", "name_fr", "name_it", "name_rm"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="code",
 *          description="code",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="name_de",
 *          description="name_de",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="name_fr",
 *          description="name_fr",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="name_it",
 *          description="name_it",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="name_rm",
 *          description="name_rm",
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
class State extends Model
{
    public $table = 'loc_states';

    public $timestamps = false;

    public $fillable = [
        'code',
        'name',
        'name_de',
        'name_fr',
        'name_it',
        'name_rm'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'code' => 'string',
        'name' => 'string',
        'name_de' => 'string',
        'name_fr' => 'string',
        'name_it' => 'string',
        'name_rm' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'code' => 'required',
        'name' => 'required',
        'name_de' => 'required',
        'name_fr' => 'required',
        'name_it' => 'required',
        'name_rm' => 'required'
    ];
}
