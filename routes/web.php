<?php

use App\Http\Controllers\Web\Admin\AttendanceTimeSettingController;
use App\Http\Controllers\Web\Admin\ClassroomController;
use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\LocationsController;
use App\Http\Controllers\Web\Admin\RoleAttendanceTimeController;
use App\Http\Controllers\Web\Admin\StudentsController;
use App\Http\Controllers\Web\Admin\SubjectController;
use App\Http\Controllers\Web\Admin\UsersController;
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
        Route::resource('/classrooms', ClassroomController::class);
        Route::resource('/user', UsersController::class);
        Route::resource('/location', LocationsController::class);
        Route::resource('/role-attendance-times', RoleAttendanceTimeController::class);
        Route::resource('/attendance-setting', AttendanceTimeSettingController::class);
        Route::resource('/subjects', SubjectController::class);

        Route::get('students-data', [StudentsController::class, 'getData'])->name('students.data');
        Route::get('classrooms-data', [ClassroomController::class, 'getData'])->name('classrooms.data');
        Route::get('classrooms-list', [ClassroomController::class, 'getList'])->name('classrooms.list');
        Route::get('user-data', [UsersController::class, 'getData'])->name('user.data');
        Route::get('user-roles', [UsersController::class, 'getRoles'])->name('user.roles');
        Route::get('location-data', [LocationsController::class, 'getData'])->name('location.data');
        Route::post('location/{id}/set-default', [LocationsController::class, 'setDefault'])->name('location.set-default');
        Route::get('location-default', [LocationsController::class, 'getDefault'])->name('location.default');
        Route::get('role-attendance-times-data', [RoleAttendanceTimeController::class, 'getData'])->name('role-attendance-times.data');
        Route::get('attendance-settings-data', [AttendanceTimeSettingController::class, 'getData'])->name('attendance-settings.data');
        Route::get('attendance-settings-list', [AttendanceTimeSettingController::class, 'getList'])->name('attendance-settings.list');
        Route::get('subjects-data', [SubjectController::class, 'getData'])->name('subjects.data');
        Route::get('subjects-list', [SubjectController::class, 'getList'])->name('subjects.list');
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
