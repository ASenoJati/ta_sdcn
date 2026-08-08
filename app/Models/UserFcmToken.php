<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFcmToken extends Model
{
    protected $fillable = ['user_id', 'fcm_token', 'device_name', 'device_platform', 'last_used_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
