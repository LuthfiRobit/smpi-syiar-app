<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('teaching_material_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Modul Ajar, CP, ATP
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teaching_material_types');
    }
};
