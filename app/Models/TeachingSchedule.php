<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeachingSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'subject_id',
        'classroom_id',
        'day',
        'lesson_hour_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public static $days = [
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu',
        'Sunday' => 'Minggu',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function journals(): HasMany
    {
        return $this->hasMany(TeachingJournal::class);
    }

    public function lessonHour(): BelongsTo
    {
        return $this->belongsTo(LessonHour::class);
    }

    public function getDayIndonesianAttribute()
    {
        return self::$days[$this->day] ?? $this->day;
    }

    /**
     * Get formatted schedule info.
     */
    public function getScheduleInfoAttribute()
    {
        return $this->subject->name . ' - ' . $this->classroom->name . ' (' . $this->dayIndonesian . ')';
    }

    /**
     * Get formatted created date.
     */
    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at->format('d/m/Y H:i');
    }
}
