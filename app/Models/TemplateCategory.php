<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @SWG\Definition(
 *      definition="TemplateCategory",
 *      required={"name", "description"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="parent_id",
 *          description="parent_id",
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
 *          property="tags",
 *          description="tags",
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
class TemplateCategory extends Model
{
    use SoftDeletes;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'description' => 'required'
    ];

    public $table = 'template_categories';

    public $fillable = [
        'parent_id',
        'name',
        'description',
        'tag_map'
    ];

    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'parent_id' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'tag_map' => 'array'
    ];

    const TypeEmail = 1;
    const TypeCommunication = 2;

    const Type = [
        self::TypeEmail => 'email',
        self::TypeCommunication => 'communication',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function categories()
    {
        return $this->hasMany(TemplateCategory::class, 'parent_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function parentCategory()
    {
        return $this->belongsTo(TemplateCategory::class, 'parent_id', 'id');
    }
}
