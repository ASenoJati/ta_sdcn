<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendance_time_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->time('check_in_start')->comment('Start time for check-in');
            $table->time('check_in_end')->comment('End time for check-in');
            $table->time('check_out_start')->comment('Start time for check-out');
            $table->time('check_out_end')->comment('End time for check-out');
            $table->unsignedInteger('grace_period_minutes')->default(0)->comment('Grace period in minutes for late check-in');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_time_settings');
    }
};
