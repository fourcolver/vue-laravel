<?php

namespace App\Models;

class LoginDevice extends Model
{
    public $timestamps = false;

    public $fillable = [
        'user_id',
        'tenant_id',
        'mobile',
        'desktop',
        'tablet',
        'created_by',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }
}
