<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

/**
 * @SWG\Definition(
 *      definition="RealEstate",
 *      required={"name", "language"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
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
 *          property="language",
 *          description="language",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="logo",
 *          description="logo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="blank_pdf",
 *          description="blank_pdf",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="district_enable",
 *          description="district_enable",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="contact_enable",
 *          description="contact_enable",
 *          type="integer",
 *          format="int32"
 *      ),
 *     @SWG\Property(
 *          property="marketplace_approval_enable",
 *          description="marketplace_approval_enable",
 *          type="integer",
 *          format="int32"
 *      ),
 *     @SWG\Property(
 *          property="news_approval_enable",
 *          description="news_approval_enable",
 *          type="integer",
 *          format="int32"
 *      ),
 *     @SWG\Property(
 *          property="comment_update_timeout",
 *          description="comment_update_timeout",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="iframe_url",
 *          description="iframe url",
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
class RealEstate extends Model
{
    use SoftDeletes;

    public $table = 'real_estates';

    protected $dates = ['deleted_at'];


    public $fillable = [
        'address_id',
        'name',
        'email',
        'phone',
        'language',
        'logo',
        'blank_pdf',
        'district_enable',
        'contact_enable',
        'marketplace_approval_enable',
        'news_approval_enable',
        'comment_update_timeout',
        'free_apartments_enable',
        'free_apartments_url',
        'opening_hours',
        'iframe_url',
        'iframe_enable',
        'cleanify_email',
        'news_receiver_ids',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'address_id' => 'integer',
        'name' => 'string',
        'email' => 'string',
        'phone' => 'string',
        'language' => 'string',
        'logo' => 'string',
        'blank_pdf' => 'boolean',
        'district_enable' => 'boolean',
        'contact_enable' => 'boolean',
        'marketplace_approval_enable' => 'boolean',
        'news_approval_enable' => 'boolean',
        'comment_update_timeout' => 'integer',
        'free_apartments_enable' => 'boolean',
        'free_apartments_url' => 'string',
        'opening_hours' => 'array',
        'iframe_url' => 'string',
        'iframe_enable' => 'boolean',
        'cleanify_email' => 'string',
        'news_receiver_ids' => 'array',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static function rules()
    {
        return [
            'name' => 'required',
            'language' => 'required',
            'cleanify_email' => 'email',
            'iframe_url' => function($attr, $val, $fail) {
                if (is_string($val)) {
                    if (!filter_var($val, FILTER_VALIDATE_URL)) {
                        $fail('The iframe url format is invalid');
                    }
                }
            },
            'news_receiver_ids' => function($attr, $val, $fail) {
                $us = User::whereIn('id', $val)->get();
                foreach ($us as $u) {
                    if (!$u->hasRole('administrator')) {
                        $fail('News email receivers must be administrators');
                    }
                }
            },
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function address()
    {
        return $this->hasOne(Address::class, 'id', 'address_id');
    }
}
