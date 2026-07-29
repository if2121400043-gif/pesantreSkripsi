<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Riwayat Jabatan Pegawai — mencatat setiap perpindahan jabatan
        Schema::create('riwayat_jabatan_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->string('jabatan', 100);
            $table->enum('jenis_pegawai', ['GURU', 'USTADZ', 'PENGASUH', 'STAFF_ADMIN', 'TENAGA_KEBERSIHAN', 'KEAMANAN', 'LAINNYA'])->nullable();
            $table->enum('status_kepegawaian', ['TETAP', 'KONTRAK', 'HONORER', 'SUKARELAWAN'])->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Riwayat Status Santri — audit log perubahan status
        Schema::create('riwayat_status_santri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_didik_id')->constrained('peserta_didik')->cascadeOnDelete();
            $table->string('status_lama', 30);
            $table->string('status_baru', 30);
            $table->date('tanggal_perubahan');
            $table->text('keterangan')->nullable();
            $table->foreignId('diubah_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_status_santri');
        Schema::dropIfExists('riwayat_jabatan_pegawai');
    }
};
