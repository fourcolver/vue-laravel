<?php

namespace App\Models;

use App\Traits\HasComments;
use Cog\Contracts\Love\Likeable\Models\Likeable as LikeableContract;
use Cog\Laravel\Love\Likeable\Models\Traits\Likeable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

/**
 * @SWG\Definition(
 *      definition="Post",
 *      required={"content"},
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
 *          property="type",
 *          description="type",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="status",
 *          description="status",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="visibility",
 *          description="visibility",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="category",
 *          description="category",
 *          type="int32"
 *      ),
 *      @SWG\Property(
 *          property="content",
 *          description="content",
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
class Post extends Model implements HasMedia, LikeableContract, Auditable
{
    use SoftDeletes;
    use HasMediaTrait;
    use Likeable;
    use HasComments;
    use \OwenIt\Auditing\Auditable;

    public $table = 'posts';

    const TypeArticle = 1;
    const TypeNewNeighbour = 2;
    const TypePinned = 3;

    const StatusNew = 1;
    const StatusPublished = 2;
    const StatusUnpublished = 3;
    const StatusNotApproved = 4;

    const VisibilityAddress = 1;
    const VisibilityDistrict = 2;
    const VisibilityAll = 3;

    const CategoryGeneral = 1;
    const CategoryMaintenance = 2;
    const CategoryElectricity = 3;
    const CategoryHeating = 4;
    const CategorySanitary = 5;

    const Type = [
        self::TypeArticle => 'article',
        self::TypeNewNeighbour => 'new_neighbour',
        self::TypePinned => 'pinned',
    ];
    const Status = [
        self::StatusNew => 'new',
        self::StatusPublished => 'published',
        self::StatusUnpublished => 'unpublished',
        self::StatusNotApproved => 'not_approved',
    ];
    const Visibility = [
        self::VisibilityAddress => 'address',
        self::VisibilityDistrict => 'district',
        self::VisibilityAll => 'all',
    ];
    const Category = [
        self::CategoryGeneral => 'general',
        self::CategoryMaintenance => 'maintenance',
        self::CategoryElectricity => 'electricity',
        self::CategoryHeating => 'heating',
        self::CategorySanitary => 'sanitary',
    ];

    protected $dates = [
        'deleted_at',
        'published_at',
        'pinned_to',
        'execution_start',
        'execution_end'
    ];

    const Fillable = [
        'user_id',
        'type',
        'content',
        'visibility',
        'category',
        'district_id',
        'pinned',
        'pinned_to',
        'execution_start',
        'execution_end',
        'title',
        'notify_email',
    ];
    public $fillable = self::Fillable;

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'type' => 'integer',
        'status' => 'integer',
        'visibility' => 'integer',
        'content' => 'string',
        'pinned' => 'boolean',
        'notify_email' => 'boolean',
    ];

    protected $auditInclude = [
        'type',
        'status',
        'visibility',
        'published_at',
        'pinned',
    ];

    const templateMap = [
        'title' => 'post.title',
        'content' => 'post.content',
        'providers' => 'post.providersList',
        'category' => 'post.categoryStr',
        'execution_start' => 'post.execution_start',
        'execution_end' => 'post.execution_end',
        'autologinUrl' => 'user.autologinUrl',
    ];

    /**
     * Validation rules
     *
     * @return array
     * @var array
     */
    public static function rules()
    {
        $categories = array_keys(self::Category);
        $categories[] = null;
        $re = RealEstate::first();
        $visibilities = self::Visibility;
        if (!$re->district_enable) {
            unset($visibilities[self::VisibilityDistrict]);
        }
        return [
            'content' => 'required',
            'visibility' => ['required', Rule::in(array_keys($visibilities))],
            'category' => [Rule::in($categories)],
            'pinned' => function ($attribute, $value, $fail) {
                if ($value && !\Auth::user()->can('pin-post')) {
                    $fail($attribute.' must be false.');
                }
            },
            'pinned_to' => Rule::requiredIf(request()->pinned),
            'execution_start' => 'nullable|date',
            'execution_end' => 'nullable|date|after_or_equal:execution_start',
        ];
    }

    /**
     * Publish validation rules
     *
     * @return array
     * @var array
     */
    public static function publishRules()
    {
        return [
            'status' => ['required', Rule::in(array_keys(self::Status))]
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function buildings()
    {
        return $this->belongsToMany(Building::class);
    }

    public function districts()
    {
        return $this->belongsToMany(District::class);
    }

    public function providers()
    {
        return $this->belongsToMany(ServiceProvider::class);
    }

    public function views()
    {
        return $this->hasMany(PostView::class);
    }

    public function registerMediaCollections()
    {
        $this->addMediaCollection('media');
    }

    public function getExecutionStartStrAttribute()
    {
        if (!$this->execution_start) {
            return "-";
        }
        return $this->execution_start->format('d.m.Y H:i');
    }
    public function getExecutionEndStrAttribute()
    {
        if (!$this->execution_end) {
            return "-";
        }
        return $this->execution_end->format('d.m.Y H:i');
    }

    public function getProvidersStrAttribute()
    {
        if (!count($this->providers)) {
            return "-";
        }
        $pNames = $this->providers->map(function($el) {
            return $el->name;
        })->toArray();

        return implode(', ', $pNames);
    }

    public function getBuildingsStrAttribute()
    {
        if (!count($this->buildings)) {
            return "-";
        }
        $pNames = $this->buildings->map(function($el) {
            return $el->name;
        })->toArray();

        return implode(', ', $pNames);
    }

    public function getDistrictsStrAttribute()
    {
        if (!count($this->districts)) {
            return "-";
        }
        $pNames = $this->districts->map(function($el) {
            return $el->name;
        })->toArray();

        return implode(', ', $pNames);
    }

    public function getCategoryStrAttribute()
    {
        if (!$this->category) {
            return "-";
        }

        return self::Category[$this->category];
    }

    public function incrementViews(int $userID)
    {
        $uv = PostView::where('post_id', $this->id)
            ->where('user_id', $userID)
            ->first();
        if (!$uv) {
            $uv = new PostView();
            $uv->user_id = $userID;
            $uv->post_id = $this->id;
        }
        $uv->views += 1;
        $uv->save();
        return $uv;
    }
}
