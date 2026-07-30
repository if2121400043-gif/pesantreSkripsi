<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Peserta Didik (profil santri/siswa)
        Schema::create('peserta_didik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orang_id')->constrained('orang')->cascadeOnDelete();
            $table->string('nis', 30)->nullable()->comment('Nomor Induk Siswa di lembaga');
            $table->string('nisn', 20)->nullable()->unique()->comment('Nomor Induk Siswa Nasional');
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->enum('status', ['AKTIF', 'LULUS', 'MUTASI_KELUAR', 'DIKELUARKAN', 'MENGUNDURKAN_DIRI', 'MENINGGAL'])->default('AKTIF');
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
        });

        // Pegawai (profil guru/staf/ustadz)
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orang_id')->constrained('orang')->cascadeOnDelete();
            $table->string('nip', 30)->nullable()->comment('Nomor Induk Pegawai');
            $table->string('nuptk', 20)->nullable()->unique()->comment('Nomor Unik Pendidik dan Tenaga Kependidikan');
            $table->enum('jenis_pegawai', ['GURU', 'USTADZ', 'PENGASUH', 'STAFF_ADMIN', 'TENAGA_KEBERSIHAN', 'KEAMANAN', 'LAINNYA'])->default('GURU');
            $table->string('jabatan', 100)->nullable();
            $table->enum('status_kepegawaian', ['TETAP', 'KONTRAK', 'HONORER', 'SUKARELAWAN'])->default('TETAP');
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->enum('pendidikan_terakhir', ['SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'S1', 'S2', 'S3'])->nullable();
            $table->string('jurusan_pendidikan', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai');
        Schema::dropIfExists('peserta_didik');
    }
};
