<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Orang — Master Identitas Tunggal
        Schema::create('orang', function (Blueprint $table) {
            $table->id();
            $table->string('niup', 20)->unique()->comment('Nomor Induk Unik Pesantren, format: NIUP-YYYY-NNNNNN');
            $table->string('nama_lengkap', 200);
            $table->string('nama_panggilan', 50)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('nik', 20)->nullable()->unique()->comment('Nomor Induk Kependudukan');
            $table->string('no_kk', 20)->nullable()->comment('Nomor Kartu Keluarga');
            $table->string('no_paspor', 30)->nullable();
            $table->enum('golongan_darah', ['A', 'B', 'AB', 'O'])->nullable();
            $table->string('kewarganegaraan', 50)->default('Indonesia');
            $table->integer('anak_ke')->nullable();
            $table->integer('jumlah_saudara')->nullable();
            $table->text('alamat_lengkap')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->foreignId('desa_id')->nullable()->constrained('desa')->nullOnDelete();
            $table->string('kode_pos', 10)->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('foto')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('nama_lengkap');
            $table->index('tanggal_lahir');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orang');
    }
};
