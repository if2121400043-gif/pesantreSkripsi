<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Asrama
        Schema::create('asrama', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesantren_id')->constrained('pesantren')->cascadeOnDelete();
            $table->string('nama', 100);
            $table->string('kode', 20)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P', 'CAMPURAN'])->default('L');
            $table->integer('kapasitas')->default(0);
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Kamar di dalam Asrama
        Schema::create('kamar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asrama_id')->constrained('asrama')->cascadeOnDelete();
            $table->string('nama', 50);
            $table->string('lantai', 10)->nullable();
            $table->integer('kapasitas')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Penempatan Mukim per Tahun Pelajaran
        Schema::create('peserta_mukim_tahun', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_didik_id')->constrained('peserta_didik')->cascadeOnDelete();
            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajaran')->cascadeOnDelete();
            $table->foreignId('kamar_id')->nullable()->constrained('kamar')->nullOnDelete();
            $table->enum('status_mukim', ['MUKIM', 'TIDAK_MUKIM'])->default('MUKIM');
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['peserta_didik_id', 'tahun_pelajaran_id'], 'pmt_peserta_tahun_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta_mukim_tahun');
        Schema::dropIfExists('kamar');
        Schema::dropIfExists('asrama');
    }
};
