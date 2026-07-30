<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calon_santri', function (Blueprint $table) {
            // Data Ayah Kandung (nama_ayah sudah ada)
            $table->string('nik_ayah', 20)->nullable()->after('nama_ayah');
            $table->string('tahun_lahir_ayah', 4)->nullable()->after('nik_ayah');
            $table->string('pendidikan_ayah', 50)->nullable()->after('tahun_lahir_ayah');
            $table->string('pekerjaan_ayah', 100)->nullable()->after('pendidikan_ayah');
            $table->string('penghasilan_ayah', 50)->nullable()->after('pekerjaan_ayah');
            $table->string('no_hp_ayah', 20)->nullable()->after('penghasilan_ayah');

            // Data Ibu Kandung (nama_ibu sudah ada)
            $table->string('nik_ibu', 20)->nullable()->after('nama_ibu');
            $table->string('tahun_lahir_ibu', 4)->nullable()->after('nik_ibu');
            $table->string('pendidikan_ibu', 50)->nullable()->after('tahun_lahir_ibu');
            $table->string('pekerjaan_ibu', 100)->nullable()->after('pendidikan_ibu');
            $table->string('penghasilan_ibu', 50)->nullable()->after('pekerjaan_ibu');
            $table->string('no_hp_ibu', 20)->nullable()->after('penghasilan_ibu');

            // Tinggal Bersama & Data Wali
            $table->string('tinggal_bersama', 50)->nullable()->after('telepon_wali');
            $table->string('nama_wali', 150)->nullable()->after('tinggal_bersama');
            $table->string('nik_wali', 20)->nullable()->after('nama_wali');
            $table->string('tahun_lahir_wali', 4)->nullable()->after('nik_wali');
            $table->string('pendidikan_wali', 50)->nullable()->after('tahun_lahir_wali');
            $table->string('pekerjaan_wali', 100)->nullable()->after('pendidikan_wali');
            $table->string('penghasilan_wali', 50)->nullable()->after('pekerjaan_wali');
            $table->string('no_hp_wali', 20)->nullable()->after('penghasilan_wali');
            $table->string('hubungan_wali', 50)->nullable()->after('no_hp_wali');
        });
    }

    public function down(): void
    {
        Schema::table('calon_santri', function (Blueprint $table) {
            $table->dropColumn([
                'nik_ayah', 'tahun_lahir_ayah', 'pendidikan_ayah', 'pekerjaan_ayah', 'penghasilan_ayah', 'no_hp_ayah',
                'nik_ibu', 'tahun_lahir_ibu', 'pendidikan_ibu', 'pekerjaan_ibu', 'penghasilan_ibu', 'no_hp_ibu',
                'tinggal_bersama', 'nama_wali', 'nik_wali', 'tahun_lahir_wali', 'pendidikan_wali', 'pekerjaan_wali', 'penghasilan_wali', 'no_hp_wali', 'hubungan_wali',
            ]);
        });
    }
};
