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
        Schema::create('journal_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teaching_journal_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->enum('type', ['file', 'link']);
            $table->string('path')->nullable(); // untuk file
            $table->text('url')->nullable(); // untuk link
            $table->string('file_name')->nullable(); // nama asli file
            $table->string('file_size')->nullable(); // ukuran file
            $table->string('mime_type')->nullable(); // tipe file
            $table->boolean('is_for_all_students')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_materials');
    }
};
