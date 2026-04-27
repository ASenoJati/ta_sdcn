<?php

use App\Http\Controllers\Api\Admin\AttendanceSettingController;
use App\Http\Controllers\Api\Admin\LocationController;
use App\Http\Controllers\Api\Admin\RoleAttendanceController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JournalController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/auth/google', [AuthController::class, 'handleGoogleCallback']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/profile', [ProfileController::class, 'show']);

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
        // Route::get('/teacher/classes', [TeacherController::class, 'index']);

        // List jadwal hari ini
        Route::get('/journals/schedules', [JournalController::class, 'index']);
        // List siswa untuk diabsen
        Route::get('/journals/students/{schedule_id}', [JournalController::class, 'getStudentsBySchedule']);
        // 1. Simpan Presensi
        Route::post('/journals/attendance', [JournalController::class, 'storeAttendance']);
        // 2. Simpan Refleksi (Endpoint Terpisah)
        Route::put('/journals/{id}/reflection', [JournalController::class, 'storeReflection']);
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});
