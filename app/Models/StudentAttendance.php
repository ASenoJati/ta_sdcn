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

    public function journal(): BelongsTo
    {
        return $this->belongsTo(TeachingJournal::class, 'teaching_journal_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
