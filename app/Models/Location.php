<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use SoftDeletes;

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

    /**
     * Scope untuk mendapatkan lokasi default
     */
    public function scopeDefault($query)
    {
        return $query->where('default', true);
    }

    /**
     * Boot method untuk handle event
     */
    protected static function boot()
    {
        parent::boot();

        // Sebelum menyimpan, pastikan hanya satu lokasi yang default
        static::saving(function ($model) {
            if ($model->default) {
                static::where('default', true)
                    ->where('id', '!=', $model->id)
                    ->update(['default' => false]);
            }
        });
    }
}
