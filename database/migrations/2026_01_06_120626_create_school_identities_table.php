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
        Schema::create('school_identities', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Sekolah
            $table->string('npsn', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('headmaster_name')->nullable(); // Nama Kepsek
            $table->string('headmaster_nip')->nullable();  // NIP Kepsek
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_identities');
    }
};
