<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $tables = [
            'teaching_schedules',
            'teaching_journals',
            'student_attendances',
            'user_attendances',
            'classrooms',
            'students',
            'subjects',
            'lesson_hours',
            'locations',
            'attendance_time_settings',
            'role_attendance_times',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('academic_year_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('academic_years')
                    ->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        $tables = [
            'teaching_schedules',
            'teaching_journals',
            'student_attendances',
            'user_attendances',
            'classrooms',
            'students',
            'subjects',
            'lesson_hours',
            'locations',
            'attendance_time_settings',
            'role_attendance_times',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['academic_year_id']);
                $table->dropColumn('academic_year_id');
            });
        }
    }
};
