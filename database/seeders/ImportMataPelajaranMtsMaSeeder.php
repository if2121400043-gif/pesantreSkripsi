<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lembaga;
use App\Models\MataPelajaran;

class ImportMataPelajaranMtsMaSeeder extends Seeder
{
    public function run()
    {
        // 1. Ambil Lembaga MTs & MA
        $lembagaMts = Lembaga::firstOrCreate(
            ['singkatan' => 'MTs'],
            ['nama' => 'Madrasah Tsanawiyah Nurul Furqon', 'jenjang' => 'MTS', 'is_active' => true]
        );

        $lembagaMa = Lembaga::firstOrCreate(
            ['singkatan' => 'MA'],
            ['nama' => 'Madrasah Aliyah Nurul Furqon', 'jenjang' => 'MA', 'is_active' => true]
        );

        // 2. Daftar Mata Pelajaran Komprehensif (Umum & Keagamaan Pesantren)
        $mapels = [
            ['kode' => 'TS', 'nama' => 'Tuntunan Shalat', 'tingkat' => 'Keagamaan'],
            ['kode' => 'TJ', 'nama' => 'Tajwid', 'tingkat' => 'Keagamaan'],
            ['kode' => 'IML', 'nama' => 'Imla\'', 'tingkat' => 'Keagamaan'],
            ['kode' => 'AY', 'nama' => 'A\'malul Yaum', 'tingkat' => 'Keagamaan'],
            ['kode' => 'TQ', 'nama' => 'Tahsinul Qur\'an', 'tingkat' => 'Keagamaan'],
            ['kode' => 'THF', 'nama' => 'Tahfidz Al-Qur\'an', 'tingkat' => 'Keagamaan'],
            ['kode' => 'THD', 'nama' => 'Tauhid', 'tingkat' => 'Keagamaan'],
            ['kode' => 'AK', 'nama' => 'Metode Al-Kamal', 'tingkat' => 'Keagamaan'],
            ['kode' => 'BAR', 'nama' => 'Bahasa Arab', 'tingkat' => 'Umum & Agama'],
            ['kode' => 'BIG', 'nama' => 'Bahasa Inggris', 'tingkat' => 'Umum'],
            ['kode' => 'BIN', 'nama' => 'Bahasa Indonesia', 'tingkat' => 'Umum'],
            ['kode' => 'MTK', 'nama' => 'Matematika', 'tingkat' => 'Umum'],
            ['kode' => 'IPA', 'nama' => 'IPA Terpadu', 'tingkat' => 'Umum'],
            ['kode' => 'IPS', 'nama' => 'IPS Terpadu', 'tingkat' => 'Umum'],
            ['kode' => 'FQH', 'nama' => 'Fiqih', 'tingkat' => 'Agama'],
            ['kode' => 'AA', 'nama' => 'Akidah Akhlak', 'tingkat' => 'Agama'],
            ['kode' => 'SKI', 'nama' => 'Sejarah Kebudayaan Islam', 'tingkat' => 'Agama'],
            ['kode' => 'QH', 'nama' => 'Al-Qur\'an Hadits', 'tingkat' => 'Agama'],
            ['kode' => 'PKN', 'nama' => 'Pendidikan Pancasila', 'tingkat' => 'Umum'],
            ['kode' => 'SB', 'nama' => 'Seni Budaya', 'tingkat' => 'Umum'],
            ['kode' => 'PJK', 'nama' => 'PJOK / Penjasorkes', 'tingkat' => 'Umum'],
            ['kode' => 'INF', 'nama' => 'Informatika / TIK', 'tingkat' => 'Umum'],
        ];

        $countMts = 0;
        $countMa = 0;

        foreach ($mapels as $order => $m) {
            // Impor untuk MTs
            MataPelajaran::firstOrCreate(
                [
                    'lembaga_id' => $lembagaMts->id,
                    'kode' => $m['kode'],
                ],
                [
                    'nama' => $m['nama'],
                    'tingkat' => $m['tingkat'],
                    'urutan' => $order + 1,
                    'is_active' => true,
                ]
            );
            $countMts++;

            // Impor untuk MA
            MataPelajaran::firstOrCreate(
                [
                    'lembaga_id' => $lembagaMa->id,
                    'kode' => $m['kode'],
                ],
                [
                    'nama' => $m['nama'],
                    'tingkat' => $m['tingkat'],
                    'urutan' => $order + 1,
                    'is_active' => true,
                ]
            );
            $countMa++;
        }

        $this->command->info("BERHASIL! Mengimpor {$countMts} Mata Pelajaran MTs dan {$countMa} Mata Pelajaran MA ke database.");
    }
}
