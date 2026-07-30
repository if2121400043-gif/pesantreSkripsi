<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $desaId = DB::table('desa')->value('id');
        $pesantrenId = DB::table('pesantren')->value('id');
        $tahunPelajaranId = DB::table('tahun_pelajaran')->where('is_active', true)->value('id') 
            ?? DB::table('tahun_pelajaran')->value('id');

        // Check if MTs, MA, Madin exist or create them
        $mtsId = DB::table('lembaga')->where('singkatan', 'MTs')->value('id');
        $madinId = DB::table('lembaga')->where('singkatan', 'MADIN')->value('id');

        // Check / Create Default Rombel
        $rombelMtsId = null;
        if ($mtsId && $tahunPelajaranId) {
            $rombelMtsId = DB::table('rombel')->where('lembaga_id', $mtsId)->value('id');
            if (!$rombelMtsId) {
                $rombelMtsId = DB::table('rombel')->insertGetId([
                    'lembaga_id' => $mtsId,
                    'tahun_pelajaran_id' => $tahunPelajaranId,
                    'nama' => 'Kelas VIII MTs',
                    'tingkat' => '8',
                    'kapasitas' => 50,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $rombelIdadiyahId = null;
        if ($madinId && $tahunPelajaranId) {
            $rombelIdadiyahId = DB::table('rombel')->where('nama', 'like', '%I\'dadiyah%')->value('id');
            if (!$rombelIdadiyahId) {
                $rombelIdadiyahId = DB::table('rombel')->insertGetId([
                    'lembaga_id' => $madinId,
                    'tahun_pelajaran_id' => $tahunPelajaranId,
                    'nama' => 'Kelas I\'dadiyah Madin',
                    'tingkat' => '1',
                    'kapasitas' => 50,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // List of real santri extracted from Pesantren Excel data
        $santriList = [
            ['nama' => 'Ach. Aliful Adzim', 'jk' => 'L'],
            ['nama' => 'Afri Wardana', 'jk' => 'L'],
            ['nama' => 'Aldiansyah S', 'jk' => 'L'],
            ['nama' => 'Alif AR', 'jk' => 'L'],
            ['nama' => 'Alim Nur Qodim', 'jk' => 'L'],
            ['nama' => 'Alvazhar Triyadi', 'jk' => 'L'],
            ['nama' => 'Anggita Gading', 'jk' => 'P'],
            ['nama' => 'Muhammad Arya Syaifullah', 'jk' => 'L'],
            ['nama' => 'Ashabul Kahfi', 'jk' => 'L'],
            ['nama' => 'Awan', 'jk' => 'L'],
            ['nama' => 'Ayla Rahmah', 'jk' => 'P'],
            ['nama' => 'Azwar Rizqi Alshidi', 'jk' => 'L'],
            ['nama' => 'Bening Khoirunnisa\'', 'jk' => 'P'],
            ['nama' => 'Diellzy Arumi Kaymuddin', 'jk' => 'P'],
            ['nama' => 'Edwar Syafa\'at', 'jk' => 'L'],
            ['nama' => 'Eka Aprilia Kaimuddin', 'jk' => 'P'],
            ['nama' => 'Farzan Atharis', 'jk' => 'L'],
            ['nama' => 'Hafidhia Hiraswati', 'jk' => 'P'],
            ['nama' => 'Haikal Brayen', 'jk' => 'L'],
            ['nama' => 'Ifat Anwari', 'jk' => 'L'],
            ['nama' => 'Keysha Syakira Rahmah', 'jk' => 'P'],
            ['nama' => 'Latifah Faisal', 'jk' => 'P'],
            ['nama' => 'Maulana Samsit Zain Putra', 'jk' => 'L'],
            ['nama' => 'Mawar Anjani Rizki', 'jk' => 'P'],
            ['nama' => 'Meysya Khumairah', 'jk' => 'P'],
            ['nama' => 'Miza Qoulan Karima', 'jk' => 'P'],
            ['nama' => 'Moh. Adrian', 'jk' => 'L'],
            ['nama' => 'Moh. Atho\'illah Robbani I', 'jk' => 'L'],
            ['nama' => 'Muh. Afi', 'jk' => 'L'],
            ['nama' => 'Muh. Afnan Syamil', 'jk' => 'L'],
            ['nama' => 'Muhammad Al-Fikra Pratama', 'jk' => 'L'],
            ['nama' => 'Muhammad al-Fatih', 'jk' => 'L'],
            ['nama' => 'Muhammad Faizin', 'jk' => 'L'],
            ['nama' => 'Muh. Fahri H', 'jk' => 'L'],
            ['nama' => 'Muh. Raihan Akbar', 'jk' => 'L'],
            ['nama' => 'Muh. Rofi', 'jk' => 'L'],
            ['nama' => 'Nabila Khumairah G', 'jk' => 'P'],
            ['nama' => 'Naylah Triana Hafidza', 'jk' => 'P'],
            ['nama' => 'Naziha Razaq', 'jk' => 'P'],
            ['nama' => 'Nazmi Ainun Sabri', 'jk' => 'P'],
            ['nama' => 'Nazmi Khumairah', 'jk' => 'P'],
            ['nama' => 'Nazril Sutono', 'jk' => 'L'],
            ['nama' => 'Nazwa Azqia Ferdiang', 'jk' => 'P'],
            ['nama' => 'Nur Andini', 'jk' => 'P'],
            ['nama' => 'Nur Fatma Mizra', 'jk' => 'P'],
            ['nama' => 'Nur Fatma Wati', 'jk' => 'P'],
            ['nama' => 'Nur Furqon', 'jk' => 'L'],
            ['nama' => 'Puja Syamsuddin', 'jk' => 'L'],
            ['nama' => 'Rajwa Zaidatul Ar-Raq', 'jk' => 'P'],
            ['nama' => 'Reza Asmawan', 'jk' => 'L'],
            ['nama' => 'Selvi Aulia', 'jk' => 'P'],
            ['nama' => 'Selvi Nadila', 'jk' => 'P'],
            ['nama' => 'Siti Marwah', 'jk' => 'P'],
            ['nama' => 'Sri Yulan', 'jk' => 'P'],
            ['nama' => 'Suhaila Afritun .B', 'jk' => 'P'],
            ['nama' => 'Syafira Az-Zahra', 'jk' => 'P'],
            ['nama' => 'Syakila Aulia Putri Susianto', 'jk' => 'P'],
        ];

        $counter = 100;
        $now = now();

        foreach ($santriList as $s) {
            $niup = sprintf('NIUP-2026-%06d', $counter);
            $nis = sprintf('2026%04d', $counter);
            $nisn = sprintf('008%07d', $counter);
            $counter++;

            // Insert to orang
            $orangId = DB::table('orang')->insertGetId([
                'niup' => $niup,
                'nama_lengkap' => $s['nama'],
                'jenis_kelamin' => $s['jk'],
                'kewarganegaraan' => 'Indonesia',
                'desa_id' => $desaId,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Insert to peserta_didik
            $pesertaDidikId = DB::table('peserta_didik')->insertGetId([
                'orang_id' => $orangId,
                'nis' => $nis,
                'nisn' => $nisn,
                'tanggal_masuk' => '2026-07-15',
                'status' => 'AKTIF',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Link to peserta_lembaga_tahun (MTs)
            if ($mtsId && $tahunPelajaranId) {
                DB::table('peserta_lembaga_tahun')->insert([
                    'peserta_didik_id' => $pesertaDidikId,
                    'lembaga_id' => $mtsId,
                    'tahun_pelajaran_id' => $tahunPelajaranId,
                    'status' => 'AKTIF',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // Link to riwayat_rombel_peserta
            $targetRombel = $rombelMtsId ?? $rombelIdadiyahId;
            if ($targetRombel && $tahunPelajaranId) {
                DB::table('riwayat_rombel_peserta')->insert([
                    'peserta_didik_id' => $pesertaDidikId,
                    'rombel_id' => $targetRombel,
                    'tahun_pelajaran_id' => $tahunPelajaranId,
                    'tanggal_masuk' => '2026-07-15',
                    'status' => 'AKTIF',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Clean up
        $niups = [];
        for ($i = 1; $i <= 60; $i++) {
            $niups[] = sprintf('NIUP-2026-%06d', $i);
        }
        
        $orangIds = DB::table('orang')->whereIn('niup', $niups)->pluck('id');
        $pesertaIds = DB::table('peserta_didik')->whereIn('orang_id', $orangIds)->pluck('id');

        DB::table('riwayat_rombel_peserta')->whereIn('peserta_didik_id', $pesertaIds)->delete();
        DB::table('peserta_lembaga_tahun')->whereIn('peserta_didik_id', $pesertaIds)->delete();
        DB::table('peserta_didik')->whereIn('id', $pesertaIds)->delete();
        DB::table('orang')->whereIn('id', $orangIds)->delete();
    }
};
