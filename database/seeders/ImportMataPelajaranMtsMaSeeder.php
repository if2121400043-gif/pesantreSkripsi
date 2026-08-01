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
            ['kode' => 'TS', 'nama' => 'Tuntunan Shalat', 'tingkat' => null],
            ['kode' => 'TJ', 'nama' => 'Tajwid', 'tingkat' => null],
            ['kode' => 'IML', 'nama' => 'Imla\'', 'tingkat' => null],
            ['kode' => 'AY', 'nama' => 'A\'malul Yaum', 'tingkat' => null],
            ['kode' => 'TQ', 'nama' => 'Tahsinul Qur\'an', 'tingkat' => null],
            ['kode' => 'THF', 'nama' => 'Tahfidz Al-Qur\'an', 'tingkat' => null],
            ['kode' => 'THD', 'nama' => 'Tauhid', 'tingkat' => null],
            ['kode' => 'AK', 'nama' => 'Metode Al-Kamal', 'tingkat' => null],
            ['kode' => 'BAR', 'nama' => 'Bahasa Arab', 'tingkat' => null],
            ['kode' => 'BIG', 'nama' => 'Bahasa Inggris', 'tingkat' => null],
            ['kode' => 'BIN', 'nama' => 'Bahasa Indonesia', 'tingkat' => null],
            ['kode' => 'MTK', 'nama' => 'Matematika', 'tingkat' => null],
            ['kode' => 'IPA', 'nama' => 'IPA Terpadu', 'tingkat' => null],
            ['kode' => 'IPS', 'nama' => 'IPS Terpadu', 'tingkat' => null],
            ['kode' => 'FQH', 'nama' => 'Fiqih', 'tingkat' => null],
            ['kode' => 'AA', 'nama' => 'Akidah Akhlak', 'tingkat' => null],
            ['kode' => 'SKI', 'nama' => 'Sejarah Kebudayaan Islam', 'tingkat' => null],
            ['kode' => 'QH', 'nama' => 'Al-Qur\'an Hadits', 'tingkat' => null],
            ['kode' => 'PKN', 'nama' => 'Pendidikan Pancasila', 'tingkat' => null],
            ['kode' => 'SB', 'nama' => 'Seni Budaya', 'tingkat' => null],
            ['kode' => 'PJK', 'nama' => 'PJOK / Penjasorkes', 'tingkat' => null],
            ['kode' => 'INF', 'nama' => 'Informatika / TIK', 'tingkat' => null],
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
