<?php

use App\Http\Controllers\Web\Admin\ClassroomController;
use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\StudentsController;
use App\Http\Controllers\Web\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');

Route::middleware(['auth'])->group(function () {

    // Route Khusus Admin
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        // Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::resource('/students', StudentsController::class);
        Route::get('students-data', [StudentsController::class, 'getData'])->name('students.data');
        Route::get('classrooms-list', [ClassroomController::class, 'getList'])->name('classrooms.list');
    });

    // Route Khusus Teacher
    Route::middleware(['role:teacher'])->prefix('teacher')->group(function () {
        // Route::get('/dashboard', [TeacherController::class, 'index'])->name('teacher.dashboard');
    });

    // Route Khusus Staff
    Route::middleware(['role:staff'])->prefix('staff')->group(function () {
        // Route::get('/dashboard', [StaffController::class, 'index'])->name('staff.dashboard');
    });

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});
