<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeachingJournal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'teaching_schedule_id',
        'date',
        'material',
        'reflection'
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

    public function materials()
    {
        return $this->hasMany(JournalMaterial::class);
    }

    public function getMaterialsForStudent($studentId = null)
    {
        if ($studentId) {
            return $this->materials()->where(function ($query) use ($studentId) {
                $query->where('is_for_all_students', true)
                    ->orWhereHas('students', function ($q) use ($studentId) {
                        $q->where('student_id', $studentId);
                    });
            })->get();
        }
        return $this->materials()->where('is_for_all_students', true)->get();
    }

    public function schedules()
    {
        return $this->belongsToMany(TeachingSchedule::class, 'journal_schedules', 'teaching_journal_id', 'teaching_schedule_id');
    }

    // Aksesori: ambil jadwal pertama (untuk keperluan tampilan ringkas)
    public function getFirstScheduleAttribute()
    {
        return $this->schedules()->first();
    }

    // Aksesori: gabungan sesi (misal "1, 2")
    public function getSessionsAttribute()
    {
        return $this->schedules->pluck('lessonHour.session')->unique()->implode(', ');
    }

    // Aksesori: range waktu (start min, end max)
    public function getTimeRangeAttribute()
    {
        $start = $this->schedules->pluck('lessonHour.start_time')->min();
        $end = $this->schedules->pluck('lessonHour.end_time')->max();
        return $start && $end ? "$start - $end" : '-';
    }

    // Tambahkan method untuk mendapatkan semua jam pelajaran yang tergabung
    public function getLessonHoursAttribute()
    {
        return $this->schedules->pluck('lessonHour')->unique('id');
    }
}
