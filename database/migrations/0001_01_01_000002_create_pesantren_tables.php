<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pesantren (hanya 1 record, profil yayasan)
        Schema::create('pesantren', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 200);
            $table->string('nspp', 30)->nullable()->comment('Nomor Statistik Pondok Pesantren');
            $table->text('alamat')->nullable();
            $table->foreignId('desa_id')->nullable()->constrained('desa')->nullOnDelete();
            $table->string('kode_pos', 10)->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('website', 200)->nullable();
            $table->string('logo')->nullable();
            $table->string('nama_pimpinan', 150)->nullable();
            $table->year('tahun_berdiri')->nullable();
            $table->text('visi')->nullable();
            $table->text('misi')->nullable();
            $table->timestamps();
        });

        // Lembaga di bawah Pesantren
        Schema::create('lembaga', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesantren_id')->constrained('pesantren')->cascadeOnDelete();
            $table->string('nama', 200);
            $table->string('singkatan', 30)->nullable();
            $table->enum('jenjang', ['PAUD', 'SD', 'SMP', 'SMA', 'MADIN', 'TAHFIDZ', 'PERGURUAN_TINGGI', 'NON_FORMAL', 'LAINNYA'])->default('LAINNYA');
            $table->enum('tipe', ['FORMAL', 'NON_FORMAL', 'PONDOK'])->default('FORMAL');
            $table->string('npsn', 20)->nullable()->comment('Nomor Pokok Sekolah Nasional');
            $table->boolean('is_active')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        // Tahun Pelajaran
        Schema::create('tahun_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesantren_id')->constrained('pesantren')->cascadeOnDelete();
            $table->string('nama', 20)->comment('Contoh: 2025/2026');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->unique(['pesantren_id', 'nama']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tahun_pelajaran');
        Schema::dropIfExists('lembaga');
        Schema::dropIfExists('pesantren');
    }
};
