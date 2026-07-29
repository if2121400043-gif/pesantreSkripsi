<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rombongan Belajar (Kelas)
        Schema::create('rombel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lembaga_id')->constrained('lembaga')->cascadeOnDelete();
            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajaran')->cascadeOnDelete();
            $table->string('nama', 50)->comment('Contoh: Kelas 7A, X-IPA-1');
            $table->string('tingkat', 10)->nullable()->comment('Contoh: 7, 8, 9, 10, 11, 12');
            $table->foreignId('wali_kelas_id')->nullable()->constrained('pegawai')->nullOnDelete();
            $table->integer('kapasitas')->default(30);
            $table->timestamps();

            $table->unique(['lembaga_id', 'tahun_pelajaran_id', 'nama'], 'rombel_unique');
        });

        // Pendaftaran Peserta ke Lembaga per Tahun
        Schema::create('peserta_lembaga_tahun', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_didik_id')->constrained('peserta_didik')->cascadeOnDelete();
            $table->foreignId('lembaga_id')->constrained('lembaga')->cascadeOnDelete();
            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajaran')->cascadeOnDelete();
            $table->enum('status', ['AKTIF', 'NAIK_KELAS', 'TINGGAL_KELAS', 'LULUS', 'MUTASI', 'KELUAR'])->default('AKTIF');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['peserta_didik_id', 'lembaga_id', 'tahun_pelajaran_id'], 'plt_peserta_lembaga_tahun_unique');
        });

        // Riwayat Penempatan di Rombel
        Schema::create('riwayat_rombel_peserta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_didik_id')->constrained('peserta_didik')->cascadeOnDelete();
            $table->foreignId('rombel_id')->constrained('rombel')->cascadeOnDelete();
            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajaran')->cascadeOnDelete();
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->enum('status', ['AKTIF', 'PINDAH', 'SELESAI'])->default('AKTIF');
            $table->timestamps();

            $table->unique(['peserta_didik_id', 'rombel_id', 'tahun_pelajaran_id'], 'rrp_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_rombel_peserta');
        Schema::dropIfExists('peserta_lembaga_tahun');
        Schema::dropIfExists('rombel');
    }
};
