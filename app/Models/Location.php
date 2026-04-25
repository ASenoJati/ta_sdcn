<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'name',
        'longitude',
        'latitude',
        'radius_km',
        'default',
        'address',
        'description'
    ];

    protected $casts = [
        'longitude' => 'float',
        'latitude' => 'float',
        'radius_km' => 'integer',
        'default' => 'boolean',
    ];
}
