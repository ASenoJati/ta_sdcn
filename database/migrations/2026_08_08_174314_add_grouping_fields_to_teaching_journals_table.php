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
        Schema::table('teaching_journals', function (Blueprint $table) {
            // Tambahkan kolom untuk grouping
            $table->foreignId('user_id')->nullable()->after('teaching_schedule_id')->constrained();
            $table->foreignId('subject_id')->nullable()->after('user_id')->constrained();
            $table->foreignId('classroom_id')->nullable()->after('subject_id')->constrained();
            // Kita tetap pertahankan teaching_schedule_id untuk kompatibilitas, nanti bisa di-nullable
            $table->foreignId('teaching_schedule_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teaching_journals', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['subject_id']);
            $table->dropForeign(['classroom_id']);
            $table->dropColumn(['user_id', 'subject_id', 'classroom_id']);
            $table->foreignId('teaching_schedule_id')->nullable(false)->change();
        });
    }
};
