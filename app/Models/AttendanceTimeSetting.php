<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceTimeSetting extends Model
{
    protected $fillable = [
        'name',
        'check_in_start',
        'check_in_end',
        'check_out_start',
        'check_out_end',
        'grace_period_minutes'
    ];

    // Opsional: Casting agar otomatis jadi objek Carbon jika diperlukan di backend
    protected $casts = [
        'check_in_start' => 'datetime:H:i',
        'check_in_end' => 'datetime:H:i',
        'check_out_start' => 'datetime:H:i',
        'check_out_end' => 'datetime:H:i',
    ];
}
