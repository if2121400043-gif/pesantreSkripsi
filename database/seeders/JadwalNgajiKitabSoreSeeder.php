<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Orang;
use App\Models\Pegawai;
use App\Models\Lembaga;
use App\Models\TahunPelajaran;
use App\Models\Rombel;
use App\Models\MataPelajaran;
use App\Models\JadwalPelajaran;

class JadwalNgajiKitabSoreSeeder extends Seeder
{
    public function run()
    {
        // 1. Cari Ustadz Makin (Khairil Makin Huda) Tanpa Duplikasi
        $orang = Orang::where('nama_lengkap', 'like', '%Khairil Makin%')
            ->orWhere('nama_lengkap', 'like', '%Makin%')
            ->first();

        if (!$orang) {
            $orang = Orang::create([
                'nama_lengkap' => 'Khairil Makin Huda',
                'niup' => 'GR-' . rand(10000, 99999),
                'is_active' => true,
            ]);
        }

        $pegawai = Pegawai::firstOrCreate(
            ['orang_id' => $orang->id],
            ['jenis_pegawai' => 'GURU', 'is_active' => true]
        );

        $this->command->info("Ustadz Makin terhubung: {$orang->nama_lengkap} (ID Pegawai: {$pegawai->id})");

        // 2. Dapatkan Tahun Pelajaran & Lembaga
        $tahunAktif = TahunPelajaran::where('is_active', true)->first() ?? TahunPelajaran::first();
        if (!$tahunAktif) {
            $this->command->error("Tahun Pelajaran tidak ditemukan.");
            return;
        }

        $lembagas = Lembaga::all();
        if ($lembagas->isEmpty()) {
            $this->command->error("Lembaga tidak ditemukan.");
            return;
        }

        // 3. Pastikan Mata Pelajaran 'Fathul Qorib' Ada untuk Setiap Lembaga
        $mapelKitabMap = [];
        foreach ($lembagas as $l) {
            $mapel = MataPelajaran::firstOrCreate(
                ['nama' => 'Fathul Qorib (Ngaji Kitab)', 'lembaga_id' => $l->id],
                ['is_active' => true]
            );
            $mapelKitabMap[$l->id] = $mapel->id;
        }

        // 4. Ambil Seluruh Rombel
        $rombels = Rombel::where('tahun_pelajaran_id', $tahunAktif->id)->get();
        if ($rombels->isEmpty()) {
            $rombels = Rombel::all();
        }

        $hariList = ['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'AHAD'];
        $count = 0;

        // 5. Tambahkan Sesi Ngaji Kitab Fathul Qorib (16:45 - 17:45 WIB) untuk Setiap Rombel & Setiap Hari
        foreach ($rombels as $rombel) {
            $mapelId = $mapelKitabMap[$rombel->lembaga_id] ?? reset($mapelKitabMap);

            foreach ($hariList as $hari) {
                JadwalPelajaran::updateOrCreate(
                    [
                        'rombel_id' => $rombel->id,
                        'hari' => $hari,
                        'jam_mulai' => '16:45',
                        'jam_selesai' => '17:45',
                    ],
                    [
                        'mata_pelajaran_id' => $mapelId,
                        'pegawai_id' => $pegawai->id,
                    ]
                );
                $count++;
            }
        }

        $this->command->info("Berhasil mengimpor {$count} sesi jadwal Ngaji Kitab Fathul Qorib (16:45 - 17:45 WIB) Ustadz Makin ke database.");
    }
}
