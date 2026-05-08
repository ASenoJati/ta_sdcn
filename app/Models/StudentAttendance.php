<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAttendance extends Model
{
    protected $fillable = [
        'teaching_journal_id',
        'student_id',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Status mapping
    public static $statuses = [
        'hadir' => ['label' => 'Hadir', 'badge' => 'success', 'icon' => 'bi-check-circle'],
        'izin' => ['label' => 'Izin', 'badge' => 'warning', 'icon' => 'bi-calendar-check'],
        'sakit' => ['label' => 'Sakit', 'badge' => 'info', 'icon' => 'bi-thermometer-half'],
        'alpa' => ['label' => 'Alpa', 'badge' => 'danger', 'icon' => 'bi-x-circle']
    ];

    public function journal(): BelongsTo
    {
        return $this->belongsTo(TeachingJournal::class, 'teaching_journal_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get status badge HTML.
     */
    public function getStatusBadgeAttribute()
    {
        $status = self::$statuses[$this->status] ?? ['badge' => 'secondary', 'label' => $this->status];
        return '<span class="badge bg-' . $status['badge'] . '"><i class="bi ' . $status['icon'] . '"></i> ' . $status['label'] . '</span>';
    }
}
