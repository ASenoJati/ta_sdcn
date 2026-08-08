<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;

class RoleAttendanceTime extends Model
{
    use SoftDeletes;

    protected $fillable = ['role_id', 'attendance_time_settings_id'];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function attendanceTimeSetting(): BelongsTo
    {
        return $this->belongsTo(AttendanceTimeSetting::class, 'attendance_time_settings_id');
    }
}
