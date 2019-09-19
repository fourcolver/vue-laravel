<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @SWG\Definition(
 *      definition="Translation",
 *      required={"object_type", "object_id", "language", "name", "value"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="object_type",
 *          description="object_type",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="object_id",
 *          description="object_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="language",
 *          description="language",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="value",
 *          description="value",
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
class Translation extends Model
{
    use SoftDeletes;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'object_type' => 'required',
        'object_id' => 'required',
        'language' => 'required',
        'name' => 'required',
        'value' => 'required'
    ];

    public $table = 'translations';
    public $fillable = [
        'object_type',
        'object_id',
        'language',
        'name',
        'value'
    ];
    protected $dates = ['deleted_at'];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'object_type' => 'string',
        'object_id' => 'integer',
        'language' => 'string',
        'name' => 'string',
        'value' => 'string'
    ];


}
