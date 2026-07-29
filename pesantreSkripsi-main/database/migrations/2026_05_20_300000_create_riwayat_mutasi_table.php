<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_mutasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_didik_id')->constrained('peserta_didik')->cascadeOnDelete();
            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajaran')->cascadeOnDelete();
            $table->enum('jenis_mutasi', ['ASRAMA', 'ROMBEL']);
            $table->string('dari_posisi')->nullable()->comment('Nama kamar/kelas sebelumnya');
            $table->string('ke_posisi')->comment('Nama kamar/kelas tujuan');
            $table->date('tanggal_mutasi');
            $table->text('keterangan')->nullable();
            $table->foreignId('diinput_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_mutasi');
    }
};
