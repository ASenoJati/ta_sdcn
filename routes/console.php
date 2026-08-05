<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // ← Pastikan ini di-import[reference:5]

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('notifications:send-class-reminders')
    ->everyMinute() // Jalankan setiap menit
    ->withoutOverlapping(); // Cegah antrian tugas yang sama menumpuk[reference:6]