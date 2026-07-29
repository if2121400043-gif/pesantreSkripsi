<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Provinsi
        Schema::create('provinsi', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique();
            $table->string('nama', 100);
            $table->timestamps();
        });

        // Kabupaten / Kota
        Schema::create('kabupaten', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique();
            $table->string('nama', 100);
            $table->foreignId('provinsi_id')->constrained('provinsi')->cascadeOnDelete();
            $table->timestamps();
        });

        // Kecamatan
        Schema::create('kecamatan', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique();
            $table->string('nama', 100);
            $table->foreignId('kabupaten_id')->constrained('kabupaten')->cascadeOnDelete();
            $table->timestamps();
        });

        // Desa / Kelurahan
        Schema::create('desa', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 15)->unique();
            $table->string('nama', 100);
            $table->foreignId('kecamatan_id')->constrained('kecamatan')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('desa');
        Schema::dropIfExists('kecamatan');
        Schema::dropIfExists('kabupaten');
        Schema::dropIfExists('provinsi');
    }
};
