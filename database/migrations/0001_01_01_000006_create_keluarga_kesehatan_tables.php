<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hubungan Keluarga (relasi antara dua Orang)
        Schema::create('hubungan_keluarga', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orang_id')->constrained('orang')->cascadeOnDelete()->comment('Santri / anak');
            $table->foreignId('keluarga_id')->constrained('orang')->cascadeOnDelete()->comment('Orang tua / wali');
            $table->enum('hubungan', ['AYAH', 'IBU', 'WALI', 'KAKAK', 'ADIK', 'KAKEK', 'NENEK', 'PAMAN', 'BIBI', 'LAINNYA'])->default('AYAH');
            $table->boolean('is_mahrom')->default(false)->comment('Apakah termasuk mahrom');
            $table->boolean('boleh_jemput')->default(false);
            $table->boolean('boleh_kunjungi')->default(false);
            $table->boolean('boleh_komunikasi')->default(false);
            $table->boolean('is_wali_utama')->default(false)->comment('Wali utama yang menjadi penanggung jawab');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['orang_id', 'keluarga_id', 'hubungan'], 'hk_orang_keluarga_hubungan_unique');
        });

        // Data Kesehatan & Kebutuhan Khusus
        Schema::create('data_kesehatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orang_id')->constrained('orang')->cascadeOnDelete();
            $table->text('riwayat_penyakit')->nullable();
            $table->text('alergi')->nullable();
            $table->text('obat_rutin')->nullable();
            $table->string('golongan_darah_rhesus', 5)->nullable();
            $table->boolean('memiliki_disabilitas')->default(false);
            $table->enum('tingkat_disabilitas', ['RINGAN', 'SEDANG', 'BERAT'])->nullable();
            $table->text('jenis_disabilitas')->nullable();
            $table->text('kebutuhan_khusus')->nullable();
            $table->string('nama_kontak_darurat', 150)->nullable();
            $table->string('telepon_kontak_darurat', 20)->nullable();
            $table->string('hubungan_kontak_darurat', 50)->nullable();
            $table->timestamps();

            $table->unique('orang_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_kesehatan');
        Schema::dropIfExists('hubungan_keluarga');
    }
};
