<?php

namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @SWG\Definition(
 *      definition="Template",
 *      required={"name"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="category_id",
 *          description="category_id",
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
 *          property="system",
 *          description="system",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="bool"
 *      ),
 *      @SWG\Property(
 *          property="subject",
 *          description="subject",
 *          type="string"
 *      ),
 *     @SWG\Property(
 *          property="body",
 *          description="body",
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
class Template extends Model
{
    use SoftDeletes;
    use Translatable;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'category_id' => 'required',
        'subject' => 'required',
        'type' => 'required'
    ];

    public $table = 'templates';

    public $fillable = [
        'category_id',
        'name',
        'subject',
        'body',
        'type'
    ];

    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'category_id' => 'integer',
        'type' => 'integer',
        'name' => 'string',
        'subject' => 'string',
        'body' => 'string',
        'system' => 'boolean'
    ];

    public function translatable()
    {
        return [
            'subject',
            'body',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function category()
    {
        return $this->hasOne(TemplateCategory::class, 'id', 'category_id');
    }
}
