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

class JadwalSekolahPagiSeeder extends Seeder
{
    public function run()
    {
        // 1. Dapatkan atau buat Tahun Pelajaran Aktif
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        if (!$tahunAktif) {
            $tahunAktif = TahunPelajaran::create([
                'nama' => '2026/2027',
                'semester' => 'GANJIL',
                'tanggal_mulai' => '2026-07-01',
                'tanggal_selesai' => '2027-06-30',
                'is_active' => true,
            ]);
        }

        // 2. Dapatkan Lembaga
        $lembaga = Lembaga::first();
        if (!$lembaga) {
            $lembaga = Lembaga::create([
                'nama' => 'MTs Nurul Furqon',
                'singkatan' => 'MTs',
                'jenjang' => 'MTS',
                'is_active' => true,
            ]);
        }

        // 3. Buat Guru / Ustadz Pengampu
        $gurusData = [
            'Ilham Maulana' => 'Tuntunan Shalat',
            'La Eni S.sy' => 'Tajwid',
            'Afiez Muhajir' => 'A\'malul Yaum',
            'Imam Malik Al-Bukhori' => 'Bahasa Arab',
            'M. Daru Adi Nugroho' => 'Tahsinul Qur\'an',
            'Sri Ruliawanti S.pd' => 'Matematika',
            'Hestiara Ramli S.pd' => 'IPA Terpadu',
            'Nur Aisyah Harli S.pd' => 'IPS Terpadu',
            'Ariyanni S.pd' => 'Bahasa Inggris',
            'Khairil Makin Huda' => 'Tauhid',
            'Sri Mahrani S.pd' => 'IPA Terpadu',
            'Sayyidah Aulia U' => 'Bahasa Inggris',
        ];

        // Alias map untuk menjamin semua variasi nama guru dari Excel langsung terhubung ke data master
        $aliases = [
            'Sayyidah Aulia U' => ['Sayyidah Aulia Ul Haqqu', 'Sayyidah Aulia', 'Sayyidah'],
            'La Eni S.sy' => ['La Eni', 'Laeni', 'La Eni S.Sy'],
            'Imam Malik Al-Bukhori' => ['Imam Malik', 'Imam Malik Al Bukhori', 'Imam Malik Al-Bukhari', 'Imam', 'Umam', 'Ach Khairul Umam', 'Khairul Umam', 'Ach. Khairul Umam'],
            'M. Daru Adi Nugroho' => ['M. Daru Adi Nugroho', 'Daru Adi Nugroho', 'M. Daru', 'Daru'],
            'Sri Ruliawanti S.pd' => ['Sri Ruliawanti', 'Sri Ruliawanti S.Pd'],
            'Hestiara Ramli S.pd' => ['Hestiara Ramli', 'Hestiara Ramli S.Pd'],
            'Nur Aisyah Harli S.pd' => ['Nur Aisyah Harli', 'Nur Aisyah Harli S.Pd', 'Nur Aisyah'],
            'Ariyanni S.pd' => ['Ariyanni', 'Ariyanni S.Pd'],
            'Khairil Makin Huda' => ['Khairil Makin Huda', 'Khairil Makin', 'Khairil', 'Ach Khairul Umam', 'Khairul Umam', 'Ach. Khairul Umam'],
            'Sri Mahrani S.pd' => ['Sri Mahrani', 'Sri Mahrani S.Pd'],
            'Ilham Maulana' => ['Ilham Maulana'],
            'Afiez Muhajir' => ['Afiez Muhajir', 'Afiz Muhajir'],
        ];

        $guruMap = [];
        foreach ($gurusData as $namaGuru => $specialty) {
            $cleanName = trim(preg_replace('/,?\s*(S\.pd|S\.sy|M\.pd|S\.ag|S\.tp|S\.h|M\.ag|Lc)\.?/i', '', $namaGuru));
            $parts = array_values(array_filter(explode(' ', $cleanName)));
            $firstName = $parts[0] ?? $cleanName;
            $secondName = $parts[1] ?? '';

            $searchAliases = $aliases[$namaGuru] ?? [$cleanName];

            // Tier 1: Pencarian berdasarkan Nama Lengkap / Alias / Gelar
            $orang = Orang::where(function($query) use ($namaGuru, $cleanName, $searchAliases) {
                $query->whereIn('nama_lengkap', array_merge([$namaGuru, $cleanName], $searchAliases));
                foreach ($searchAliases as $alias) {
                    $query->orWhere('nama_lengkap', 'like', "%{$alias}%");
                }
            })->first();

            // Tier 2: Fallback 2 Kata Depan
            if (!$orang && $secondName) {
                $orang = Orang::where('nama_lengkap', 'like', "{$firstName} {$secondName}%")->first();
            }

            // Tier 3: Fallback 1 Kata Depan
            if (!$orang && strlen($firstName) > 2) {
                $orang = Orang::where('nama_lengkap', 'like', "{$firstName}%")->first();
            }

            // Tier 4: Buat Data Orang Baru jika sama sekali belum ada
            if (!$orang) {
                $orang = Orang::create([
                    'nama_lengkap' => $namaGuru,
                    'niup' => 'GR-' . rand(10000, 99999),
                    'is_active' => true,
                ]);
            }

            $pegawai = Pegawai::firstOrCreate(
                ['orang_id' => $orang->id],
                [
                    'jenis_pegawai' => 'GURU',
                    'is_active' => true,
                ]
            );

            $guruMap[$namaGuru] = $pegawai->id;
        }

        // 4. Buat Mata Pelajaran
        $mapelsData = [
            'Tuntunan Shalat',
            'Tajwid',
            'A\'malul Yaum',
            'Imla\'',
            'Tahsinul Qur\'an',
            'Matematika',
            'IPA Terpadu',
            'IPS Terpadu',
            'Bahasa Arab',
            'Bahasa Inggris',
            'Tauhid',
            'Ilmu Al-Qur\'an',
            'Tahfidz',
            'Upacara / Apel',
        ];

        $mapelMap = [];
        foreach ($mapelsData as $namaMapel) {
            $mapel = MataPelajaran::firstOrCreate(
                ['nama' => $namaMapel, 'lembaga_id' => $lembaga->id],
                ['is_active' => true]
            );
            $mapelMap[$namaMapel] = $mapel->id;
        }

        // 5. Buat Rombel Kelas VII, VIII, IX, X
        $kelasList = [
            'VII' => 'KELAS VII',
            'VIII' => 'KELAS VIII',
            'IX' => 'KELAS IX',
            'X' => 'KELAS X',
        ];

        $rombelMap = [];
        foreach ($kelasList as $tingkat => $namaRombel) {
            $rombel = Rombel::firstOrCreate(
                ['nama' => $namaRombel, 'tahun_pelajaran_id' => $tahunAktif->id],
                [
                    'lembaga_id' => $lembaga->id,
                    'tingkat' => $tingkat,
                ]
            );
            $rombelMap[$namaRombel] = $rombel->id;
        }

        // 6. Matriks Jadwal Pelajaran Sekolah Pagi (07.15 - 11.45)
        $jadwalMatrix = [
            'KELAS VII' => [
                'SENIN' => [
                    ['07:15', '07:30', 'Upacara / Apel', null],
                    ['07:30', '08:15', 'Upacara / Apel', null],
                    ['08:15', '09:00', 'Tuntunan Shalat', 'Ilham Maulana'],
                    ['09:15', '10:00', 'Tuntunan Shalat', 'Ilham Maulana'],
                    ['10:00', '10:45', 'Imla\'', 'Imam Malik Al-Bukhori'],
                    ['10:45', '11:45', 'Imla\'', 'Imam Malik Al-Bukhori'],
                ],
                'SELASA' => [
                    ['07:15', '07:30', 'Upacara / Apel', null],
                    ['07:30', '08:15', 'Tuntunan Shalat', 'Ilham Maulana'],
                    ['08:15', '09:00', 'Tuntunan Shalat', 'Ilham Maulana'],
                    ['09:15', '10:00', 'Tajwid', 'La Eni S.sy'],
                    ['10:00', '10:45', 'Tajwid', 'La Eni S.sy'],
                    ['10:45', '11:45', 'Tajwid', 'La Eni S.sy'],
                ],
                'RABU' => [
                    ['07:15', '07:30', 'Upacara / Apel', null],
                    ['07:30', '08:15', 'Tahsinul Qur\'an', 'M. Daru Adi Nugroho'],
                    ['08:15', '09:00', 'Tahsinul Qur\'an', 'M. Daru Adi Nugroho'],
                    ['09:15', '10:00', 'Tajwid', 'La Eni S.sy'],
                    ['10:00', '10:45', 'Tajwid', 'La Eni S.sy'],
                    ['10:45', '11:45', 'Tajwid', 'La Eni S.sy'],
                ],
                'KAMIS' => [
                    ['07:15', '07:30', 'Upacara / Apel', null],
                    ['07:30', '08:15', 'Tuntunan Shalat', 'Ilham Maulana'],
                    ['08:15', '09:00', 'Tuntunan Shalat', 'Ilham Maulana'],
                    ['09:15', '10:00', 'Imla\'', 'Imam Malik Al-Bukhori'],
                    ['10:00', '10:45', 'Imla\'', 'Imam Malik Al-Bukhori'],
                    ['10:45', '11:45', 'Imla\'', 'Imam Malik Al-Bukhori'],
                ],
                'SABTU' => [
                    ['07:15', '07:30', 'Upacara / Apel', null],
                    ['07:30', '08:15', 'Imla\'', 'Imam Malik Al-Bukhori'],
                    ['08:15', '09:00', 'Imla\'', 'Imam Malik Al-Bukhori'],
                    ['09:15', '10:00', 'A\'malul Yaum', 'Afiez Muhajir'],
                    ['10:00', '10:45', 'A\'malul Yaum', 'Afiez Muhajir'],
                    ['10:45', '11:45', 'A\'malul Yaum', 'Afiez Muhajir'],
                ],
                'AHAD' => [
                    ['07:15', '07:30', 'Upacara / Apel', null],
                    ['07:30', '08:15', 'Tahsinul Qur\'an', 'M. Daru Adi Nugroho'],
                    ['08:15', '09:00', 'Tahsinul Qur\'an', 'M. Daru Adi Nugroho'],
                    ['09:15', '10:00', 'A\'malul Yaum', 'Afiez Muhajir'],
                    ['10:00', '10:45', 'A\'malul Yaum', 'Afiez Muhajir'],
                    ['10:45', '11:45', 'A\'malul Yaum', 'Afiez Muhajir'],
                ],
            ],

            'KELAS VIII' => [
                'SENIN' => [
                    ['07:15', '07:30', 'Upacara / Apel', null],
                    ['07:30', '08:15', 'Upacara / Apel', null],
                    ['08:15', '09:00', 'Matematika', 'Sri Ruliawanti S.pd'],
                    ['09:15', '10:00', 'Matematika', 'Sri Ruliawanti S.pd'],
                    ['10:00', '10:45', 'Bahasa Inggris', 'Ariyanni S.pd'],
                    ['10:45', '11:45', 'Bahasa Inggris', 'Ariyanni S.pd'],
                ],
                'SELASA' => [
                    ['07:15', '07:30', 'Upacara / Apel', null],
                    ['07:30', '08:15', 'Bahasa Arab', 'Imam Malik Al-Bukhori'],
                    ['08:15', '09:00', 'Bahasa Arab', 'Imam Malik Al-Bukhori'],
                    ['09:15', '10:00', 'Bahasa Inggris', 'Ariyanni S.pd'],
                    ['10:00', '10:45', 'Bahasa Inggris', 'Ariyanni S.pd'],
                    ['10:45', '11:45', 'Bahasa Inggris', 'Ariyanni S.pd'],
                ],
                'RABU' => [
                    ['07:15', '07:30', 'Upacara / Apel', null],
                    ['07:30', '08:15', 'Bahasa Arab', 'Imam Malik Al-Bukhori'],
                    ['08:15', '09:00', 'Bahasa Arab', 'Imam Malik Al-Bukhori'],
                    ['09:15', '10:00', 'IPS Terpadu', 'Nur Aisyah Harli S.pd'],
                    ['10:00', '10:45', 'IPS Terpadu', 'Nur Aisyah Harli S.pd'],
                    ['10:45', '11:45', 'IPS Terpadu', 'Nur Aisyah Harli S.pd'],
                ],
                'KAMIS' => [
                    ['07:15', '07:30', 'Upacara / Apel', null],
                    ['07:30', '08:15', 'Matematika', 'Sri Ruliawanti S.pd'],
                    ['08:15', '09:00', 'Matematika', 'Sri Ruliawanti S.pd'],
                    ['09:15', '10:00', 'IPS Terpadu', 'Nur Aisyah Harli S.pd'],
                    ['10:00', '10:45', 'IPS Terpadu', 'Nur Aisyah Harli S.pd'],
                    ['10:45', '11:45', 'IPS Terpadu', 'Nur Aisyah Harli S.pd'],
                ],
                'SABTU' => [
                    ['07:15', '07:30', 'Upacara / Apel', null],
                    ['07:30', '08:15', 'IPA Terpadu', 'Hestiara Ramli S.pd'],
                    ['08:15', '09:00', 'IPA Terpadu', 'Hestiara Ramli S.pd'],
                    ['09:15', '10:00', 'Tauhid', 'Khairil Makin Huda'],
                    ['10:00', '10:45', 'Tauhid', 'Khairil Makin Huda'],
                    ['10:45', '11:45', 'Tauhid', 'Khairil Makin Huda'],
                ],
                'AHAD' => [
                    ['07:15', '07:30', 'Upacara / Apel', null],
                    ['07:30', '08:15', 'IPA Terpadu', 'Hestiara Ramli S.pd'],
                    ['08:15', '09:00', 'IPA Terpadu', 'Hestiara Ramli S.pd'],
                    ['09:15', '10:00', 'Tauhid', 'Khairil Makin Huda'],
                    ['10:00', '10:45', 'Tauhid', 'Khairil Makin Huda'],
                    ['10:45', '11:45', 'Tauhid', 'Khairil Makin Huda'],
                ],
            ],

            'KELAS IX' => [
                'SENIN' => [
                    ['07:15', '07:30', 'Upacara / Apel', null],
                    ['07:30', '08:15', 'Upacara / Apel', null],
                    ['08:15', '09:00', 'Bahasa Inggris', 'Ariyanni S.pd'],
                    ['09:15', '10:00', 'Bahasa Inggris', 'Ariyanni S.pd'],
                    ['10:00', '10:45', 'Matematika', 'Sri Ruliawanti S.pd'],
                    ['10:45', '11:45', 'Matematika', 'Sri Ruliawanti S.pd'],
                ],
                'SELASA' => [
                    ['07:15', '07:30', 'Upacara / Apel', null],
                    ['07:30', '08:15', 'IPA Terpadu', 'Sri Mahrani S.pd'],
                    ['08:15', '09:00', 'IPA Terpadu', 'Sri Mahrani S.pd'],
                    ['09:15', '10:00', 'Tauhid', 'Khairil Makin Huda'],
                    ['10:00', '10:45', 'Tauhid', 'Khairil Makin Huda'],
                    ['10:45', '11:45', 'Tauhid', 'Khairil Makin Huda'],
                ],
                'RABU' => [
                    ['07:15', '07:30', 'Upacara / Apel', null],
                    ['07:30', '08:15', 'Tauhid', 'Khairil Makin Huda'],
                    ['08:15', '09:00', 'Tauhid', 'Khairil Makin Huda'],
                    ['09:15', '10:00', 'IPA Terpadu', 'Sri Mahrani S.pd'],
                    ['10:00', '10:45', 'IPA Terpadu', 'Sri Mahrani S.pd'],
                    ['10:45', '11:45', 'IPA Terpadu', 'Sri Mahrani S.pd'],
                ],
                'KAMIS' => [
                    ['07:15', '07:30', 'Upacara / Apel', null],
                    ['07:30', '08:15', 'Bahasa Inggris', 'Ariyanni S.pd'],
                    ['08:15', '09:00', 'Bahasa Inggris', 'Ariyanni S.pd'],
                    ['09:15', '10:00', 'Matematika', 'Sri Ruliawanti S.pd'],
                    ['10:00', '10:45', 'Matematika', 'Sri Ruliawanti S.pd'],
                    ['10:45', '11:45', 'Matematika', 'Sri Ruliawanti S.pd'],
                ],
                'SABTU' => [
                    ['07:15', '07:30', 'Upacara / Apel', null],
                    ['07:30', '08:15', 'Bahasa Arab', 'Imam Malik Al-Bukhori'],
                    ['08:15', '09:00', 'Bahasa Arab', 'Imam Malik Al-Bukhori'],
                    ['09:15', '10:00', 'IPS Terpadu', 'Nur Aisyah Harli S.pd'],
                    ['10:00', '10:45', 'IPS Terpadu', 'Nur Aisyah Harli S.pd'],
                    ['10:45', '11:45', 'IPS Terpadu', 'Nur Aisyah Harli S.pd'],
                ],
                'AHAD' => [
                    ['07:15', '07:30', 'Upacara / Apel', null],
                    ['07:30', '08:15', 'Bahasa Arab', 'Imam Malik Al-Bukhori'],
                    ['08:15', '09:00', 'Bahasa Arab', 'Imam Malik Al-Bukhori'],
                    ['09:15', '10:00', 'IPS Terpadu', 'Nur Aisyah Harli S.pd'],
                    ['10:00', '10:45', 'IPS Terpadu', 'Nur Aisyah Harli S.pd'],
                    ['10:45', '11:45', 'IPS Terpadu', 'Nur Aisyah Harli S.pd'],
                ],
            ],

            'KELAS X' => [
                'SENIN' => [
                    ['07:15', '07:30', 'Upacara / Apel', null],
                    ['07:30', '08:15', 'Upacara / Apel', null],
                    ['08:15', '09:00', 'Tahfidz', 'M. Daru Adi Nugroho'],
                    ['09:15', '10:00', 'Tauhid', 'Khairil Makin Huda'],
                    ['10:00', '10:45', 'Tauhid', 'Khairil Makin Huda'],
                    ['10:45', '11:45', 'Tauhid', 'Khairil Makin Huda'],
                ],
                'SELASA' => [
                    ['07:15', '07:30', 'Upacara / Apel', null],
                    ['07:30', '08:15', 'Tahfidz', 'M. Daru Adi Nugroho'],
                    ['08:15', '09:00', 'Tahfidz', 'M. Daru Adi Nugroho'],
                    ['09:15', '10:00', 'Bahasa Inggris', 'Sayyidah Aulia U'],
                    ['10:00', '10:45', 'Bahasa Inggris', 'Sayyidah Aulia U'],
                    ['10:45', '11:45', 'Bahasa Inggris', 'Sayyidah Aulia U'],
                ],
                'RABU' => [
                    ['07:15', '07:30', 'Upacara / Apel', null],
                    ['07:30', '08:15', 'Tauhid', 'Khairil Makin Huda'],
                    ['08:15', '09:00', 'Tauhid', 'Khairil Makin Huda'],
                    ['09:15', '10:00', 'Tahfidz', 'M. Daru Adi Nugroho'],
                    ['10:00', '10:45', 'Tahfidz', 'M. Daru Adi Nugroho'],
                    ['10:45', '11:45', 'Tahfidz', 'M. Daru Adi Nugroho'],
                ],
                'KAMIS' => [
                    ['07:15', '07:30', 'Upacara / Apel', null],
                    ['07:30', '08:15', 'Tahfidz', 'M. Daru Adi Nugroho'],
                    ['08:15', '09:00', 'Tahfidz', 'M. Daru Adi Nugroho'],
                    ['09:15', '10:00', 'Bahasa Inggris', 'Sayyidah Aulia U'],
                    ['10:00', '10:45', 'Bahasa Inggris', 'Sayyidah Aulia U'],
                    ['10:45', '11:45', 'Bahasa Inggris', 'Sayyidah Aulia U'],
                ],
                'SABTU' => [
                    ['07:15', '07:30', 'Upacara / Apel', null],
                    ['07:30', '08:15', 'Tahfidz', 'M. Daru Adi Nugroho'],
                    ['08:15', '09:00', 'Tahfidz', 'M. Daru Adi Nugroho'],
                    ['09:15', '10:00', 'Bahasa Inggris', 'Sayyidah Aulia U'],
                    ['10:00', '10:45', 'Bahasa Inggris', 'Sayyidah Aulia U'],
                    ['10:45', '11:45', 'Bahasa Inggris', 'Sayyidah Aulia U'],
                ],
                'AHAD' => [
                    ['07:15', '07:30', 'Upacara / Apel', null],
                    ['07:30', '08:15', 'Tauhid', 'Khairil Makin Huda'],
                    ['08:15', '09:00', 'Tauhid', 'Khairil Makin Huda'],
                    ['09:15', '10:00', 'Tahfidz', 'M. Daru Adi Nugroho'],
                    ['10:00', '10:45', 'Tahfidz', 'M. Daru Adi Nugroho'],
                    ['10:45', '11:45', 'Tahfidz', 'M. Daru Adi Nugroho'],
                ],
            ],
        ];

        // 7. Simpan Seluruh Sesi ke Database
        $count = 0;
        foreach ($jadwalMatrix as $namaRombel => $hariData) {
            $rombelId = $rombelMap[$namaRombel] ?? null;
            if (!$rombelId) continue;

            foreach ($hariData as $hari => $sesiList) {
                foreach ($sesiList as $s) {
                    $jamMulai = $s[0];
                    $jamSelesai = $s[1];
                    $namaMapel = $s[2];
                    $namaGuru = $s[3];

                    $mapelId = $mapelMap[$namaMapel] ?? null;
                    $guruId = $namaGuru ? ($guruMap[$namaGuru] ?? null) : null;

                    if ($rombelId && $mapelId) {
                        JadwalPelajaran::updateOrCreate(
                            [
                                'rombel_id' => $rombelId,
                                'hari' => $hari,
                                'jam_mulai' => $jamMulai,
                                'jam_selesai' => $jamSelesai,
                            ],
                            [
                                'mata_pelajaran_id' => $mapelId,
                                'pegawai_id' => $guruId,
                            ]
                        );
                        $count++;
                    }
                }
            }
        }

        $this->command->info("Berhasil mengimpor {$count} sesi jadwal sekolah pagi ke database.");
    }
}
