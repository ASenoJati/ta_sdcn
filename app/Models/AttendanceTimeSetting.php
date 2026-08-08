<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceTimeSetting extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'check_in_start',
        'check_in_end',
        'check_out_start',
        'check_out_end',
        'grace_period_minutes'
    ];

    protected $casts = [
        'check_in_start' => 'datetime:H:i',
        'check_in_end' => 'datetime:H:i',
        'check_out_start' => 'datetime:H:i',
        'check_out_end' => 'datetime:H:i',
    ];

    public function roleAttendanceTimes(): HasMany
    {
        return $this->hasMany(RoleAttendanceTime::class, 'attendance_time_settings_id');
    }

    /**
     * Check if setting is being used by any role
     */
    public function isUsed()
    {
        return $this->roleAttendanceTimes()->count() > 0;
    }

    /**
     * Format check-in time range
     */
    public function getCheckInRangeAttribute()
    {
        return $this->check_in_start . ' - ' . $this->check_in_end;
    }

    /**
     * Format check-out time range
     */
    public function getCheckOutRangeAttribute()
    {
        if ($this->check_out_start && $this->check_out_end) {
            return $this->check_out_start . ' - ' . $this->check_out_end;
        }
        return 'Tidak diatur';
    }

    /**
     * Format grace period
     */
    public function getGracePeriodFormattedAttribute()
    {
        if ($this->grace_period_minutes > 0) {
            return $this->grace_period_minutes . ' menit';
        }
        return 'Tidak ada';
    }
}
