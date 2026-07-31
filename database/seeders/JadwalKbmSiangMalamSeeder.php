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

class JadwalKbmSiangMalamSeeder extends Seeder
{
    public function run()
    {
        // 1. Dapatkan atau buat Tahun Pelajaran Aktif
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

        // 2. Hubungkan Guru Pengampu KBM Diniyah Siang-Malam (Tanpa Duplikasi)
        $gurusData = [
            'Ilham Maulana',
            'La Eni S.sy',
            'Afiez Muhajir',
            'Imam Malik Al-Bukhori',
            'M. Daru Adi Nugroho',
            'Khairil Makin Huda',
            'Sayyidah Aulia U',
        ];

        $aliases = [
            'Sayyidah Aulia U' => ['Sayyidah Aulia Ul Haqqu', 'Sayyidah Aulia'],
            'La Eni S.sy' => ['La Eni', 'Laeni'],
            'Imam Malik Al-Bukhori' => ['Imam Malik'],
            'M. Daru Adi Nugroho' => ['M. Daru', 'Daru'],
            'Khairil Makin Huda' => ['Khairil Makin'],
            'Ilham Maulana' => ['Ilham Maulana'],
            'Afiez Muhajir' => ['Afiz Muhajir'],
        ];

        $guruPegawaiIds = [];
        foreach ($gurusData as $namaGuru) {
            $cleanName = trim(preg_replace('/,?\s*(S\.pd|S\.sy|M\.pd|S\.ag|S\.tp)\.?/i', '', $namaGuru));
            $searchAliases = $aliases[$namaGuru] ?? [$cleanName];

            $orang = Orang::where(function($query) use ($namaGuru, $cleanName, $searchAliases) {
                $query->whereIn('nama_lengkap', array_merge([$namaGuru, $cleanName], $searchAliases));
                foreach ($searchAliases as $alias) {
                    $query->orWhere('nama_lengkap', 'like', "%{$alias}%");
                }
            })->first();

            if (!$orang) {
                $orang = Orang::create([
                    'nama_lengkap' => $namaGuru,
                    'niup' => 'GR-' . rand(10000, 99999),
                    'is_active' => true,
                ]);
            }

            $pegawai = Pegawai::firstOrCreate(
                ['orang_id' => $orang->id],
                ['jenis_pegawai' => 'GURU', 'is_active' => true]
            );

            $guruPegawaiIds[] = $pegawai->id;
        }

        // 3. Pastikan Mata Pelajaran 'KBM Diniyah Siang' dan 'KBM Diniyah Malam' Ada
        $mapelSiangMap = [];
        $mapelMalamMap = [];
        foreach ($lembagas as $l) {
            $mapelSiang = MataPelajaran::firstOrCreate(
                ['nama' => 'KBM Diniyah Siang', 'lembaga_id' => $l->id],
                ['is_active' => true]
            );
            $mapelSiangMap[$l->id] = $mapelSiang->id;

            $mapelMalam = MataPelajaran::firstOrCreate(
                ['nama' => 'KBM Diniyah Malam', 'lembaga_id' => $l->id],
                ['is_active' => true]
            );
            $mapelMalamMap[$l->id] = $mapelMalam->id;
        }

        // 4. Ambil Seluruh Rombel
        $rombels = Rombel::where('tahun_pelajaran_id', $tahunAktif->id)->get();
        if ($rombels->isEmpty()) {
            $rombels = Rombel::all();
        }

        $hariList = ['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'AHAD'];
        $count = 0;
        $guruIndex = 0;
        $totalGurus = count($guruPegawaiIds);

        // 5. Tambahkan Sesi KBM Siang (13:00 - 15:00 WIB) & KBM Malam (20:00 - 23:00 WIB)
        foreach ($rombels as $rombel) {
            $mapelSiangId = $mapelSiangMap[$rombel->lembaga_id] ?? reset($mapelSiangMap);
            $mapelMalamId = $mapelMalamMap[$rombel->lembaga_id] ?? reset($mapelMalamMap);

            foreach ($hariList as $hari) {
                // Sesi Siang (13:00 - 15:00 WIB)
                $assignedGuruSiang = $guruPegawaiIds[$guruIndex % $totalGurus];
                $guruIndex++;

                JadwalPelajaran::updateOrCreate(
                    [
                        'rombel_id' => $rombel->id,
                        'hari' => $hari,
                        'jam_mulai' => '13:00',
                        'jam_selesai' => '15:00',
                    ],
                    [
                        'mata_pelajaran_id' => $mapelSiangId,
                        'pegawai_id' => $assignedGuruSiang,
                    ]
                );
                $count++;

                // Sesi Malam (20:00 - 23:00 WIB)
                $assignedGuruMalam = $guruPegawaiIds[$guruIndex % $totalGurus];
                $guruIndex++;

                JadwalPelajaran::updateOrCreate(
                    [
                        'rombel_id' => $rombel->id,
                        'hari' => $hari,
                        'jam_mulai' => '20:00',
                        'jam_selesai' => '23:00',
                    ],
                    [
                        'mata_pelajaran_id' => $mapelMalamId,
                        'pegawai_id' => $assignedGuruMalam,
                    ]
                );
                $count++;
            }
        }

        $this->command->info("Berhasil mengimpor {$count} sesi KBM Siang (13:00-15:00) & KBM Malam (20:00-23:00) ke database tanpa duplikasi data guru.");
    }
}
