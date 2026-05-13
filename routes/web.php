<?php

use App\Http\Controllers\Web\Admin\AttendanceTimeSettingController;
use App\Http\Controllers\Web\Admin\ClassroomController;
use App\Http\Controllers\Web\Admin\ClassroomScheduleController;
use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\LessonHourController;
use App\Http\Controllers\Web\Admin\LocationsController;
use App\Http\Controllers\Web\Admin\MoveClassController;
use App\Http\Controllers\Web\Admin\RoleAttendanceTimeController;
use App\Http\Controllers\Web\Admin\StudentsController;
use App\Http\Controllers\Web\Admin\SubjectController;
use App\Http\Controllers\Web\Admin\TeachingJournalController;
use App\Http\Controllers\Web\Admin\TeachingScheduleController;
use App\Http\Controllers\Web\Admin\UserAttendanceController;
use App\Http\Controllers\Web\Admin\UsersController;
use App\Http\Controllers\Web\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');

Route::middleware(['auth'])->group(function () {

    // Route Khusus Admin
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/dashboard-charts', [DashboardController::class, 'getChartData'])->name('admin.dashboard.charts');

        Route::resource('/students', StudentsController::class);
        Route::resource('/classrooms', ClassroomController::class);
        Route::resource('/user', UsersController::class);
        Route::resource('/location', LocationsController::class);
        Route::resource('/role-attendance-times', RoleAttendanceTimeController::class);
        Route::resource('/attendance-setting', AttendanceTimeSettingController::class);
        Route::resource('/subjects', SubjectController::class);
        Route::resource('/lesson-hours', LessonHourController::class);
        Route::resource('/teaching-schedules', TeachingScheduleController::class);
        Route::resource('/teaching-journals', TeachingJournalController::class);
        Route::resource('/user-attendances', UserAttendanceController::class);

        Route::get('/move-class', [MoveClassController::class, 'index'])->name('move-class.index');
        Route::get('/move-class/get-students', [MoveClassController::class, 'getStudents'])->name('move-class.get-students');
        Route::post('/move-class/move', [MoveClassController::class, 'moveStudents'])->name('move-class.move');

        Route::get('students-data', [StudentsController::class, 'getData'])->name('students.data');
        Route::get('classrooms-data', [ClassroomController::class, 'getData'])->name('classrooms.data');
        Route::get('classrooms-list', [ClassroomController::class, 'getList'])->name('classrooms.list');
        Route::get('/classrooms/{id}/students', [ClassroomController::class, 'showStudents'])->name('classrooms.students');
        Route::get('/classrooms/{id}/students-data', [ClassroomController::class, 'getStudentsData'])->name('classrooms.students.data');
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
        Route::get('lesson-hours-data', [LessonHourController::class, 'getData'])->name('lesson-hours.data');
        Route::get('lesson-hours-list', [LessonHourController::class, 'getList'])->name('lesson-hours.list');
        Route::get('teaching-schedules-data', [TeachingScheduleController::class, 'getData'])->name('teaching-schedules.data');
        Route::get('teaching-schedules-teachers', [TeachingScheduleController::class, 'getTeachers'])->name('teaching-schedules.teachers');
        Route::get('teaching-schedules-lesson-hours', [TeachingScheduleController::class, 'getLessonHours'])->name('teaching-schedules.lesson-hours');
        Route::post('teaching-schedules-check-availability', [TeachingScheduleController::class, 'checkAvailability'])->name('teaching-schedules.check-availability');
        Route::get('teaching-schedules/classroom/{id}', [TeachingScheduleController::class, 'showByClassroom'])->name('teaching-schedules.by-classroom');
        Route::get('teaching-schedules/classroom/{id}/data', [TeachingScheduleController::class, 'getScheduleByClassroom'])->name('teaching-schedules.by-classroom.data');
        Route::get('teaching-journals-data', [TeachingJournalController::class, 'getData'])->name('teaching-journals.data');
        Route::get('user-attendances-data', [UserAttendanceController::class, 'getData'])->name('user-attendances.data');

        // Classroom Schedules routes
        Route::get('/classroom-schedules', [ClassroomScheduleController::class, 'index'])->name('classroom-schedules.index');
        Route::get('/classroom-schedules/data', [ClassroomScheduleController::class, 'getData'])->name('classroom-schedules.data');
        Route::get('/classroom-schedules/{id}', [ClassroomScheduleController::class, 'show'])->name('classroom-schedules.show');
        Route::get('/classroom-schedules/{id}/schedule-data', [ClassroomScheduleController::class, 'getScheduleData'])->name('classroom-schedules.schedule-data');

        // Schedule CRUD
        Route::post('/classroom-schedules/schedule', [ClassroomScheduleController::class, 'storeSchedule'])->name('classroom-schedules.store-schedule');
        Route::put('/classroom-schedules/schedule/{id}', [ClassroomScheduleController::class, 'updateSchedule'])->name('classroom-schedules.update-schedule');
        Route::delete('/classroom-schedules/schedule/{id}', [ClassroomScheduleController::class, 'destroySchedule'])->name('classroom-schedules.destroy-schedule');
        Route::get('/classroom-schedules/schedule/{id}/edit', [ClassroomScheduleController::class, 'editSchedule'])->name('classroom-schedules.edit-schedule');

        // Supporting routes
        Route::get('/classroom-schedules/teachers/list', [ClassroomScheduleController::class, 'getTeachers'])->name('classroom-schedules.teachers');
        Route::get('/classroom-schedules/lesson-hours/list', [ClassroomScheduleController::class, 'getLessonHours'])->name('classroom-schedules.lesson-hours');
        Route::post('/classroom-schedules/check-availability', [ClassroomScheduleController::class, 'checkAvailability'])->name('classroom-schedules.check-availability');

        // User Profile routes
        Route::get('/profile', [UsersController::class, 'profile'])->name('user.profile');
        Route::post('/profile/update', [UsersController::class, 'updateProfile'])->name('user.update-profile');
        Route::post('/profile/update-password', [UsersController::class, 'updatePassword'])->name('user.update-password');
    });

    // Route Khusus Teacher
    Route::middleware(['role:teacher'])->prefix('teacher')->group(function () {
        Route::get('/dashboard', function () {
            return "<h1>Dashboard Guru</h1><p>Selamat datang di dashboard guru!</p>";
        })->name('teacher.dashboard');
    });

    // Route Khusus Staff
    Route::middleware(['role:staff'])->prefix('staff')->group(function () {
        Route::get('/dashboard', function () {
            return "<h1>Dashboard Staff</h1><p>Selamat datang di dashboard staff!</p>";
        })->name('staff.dashboard');
    });

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/storage/{path}', function ($path) {
    $file = storage_path('app/public/' . $path);

    if (!File::exists($file)) {
        abort(404);
    }

    return Response::file($file);
})->where('path', '.*');
