<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    protected $fillable = [
        'teaching_journal_id',
        'student_id',
        'status',
    ];
}
