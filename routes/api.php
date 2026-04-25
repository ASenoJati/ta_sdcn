<?php

use App\Http\Controllers\Api\Admin\AttendanceSettingController;
use App\Http\Controllers\Api\Admin\LocationController;
use App\Http\Controllers\Api\Admin\RoleAttendanceController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/auth/google', [AuthController::class, 'handleGoogleCallback']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {

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
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});
