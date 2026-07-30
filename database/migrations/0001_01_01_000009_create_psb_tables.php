<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Gelombang Pendaftaran PSB
        Schema::create('gelombang_psb', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesantren_id')->constrained('pesantren')->cascadeOnDelete();
            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajaran')->cascadeOnDelete();
            $table->string('nama', 100)->comment('Gelombang 1, Gelombang 2');
            $table->date('tanggal_buka');
            $table->date('tanggal_tutup');
            $table->integer('kuota')->default(0);
            $table->decimal('biaya_pendaftaran', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Calon Santri
        Schema::create('calon_santri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gelombang_id')->constrained('gelombang_psb')->cascadeOnDelete();
            $table->string('no_pendaftaran', 30)->unique();
            $table->string('nama_lengkap', 200);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('nik', 20)->nullable();
            $table->string('no_kk', 20)->nullable();
            $table->string('asal_sekolah', 200)->nullable();
            $table->string('nisn', 20)->nullable();
            $table->string('nama_ayah', 150)->nullable();
            $table->string('nama_ibu', 150)->nullable();
            $table->string('telepon_wali', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->foreignId('lembaga_tujuan_id')->nullable()->constrained('lembaga')->nullOnDelete();
            $table->enum('status', ['BARU_MASUK', 'HADIR_TES', 'DITERIMA', 'TIDAK_LULUS', 'DIBATALKAN'])->default('BARU_MASUK');
            $table->text('catatan_verifikasi')->nullable();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tanggal_verifikasi')->nullable();
            $table->timestamps();
        });

        // Dokumen Persyaratan PSB
        Schema::create('dokumen_psb', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_santri_id')->constrained('calon_santri')->cascadeOnDelete();
            $table->string('jenis_dokumen', 100)->comment('Akta, KK, Ijazah, Foto');
            $table->string('file_path');
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_psb');
        Schema::dropIfExists('calon_santri');
        Schema::dropIfExists('gelombang_psb');
    }
};
