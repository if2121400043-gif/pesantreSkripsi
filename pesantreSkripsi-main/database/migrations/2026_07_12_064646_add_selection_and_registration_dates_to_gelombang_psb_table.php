<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('gelombang_psb', function (Blueprint $table) {
            $table->date('tanggal_seleksi_awal')->nullable()->after('tanggal_tutup');
            $table->date('tanggal_seleksi_akhir')->nullable()->after('tanggal_seleksi_awal');
            $table->date('tanggal_daftar_ulang_awal')->nullable()->after('tanggal_seleksi_akhir');
            $table->date('tanggal_daftar_ulang_akhir')->nullable()->after('tanggal_daftar_ulang_awal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gelombang_psb', function (Blueprint $table) {
            $table->dropColumn([
                'tanggal_seleksi_awal',
                'tanggal_seleksi_akhir',
                'tanggal_daftar_ulang_awal',
                'tanggal_daftar_ulang_akhir'
            ]);
        });
    }
};
