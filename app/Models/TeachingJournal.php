<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeachingJournal extends Model
{
    protected $fillable = [
        'teaching_schedule_id',
        'date',
        'material',
        'reflection',
    ];
}
