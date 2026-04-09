<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('picket_attendances', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $blueprint->date('date');
            $blueprint->time('check_in')->nullable();
            $blueprint->time('check_out')->nullable();
            $blueprint->string('status'); // Hadir, Izin, Sakit, Alpha
            $blueprint->boolean('is_extra')->default(false); // True if teacher checks in without being scheduled
            $blueprint->text('note')->nullable();
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('picket_attendances');
    }
};
