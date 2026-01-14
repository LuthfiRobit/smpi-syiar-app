<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('teaching_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('teaching_material_type_id')->constrained('teaching_material_types')->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('set null');

            $table->string('grade_level'); // 7, 8, 9
            $table->enum('file_type', ['file', 'link']);
            $table->string('file_path')->nullable();
            $table->string('link_url')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_note')->nullable(); // Optional feedback

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teaching_materials');
    }
};
