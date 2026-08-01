<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Orang;
use App\Models\Pegawai;
use App\Models\JadwalPelajaran;
use App\Models\User;

class FixGuruJadwalRelationSeeder extends Seeder
{
    public function run()
    {
        // Daftar nama guru yang dipakai oleh seeder-seeder sebelumnya.
        // Key = nama yang mungkin dibuat duplikat oleh seeder
        // Value = array variasi nama lengkap yang mungkin ada di database master
        $guruMapping = [
            'Sayyidah Aulia U'      => ['Sayyidah Aulia Ul Haqqu', 'Sayyidah Aulia'],
            'La Eni S.sy'           => ['La Eni'],
            'Imam Malik Al-Bukhori' => ['Imam Malik Al-Bukhori', 'Imam Malik Al Bukhori', 'Imam Malik', 'Imam', 'Umam', 'Ach Khairul Umam', 'Khairul Umam', 'Ach. Khairul Umam'],
            'M. Daru Adi Nugroho'   => ['M. Daru Adi Nugroho', 'Daru Adi Nugroho', 'Daru'],
            'Sri Ruliawanti S.pd'   => ['Sri Ruliawanti'],
            'Hestiara Ramli S.pd'   => ['Hestiara Ramli'],
            'Nur Aisyah Harli S.pd' => ['Nur Aisyah Harli', 'Nur Aisyah'],
            'Ariyanni S.pd'         => ['Ariyanni'],
            'Khairil Makin Huda'    => ['Khairil Makin Huda', 'Khairil Makin', 'Ach Khairul Umam', 'Khairul Umam', 'Ach. Khairul Umam'],
            'Sri Mahrani S.pd'      => ['Sri Mahrani'],
            'Ilham Maulana'         => ['Ilham Maulana'],
            'Afiez Muhajir'         => ['Afiez Muhajir', 'Afiz Muhajir'],
        ];

        $totalFixed = 0;

        foreach ($guruMapping as $seederName => $possibleRealNames) {
            $this->command->info("\n--- Memeriksa: {$seederName} ---");

            // 1. Cari semua record Orang yang cocok dengan nama ini
            $allMatches = collect();

            // Cari dengan nama persis dari seeder
            $exact = Orang::where('nama_lengkap', $seederName)->get();
            $allMatches = $allMatches->merge($exact);

            // Cari dengan variasi alias
            foreach ($possibleRealNames as $alias) {
                $found = Orang::where('nama_lengkap', 'like', "%{$alias}%")->get();
                $allMatches = $allMatches->merge($found);
            }

            // Deduplicate
            $allMatches = $allMatches->unique('id');

            if ($allMatches->count() <= 1) {
                $this->command->line("  Hanya 1 record ditemukan, skip.");
                continue;
            }

            $this->command->warn("  Ditemukan {$allMatches->count()} record Orang yang cocok:");

            // 2. Tentukan mana yang ASLI (yang punya User account)
            $realOrang = null;
            $duplicateOrangs = collect();

            foreach ($allMatches as $orang) {
                $hasUser = User::where('orang_id', $orang->id)->exists();
                $this->command->line("    ID: {$orang->id} | Nama: {$orang->nama_lengkap} | User: " . ($hasUser ? 'YA ✅' : 'TIDAK'));

                if ($hasUser && !$realOrang) {
                    $realOrang = $orang;
                } else {
                    $duplicateOrangs->push($orang);
                }
            }

            // Jika tidak ada yang punya User, ambil yang ID paling kecil sebagai "asli"
            if (!$realOrang) {
                $realOrang = $allMatches->sortBy('id')->first();
                $duplicateOrangs = $allMatches->where('id', '!=', $realOrang->id);
                $this->command->warn("  Tidak ada yang punya User, memilih ID terkecil ({$realOrang->id}) sebagai master.");
            }

            $this->command->info("  Master dipilih: ID {$realOrang->id} ({$realOrang->nama_lengkap})");

            // 3. Pastikan master punya record Pegawai
            $realPegawai = Pegawai::firstOrCreate(
                ['orang_id' => $realOrang->id],
                ['jenis_pegawai' => 'GURU', 'is_active' => true]
            );

            $this->command->info("  Master Pegawai ID: {$realPegawai->id}");

            // 4. Pindahkan semua jadwal dari duplikat ke master
            foreach ($duplicateOrangs as $dupOrang) {
                $dupPegawai = Pegawai::where('orang_id', $dupOrang->id)->first();
                if ($dupPegawai) {
                    $jadwalCount = JadwalPelajaran::where('pegawai_id', $dupPegawai->id)->count();
                    if ($jadwalCount > 0) {
                        JadwalPelajaran::where('pegawai_id', $dupPegawai->id)
                            ->update(['pegawai_id' => $realPegawai->id]);
                        $this->command->info("  ✅ Dipindahkan {$jadwalCount} jadwal dari Pegawai #{$dupPegawai->id} (duplikat) ke Pegawai #{$realPegawai->id} (master)");
                        $totalFixed += $jadwalCount;
                    }

                    // Hapus pegawai duplikat
                    $dupPegawai->delete();
                    $this->command->line("  🗑️ Pegawai duplikat #{$dupPegawai->id} dihapus.");
                }

                // Hapus orang duplikat (soft delete jika tersedia)
                if (method_exists($dupOrang, 'trashed')) {
                    $dupOrang->delete(); // soft delete
                    $this->command->line("  🗑️ Orang duplikat #{$dupOrang->id} ({$dupOrang->nama_lengkap}) di-soft-delete.");
                }
            }
        }

        $this->command->info("\n========================================");
        $this->command->info("✅ Total jadwal yang diperbaiki: {$totalFixed}");
        $this->command->info("========================================\n");
    }
}
