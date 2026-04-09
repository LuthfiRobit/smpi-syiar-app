<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('picket_schedules', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $blueprint->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $blueprint->string('day');
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('picket_schedules');
    }
};
