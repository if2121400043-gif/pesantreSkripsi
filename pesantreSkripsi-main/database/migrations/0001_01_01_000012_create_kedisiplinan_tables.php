<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_pelanggaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesantren_id')->constrained('pesantren')->cascadeOnDelete();
            $table->string('nama', 150);
            $table->enum('kategori', ['RINGAN', 'SEDANG', 'BERAT'])->default('RINGAN');
            $table->integer('poin')->default(0);
            $table->timestamps();
        });

        Schema::create('catatan_pelanggaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_didik_id')->constrained('peserta_didik')->cascadeOnDelete();
            $table->foreignId('jenis_pelanggaran_id')->constrained('jenis_pelanggaran')->cascadeOnDelete();
            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajaran')->cascadeOnDelete();
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->string('tindakan', 200)->nullable();
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('catatan_prestasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_didik_id')->constrained('peserta_didik')->cascadeOnDelete();
            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajaran')->cascadeOnDelete();
            $table->string('judul', 200);
            $table->enum('tingkat', ['INTERNAL', 'KECAMATAN', 'KABUPATEN', 'PROVINSI', 'NASIONAL', 'INTERNASIONAL'])->default('INTERNAL');
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('perizinan_keluar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_didik_id')->constrained('peserta_didik')->cascadeOnDelete();
            $table->enum('jenis', ['PULANG', 'KELUAR_SEMENTARA', 'SAKIT', 'KEPERLUAN_KHUSUS'])->default('PULANG');
            $table->datetime('waktu_keluar');
            $table->datetime('waktu_kembali_rencana')->nullable();
            $table->datetime('waktu_kembali_aktual')->nullable();
            $table->string('dijemput_oleh', 150)->nullable();
            $table->string('hubungan_penjemput', 50)->nullable();
            $table->text('alasan')->nullable();
            $table->enum('status', ['MENUNGGU', 'DISETUJUI', 'DITOLAK', 'SELESAI'])->default('MENUNGGU');
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tanggal_persetujuan')->nullable();
            $table->text('catatan_persetujuan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perizinan_keluar');
        Schema::dropIfExists('catatan_prestasi');
        Schema::dropIfExists('catatan_pelanggaran');
        Schema::dropIfExists('jenis_pelanggaran');
    }
};
