<?php

use App\Http\Controllers\Api\Admin\AttendanceSettingController;
use App\Http\Controllers\Api\Admin\LocationController;
use App\Http\Controllers\Api\Admin\RoleAttendanceController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JournalController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/auth/google', [AuthController::class, 'handleGoogleCallback']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Check-in
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
    // Check-out
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkout']);
    // History Absensi (Index)
    Route::get('/attendance/history', [AttendanceController::class, 'index']);

    // Khusus Admin
    Route::middleware('role:admin')->group(function () {
        // Route::get('/admin/dashboard', [AdminController::class, 'index']);

        Route::apiResource('users', UserController::class);
        Route::apiResource('attendance-settings', AttendanceSettingController::class);
        Route::apiResource('role-attendance', RoleAttendanceController::class);
        Route::apiResource('locations', LocationController::class);
    });

    // Khusus Teacher
    Route::middleware('role:teacher')->group(function () {
        // List jadwal hari ini
        Route::get('/journals/schedules', [JournalController::class, 'index']);
        // List siswa untuk diabsen
        Route::get('/journals/students/{schedule_id}', [JournalController::class, 'getStudentsBySchedule']);
        // 1. Simpan Presensi
        Route::post('/journals/attendance', [JournalController::class, 'storeAttendance']);
        // 2. Simpan Refleksi (Endpoint Terpisah)
        Route::put('/journals/{id}/reflection', [JournalController::class, 'storeReflection']);

        Route::get('/journals/{schedule_id}/detail', [JournalController::class, 'detail']);

        Route::put('/journals/{id}/update', [JournalController::class, 'update']);
        // List semua jadwal dalam satu pekan
        Route::get('/journals/schedules/all', [JournalController::class, 'allSchedules']);
    });
});
