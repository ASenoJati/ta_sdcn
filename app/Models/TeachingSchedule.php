<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeachingSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject_id',
        'classroom_id',
        'day',
        'start_time',
        'end_time',
    ];

    /**
     * Relasi ke Model Subject (Mata Pelajaran)
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Relasi ke Model Classroom (Kelas)
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Relasi ke Model User (Guru)
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
