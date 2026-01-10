<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('teaching_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');
            $table->date('date');
            $table->string('topic'); // Materi bahasan
            $table->text('description')->nullable(); // Detail kegiatan
            $table->string('photo_path')->nullable(); // Bukti foto
            $table->enum('status', ['Draft', 'Submitted', 'Approved'])->default('Draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teaching_journals');
    }
};
