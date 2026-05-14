<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classroom extends Model
{
    use SoftDeletes;

    protected $fillable = ['name'];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'classroom_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(TeachingSchedule::class);
    }
}
