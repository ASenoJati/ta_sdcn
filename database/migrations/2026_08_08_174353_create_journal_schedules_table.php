<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('journal_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teaching_journal_id')->constrained()->onDelete('cascade');
            $table->foreignId('teaching_schedule_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['teaching_journal_id', 'teaching_schedule_id'], 'unique_journal_schedule');
        });
    }

    public function down()
    {
        Schema::dropIfExists('journal_schedules');
    }
};
