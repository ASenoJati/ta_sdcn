<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'device_name',
        'platform',
        'last_used_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
