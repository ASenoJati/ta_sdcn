<?php

namespace App\Traits;

use Carbon\Carbon;

trait HasIndonesianDate
{
    /**
     * Get created_at in Indonesian format
     */
    public function getCreatedAtFormattedAttribute(): string
    {
        return $this->created_at ? $this->created_at->translatedFormat('d F Y H.i') : '-';
    }

    /**
     * Get updated_at in Indonesian format
     */
    public function getUpdatedAtFormattedAttribute(): string
    {
        return $this->updated_at ? $this->updated_at->translatedFormat('d F Y H.i') : '-';
    }

    /**
     * Get date only format
     */
    public function getCreatedDateAttribute(): string
    {
        return $this->created_at ? $this->created_at->translatedFormat('d F Y') : '-';
    }

    /**
     * Get time only format
     */
    public function getCreatedTimeAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('H.i') : '-';
    }

    /**
     * Untuk kolom tanggal tertentu, misal date_of_birth
     */
    public function getDateOfBirthFormattedAttribute(): string
    {
        return $this->date_of_birth ? Carbon::parse($this->date_of_birth)->translatedFormat('d F Y') : '-';
    }

    // Tambahkan method lain sesuai kebutuhan
}
