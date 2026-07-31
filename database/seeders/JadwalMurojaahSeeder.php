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

class JadwalMurojaahSeeder extends Seeder
{
    public function run()
    {
        // 1. Map Nama Panggilan Pilihan Pengguna ke Data Master Guru yang Ada (STRICT NO-DUPLICATE)
        $panggilanMap = [
            'Sumail' => ['Sumail', 'Ustadz Sumail'],
            'Umam'   => ['Umam', 'Ach Khairul Umam', 'Khairul Umam'],
            'Makin'  => ['Makin', 'Khairil Makin Huda', 'Khairil Makin'],
            'Aulia'  => ['Aulia', 'Sayyidah Aulia Ul Haqqu', 'Sayyidah Aulia U', 'Sayyidah Aulia'],
            'Imam'   => ['Imam', 'Imam Malik Al-Bukhori', 'Imam Malik'],
            'Ilham'  => ['Ilham', 'Ilham Maulana'],
            'Afiez'  => ['Afiez', 'Afiez Muhajir', 'Afiz Muhajir'],
        ];

        $guruPegawaiIds = [];
        $foundTeachersInfo = [];

        foreach ($panggilanMap as $shortName => $possibleNames) {
            $orang = null;
            foreach ($possibleNames as $nameToSearch) {
                $orang = Orang::where('nama_lengkap', 'like', "%{$nameToSearch}%")->first();
                if ($orang) break;
            }

            // Jika sama sekali tidak ditemukan, cari berdasarkan 1 kata kunci
            if (!$orang) {
                $orang = Orang::where('nama_lengkap', 'like', "%{$shortName}%")->first();
            }

            if ($orang) {
                $pegawai = Pegawai::firstOrCreate(
                    ['orang_id' => $orang->id],
                    ['jenis_pegawai' => 'GURU', 'is_active' => true]
                );
                $guruPegawaiIds[] = $pegawai->id;
                $foundTeachersInfo[] = "{$shortName} -> " . $orang->nama_lengkap . " (ID Pegawai: {$pegawai->id})";
            } else {
                // Jangan buat baru jika tidak ditemukan demi keamanan duplikasi, catat warning
                $this->command->warn("Warning: Guru dengan nama panggilan '{$shortName}' tidak ditemukan di database master orang.");
            }
        }

        $this->command->info("Guru terhubung: " . implode(" | ", $foundTeachersInfo));

        if (empty($guruPegawaiIds)) {
            $this->command->error("Tidak ada ID Pegawai yang berhasil dihubungkan.");
            return;
        }

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

        // 3. Pastikan Mata Pelajaran 'Murojaah' Ada untuk Setiap Lembaga
        $mapelMurojaahMap = [];
        foreach ($lembagas as $l) {
            $mapel = MataPelajaran::firstOrCreate(
                ['nama' => 'Murojaah', 'lembaga_id' => $l->id],
                ['is_active' => true]
            );
            $mapelMurojaahMap[$l->id] = $mapel->id;
        }

        // 4. Ambil Seluruh Rombel
        $rombels = Rombel::where('tahun_pelajaran_id', $tahunAktif->id)->get();
        if ($rombels->isEmpty()) {
            $rombels = Rombel::all();
        }

        $hariList = ['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'AHAD'];
        $slotJamList = [
            ['03:00', '04:00'],
            ['05:00', '06:00'],
            ['18:30', '19:30'],
        ];

        $count = 0;
        $guruIndex = 0;
        $totalGurus = count($guruPegawaiIds);

        // 5. Tambahkan Sesi Murojaah (03:00-04:00, 05:00-06:00, 18:30-19:30 WIB) untuk Setiap Rombel & Setiap Hari
        foreach ($rombels as $rombel) {
            $mapelId = $mapelMurojaahMap[$rombel->lembaga_id] ?? reset($mapelMurojaahMap);

            foreach ($slotJamList as $slot) {
                $jamMulai = $slot[0];
                $jamSelesai = $slot[1];

                foreach ($hariList as $hari) {
                    // Rotasi pembimbing murojaah per rombel & hari secara merata
                    $assignedPegawaiId = $guruPegawaiIds[$guruIndex % $totalGurus];
                    $guruIndex++;

                    JadwalPelajaran::updateOrCreate(
                        [
                            'rombel_id' => $rombel->id,
                            'hari' => $hari,
                            'jam_mulai' => $jamMulai,
                            'jam_selesai' => $jamSelesai,
                        ],
                        [
                            'mata_pelajaran_id' => $mapelId,
                            'pegawai_id' => $assignedPegawaiId,
                        ]
                    );
                    $count++;
                }
            }
        }

        $this->command->info("Berhasil mengimpor {$count} sesi jadwal Murojaah (03:00-04:00, 05:00-06:00, 18:30-19:30 WIB) ke database tanpa duplikasi data guru.");
    }
}
