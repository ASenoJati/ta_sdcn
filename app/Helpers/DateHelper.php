<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Format tanggal Indonesia: 17 Mei 2026
     */
    public static function formatDate($date)
    {
        if (!$date) return '-';
        if (!($date instanceof Carbon)) {
            $date = Carbon::parse($date);
        }
        return $date->translatedFormat('d F Y');
    }

    /**
     * Format tanggal dan waktu Indonesia: 17 Mei 2026 16:44
     */
    public static function formatDateTime($date)
    {
        if (!$date) return '-';
        if (!($date instanceof Carbon)) {
            $date = Carbon::parse($date);
        }
        return $date->translatedFormat('d F Y H:i');
    }

    /**
     * Format waktu saja: 16:44
     */
    public static function formatTime($date)
    {
        if (!$date) return '-';
        if (!($date instanceof Carbon)) {
            $date = Carbon::parse($date);
        }
        return $date->translatedFormat('H:i');
    }

    /**
     * Format tanggal pendek: 17/05/2026 (jika diperlukan)
     */
    public static function formatShortDate($date)
    {
        if (!$date) return '-';
        if (!($date instanceof Carbon)) {
            $date = Carbon::parse($date);
        }
        return $date->translatedFormat('d/m/Y');
    }
}
