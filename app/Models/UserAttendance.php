<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Model;

class UserAttendance extends Model
{
    protected $casts = [
        'check_in_status' => AttendanceStatus::class,
        'check_out_status' => AttendanceStatus::class,
        'attendance_date' => 'date',
    ];
}
