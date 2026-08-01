<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetKelasMapelSeeder extends Seeder
{
    /**
     * Clear and reset all Rombel (Kelas), Mata Pelajaran, Jadwal Pelajaran,
     * Presensi Kelas, Nilai Rapor, and Plotting Kelas tables.
     * 
     * Keeps ALL Guru (Pegawai), Santri (Peserta Didik), Users, Orang, Lembaga,
     * Tahun Pelajaran, and Asrama data 100% INTACT.
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        // Truncate class, subject, schedule, attendance, grades, and class plotting tables
        DB::table('jadwal_pelajaran')->truncate();
        DB::table('presensi_kelas')->truncate();
        DB::table('nilai_rapor')->truncate();
        DB::table('riwayat_rombel_peserta')->truncate();
        DB::table('rombel')->truncate();
        DB::table('mata_pelajaran')->truncate();

        Schema::enableForeignKeyConstraints();

        $this->command->info('BERHASIL! Data kelas (rombel), mata pelajaran, jadwal, presensi kelas, nilai rapor, dan plotting kelas telah dibersihkan total. Data Guru, Santri, User, Lembaga, dan Tahun Pelajaran TETAP UTUH.');
    }
}
