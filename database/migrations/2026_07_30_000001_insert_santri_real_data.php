<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

        // 1. Create Non-Formal Lembaga (Metode Al-Kamal) if not exists
        $alKamalLembagaId = DB::table('lembaga')->where('nama', 'like', '%Al-Kamal%')->value('id');
        if (!$alKamalLembagaId && $pesantrenId) {
            $alKamalLembagaId = DB::table('lembaga')->insertGetId([
                'pesantren_id' => $pesantrenId,
                'nama' => 'Pengajaran Metode Al-Kamal',
                'singkatan' => 'AL-KAMAL',
                'jenjang' => 'NON_FORMAL',
                'tipe' => 'NON_FORMAL',
                'urutan' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Create Rombel (Formal & Non-Formal Classes)
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

        $rombelAlKamalId = null;
        $targetLembagaAlKamal = $alKamalLembagaId ?? $madinId;
        if ($targetLembagaAlKamal && $tahunPelajaranId) {
            $rombelAlKamalId = DB::table('rombel')->where('nama', 'like', '%Al-Kamal%')->value('id');
            if (!$rombelAlKamalId) {
                $rombelAlKamalId = DB::table('rombel')->insertGetId([
                    'lembaga_id' => $targetLembagaAlKamal,
                    'tahun_pelajaran_id' => $tahunPelajaranId,
                    'nama' => 'Jilid 2 Putri (Al-Kamal)',
                    'tingkat' => '2',
                    'kapasitas' => 50,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 3. Insert Real Teachers (Guru / Pengajar)
        $guruRole = DB::table('roles')->where('nama', 'GURU')->value('id');
        $guruData = [
            ['nama' => 'Ach Khairul Umam', 'email' => 'kamluja12@gmail.com', 'hp' => '085233449669', 'username' => 'khairul.umam', 'jk' => 'L'],
            ['nama' => 'Sayyidah Aulia Ul Haqqu', 'email' => 'syydhauliaa@gmail.com', 'hp' => '085843439612', 'username' => 'sayyidah.aulia', 'jk' => 'P'],
            ['nama' => 'Alfiani Nur Sakinah', 'email' => 'sayealfian23@gmail.com', 'hp' => '089527813996', 'username' => 'alfiani.sakinah', 'jk' => 'P'],
            ['nama' => 'Khairil Makin Huda', 'email' => 'khairilmakin44@gmail.com', 'hp' => '087841105470', 'username' => 'khairil.makin', 'jk' => 'L'],
        ];

        $now = now();
        $guruCounter = 1;

        foreach ($guruData as $g) {
            $niup = sprintf('NIUP-GURU-%04d', $guruCounter++);
            
            // Insert to orang
            $orangId = DB::table('orang')->insertGetId([
                'niup' => $niup,
                'nama_lengkap' => $g['nama'],
                'jenis_kelamin' => $g['jk'],
                'email' => strtolower($g['email']),
                'telepon' => $g['hp'],
                'kewarganegaraan' => 'Indonesia',
                'desa_id' => $desaId,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Insert to pegawai
            DB::table('pegawai')->insert([
                'orang_id' => $orangId,
                'jenis_pegawai' => 'GURU',
                'status_kepegawaian' => 'TETAP',
                'tanggal_masuk' => '2026-07-15',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Insert user account for login
            $userId = DB::table('users')->insertGetId([
                'orang_id' => $orangId,
                'username' => $g['username'],
                'email' => strtolower($g['email']),
                'email_verified_at' => $now,
                'password' => Hash::make('password'),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Assign GURU role
            if ($guruRole) {
                DB::table('user_role')->insert([
                    'user_id' => $userId,
                    'role_id' => $guruRole,
                    'is_default' => true,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 4. List of real santri extracted from Pesantren Excel data
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

            // Link to peserta_lembaga_tahun (MTs Formal)
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

            // Link to Rombel 1: MTs Formal Rombel
            if ($rombelMtsId && $tahunPelajaranId) {
                DB::table('riwayat_rombel_peserta')->insert([
                    'peserta_didik_id' => $pesertaDidikId,
                    'rombel_id' => $rombelMtsId,
                    'tahun_pelajaran_id' => $tahunPelajaranId,
                    'tanggal_masuk' => '2026-07-15',
                    'status' => 'AKTIF',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // Link to Rombel 2: MULTIPLE ROMBEL / KELAS NON-FORMAL!
            // E.g., Santri Putri ALSO enrolled in Al-Kamal Rombel
            if ($s['jk'] === 'P' && $rombelAlKamalId && $tahunPelajaranId) {
                DB::table('riwayat_rombel_peserta')->insert([
                    'peserta_didik_id' => $pesertaDidikId,
                    'rombel_id' => $rombelAlKamalId,
                    'tahun_pelajaran_id' => $tahunPelajaranId,
                    'tanggal_masuk' => '2026-07-15',
                    'status' => 'AKTIF',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // E.g., Santri Putra ALSO enrolled in I'dadiyah Madin Rombel
            if ($s['jk'] === 'L' && $rombelIdadiyahId && $tahunPelajaranId) {
                DB::table('riwayat_rombel_peserta')->insert([
                    'peserta_didik_id' => $pesertaDidikId,
                    'rombel_id' => $rombelIdadiyahId,
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
        for ($i = 100; $i <= 160; $i++) {
            $niups[] = sprintf('NIUP-2026-%06d', $i);
        }
        
        $orangIds = DB::table('orang')->whereIn('niup', $niups)->pluck('id');
        $pesertaIds = DB::table('peserta_didik')->whereIn('orang_id', $orangIds)->pluck('id');

        DB::table('riwayat_rombel_peserta')->whereIn('peserta_didik_id', $pesertaIds)->delete();
        DB::table('peserta_lembaga_tahun')->whereIn('peserta_didik_id', $pesertaIds)->delete();
        DB::table('peserta_didik')->whereIn('id', $pesertaIds)->delete();
        
        // Clean up Guru
        $guruNiups = ['NIUP-GURU-0001', 'NIUP-GURU-0002', 'NIUP-GURU-0003', 'NIUP-GURU-0004'];
        $guruOrangIds = DB::table('orang')->whereIn('niup', $guruNiups)->pluck('id');
        $userIds = DB::table('users')->whereIn('orang_id', $guruOrangIds)->pluck('id');

        DB::table('user_role')->whereIn('user_id', $userIds)->delete();
        DB::table('users')->whereIn('id', $userIds)->delete();
        DB::table('pegawai')->whereIn('orang_id', $guruOrangIds)->delete();

        DB::table('orang')->whereIn('id', $orangIds->merge($guruOrangIds))->delete();
    }
};
