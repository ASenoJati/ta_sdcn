<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAttendance extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'location_id',
        'attendance_date',
        'check_in_time',
        'check_out_time',
        'check_in_latitude',
        'check_in_longitude',
        'check_out_latitude',
        'check_out_longitude',
        'check_in_status',
        'check_out_status',
        'image_in',
        'image_out',
        'notes',
    ];

    protected $casts = [
        'check_in_status' => AttendanceStatus::class,
        'check_out_status' => AttendanceStatus::class,
        'attendance_date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];

    /**
     * Get the user that owns the attendance.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the location that owns the attendance.
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get check-in status badge.
     */
    public function getCheckInStatusBadgeAttribute()
    {
        $statuses = [
            'on_time' => ['label' => 'Tepat Waktu', 'badge' => 'success', 'icon' => 'bi-check-circle'],
            'late' => ['label' => 'Terlambat', 'badge' => 'warning', 'icon' => 'bi-exclamation-triangle'],
            'present' => ['label' => 'Hadir', 'badge' => 'primary', 'icon' => 'bi-person-check'],
            // 'absent' adalah fallback jika data null
            'absent' => ['label' => 'Tidak Hadir', 'badge' => 'danger', 'icon' => 'bi-x-circle']
        ];

        // Ambil string value dari Enum jika tidak null, jika null pakai 'absent'
        $key = $this->check_in_status ? $this->check_in_status->value : 'absent';

        $status = $statuses[$key] ?? [
            'label' => $key,
            'badge' => 'secondary',
            'icon' => 'bi-question-circle'
        ];

        return '<span class="badge bg-' . $status['badge'] . '"><i class="bi ' . $status['icon'] . '"></i> ' . $status['label'] . '</span>';
    }

    /**
     * Get check-out status badge.
     */
    public function getCheckOutStatusBadgeAttribute()
    {
        if (!$this->check_out_status) {
            return '<span class="badge bg-secondary"><i class="bi bi-clock"></i> Belum Check-out</span>';
        }

        $statuses = [
            'on_time' => ['label' => 'Tepat Waktu', 'badge' => 'success', 'icon' => 'bi-check-circle'],
            'early' => ['label' => 'Pulang Awal', 'badge' => 'warning', 'icon' => 'bi-clock-history'],
            'late' => ['label' => 'Pulang Terlambat', 'badge' => 'info', 'icon' => 'bi-moon']
        ];

        // Karena sudah di-cast ke Enum, kita harus ambil ->value
        $key = $this->check_out_status->value;

        $status = $statuses[$key] ?? [
            'label' => $key,
            'badge' => 'secondary',
            'icon' => 'bi-question-circle'
        ];

        return '<span class="badge bg-' . $status['badge'] . '"><i class="bi ' . $status['icon'] . '"></i> ' . $status['label'] . '</span>';
    }

    /**
     * Get formatted check-in time.
     */
    public function getCheckInTimeFormattedAttribute()
    {
        return $this->check_in_time ? $this->check_in_time->format('H:i:s') : '-';
    }

    /**
     * Get formatted check-out time.
     */
    public function getCheckOutTimeFormattedAttribute()
    {
        return $this->check_out_time ? $this->check_out_time->format('H:i:s') : '-';
    }

    /**
     * Get date formatted.
     */
    public function getAttendanceDateFormattedAttribute()
    {
        return $this->attendance_date->format('d/m/Y');
    }

    /**
     * Get work duration.
     */
    public function getWorkDurationAttribute()
    {
        if (!$this->check_in_time || !$this->check_out_time) {
            return '-';
        }

        $diff = $this->check_in_time->diff($this->check_out_time);
        return sprintf('%d jam %d menit', $diff->h, $diff->i);
    }
}
