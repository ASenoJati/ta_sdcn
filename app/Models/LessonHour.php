<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LessonHour extends Model
{
    protected $fillable = [
        'session',
        'start_time',
        'end_time',
    ];

    public function teachingSchedules(): HasMany
    {
        return $this->hasMany(TeachingSchedule::class);
    }
}
