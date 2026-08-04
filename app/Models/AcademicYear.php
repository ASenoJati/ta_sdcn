<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relasi ke semua tabel yang membutuhkan tahun ajaran
    public function teachingSchedules()
    {
        return $this->hasMany(TeachingSchedule::class);
    }

    public function teachingJournals()
    {
        return $this->hasMany(TeachingJournal::class);
    }

    public function studentAttendances()
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function userAttendances()
    {
        return $this->hasMany(UserAttendance::class);
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function lessonHours()
    {
        return $this->hasMany(LessonHour::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function attendanceTimeSettings()
    {
        return $this->hasMany(AttendanceTimeSetting::class);
    }

    public function roleAttendanceTimes()
    {
        return $this->hasMany(RoleAttendanceTime::class);
    }

    // Scope untuk mendapatkan tahun ajaran aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessor untuk periode
    public function getPeriodAttribute()
    {
        return $this->start_date->format('d/m/Y') . ' - ' . $this->end_date->format('d/m/Y');
    }

    // Accessor untuk status label
    public function getStatusLabelAttribute()
    {
        return $this->is_active ? 'Aktif' : 'Tidak Aktif';
    }
}
