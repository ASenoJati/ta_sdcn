<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeachingJournal extends Model
{
    protected $fillable = [
        'teaching_schedule_id',
        'date',
        'material',
        'reflection',
    ];

    protected $casts = [
        'date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function teachingSchedule(): BelongsTo
    {
        return $this->belongsTo(TeachingSchedule::class, 'teaching_schedule_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(StudentAttendance::class, 'teaching_journal_id');
    }

    /**
     * Get formatted date.
     */
    public function getDateFormattedAttribute()
    {
        return $this->date->format('d/m/Y');
    }

    /**
     * Get day name in Indonesian.
     */
    public function getDayNameAttribute()
    {
        $days = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];
        return $days[$this->date->format('l')] ?? $this->date->format('l');
    }

    /**
     * Get summary of attendance.
     */
    public function getAttendanceSummaryAttribute()
    {
        // Ubah dari $this->studentAttendances menjadi $this->attendances
        $attendances = $this->attendances;

        // Jika data absensi kosong, pastikan kita mengembalikan collection kosong agar tidak error
        if (!$attendances) {
            $attendances = collect();
        }

        return [
            'hadir' => $attendances->where('status', 'hadir')->count(),
            'izin'  => $attendances->where('status', 'izin')->count(),
            'sakit' => $attendances->where('status', 'sakit')->count(),
            'alpa'  => $attendances->where('status', 'alpa')->count(),
            'total' => $attendances->count()
        ];
    }
}
