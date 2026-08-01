<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Lembaga;
use App\Models\TahunPelajaran;
use App\Models\Rombel;
use App\Models\Orang;
use App\Models\PesertaDidik;
use App\Models\Pegawai;
use App\Models\HubunganKeluarga;
use App\Models\RiwayatRombelPeserta;

class CleanAndSyncSantriSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        // 1. Ambil ID semua Pegawai (Guru/Pengasuh) agar TIDAK terhapus
        $pegawaiOrangIds = Pegawai::pluck('orang_id')->toArray();

        // 2. Daftar 56 Nama Santri Asli
        $realSantriList = [
            'Achmad Mukhizan Amran', 'Selvi Nadila', 'Nazril Sutriyono', 'Edwar Syafaat',
            'Wa Ode Eka Aprilia Kaimuddin', 'Nazmi Khumairah', 'Nazwa Azqia Ferdiang',
            'Wa Ode Meysya Humairoh', 'Anisa', 'Al-Vazhar Tryadi', 'La Ode Muh. Raihan Akbar',
            'Muhammad Afnan Syamil', 'Azwar Rizqi Al-Sidiq', 'Muh. Fahri Herdiansyah',
            'La Ode Muhammad Afiq', 'Puja Samsudin', 'Adiba Kheyla Az-zahra', 'Muh. Rhofi',
            'Agustian', 'Lathifah Faisal', 'Nabila Khumairah Gole', 'Ayla Rahma',
            'Nailah Triana Hafidza', 'Rajwa Zaidatul Ar Raq', 'MUH. FAIZIN', 'NAZIHA RAZAK',
            'NAZMI AINUN SABRI', 'SYAKILLA AULIA PUTRI SUSIANTO', 'BENING KHOIRUNNISA',
            'REZA ASMAWAN. F', 'IFFAT ANWARI', 'AFRIL WARDANA', 'Ach. Aliful Adzim',
            'Anggita Gading', 'Aldiansyah', 'Muh. Athoillah', 'Farzan Atharis',
            'Arya Syaifullah', 'Alif AR', 'Ashabul Kahfi', 'Dielzi Arumika', 'Haikal Brayen',
            'Hazidzia Hiraswati', 'Maulana S.Z.P', 'Muh. Adrian', 'Muh. Alfikra',
            'Muh. AlFatih', 'Nur Fatma Mizra', 'Nur Fatma Wati', 'Sri Yulan',
            'Suhaila Afriyatun', 'Syafira A.Z', 'Alim Nur Qodim', 'Awan', 'Selvi Aulia',
            'Siti Marwa', 'Mawar Anjani', 'Nur Furqon'
        ];

        // 3. Bersihkan tabel peserta_didik & riwayat_rombel_peserta lama
        DB::table('riwayat_rombel_peserta')->truncate();
        DB::table('peserta_didik')->truncate();

        // 4. Hapus data Orang santri lama/dummy yang tidak masuk daftar 56 santri & bukan pegawai
        $orangToDelete = Orang::whereNotIn('id', $pegawaiOrangIds)->get();
        foreach ($orangToDelete as $o) {
            $isReal = false;
            foreach ($realSantriList as $realName) {
                if (stripos($o->nama_lengkap, $realName) !== false || stripos($realName, $o->nama_lengkap) !== false) {
                    $isReal = true;
                    break;
                }
            }
            if (!$isReal) {
                HubunganKeluarga::where('orang_id', $o->id)->orWhere('keluarga_id', $o->id)->delete();
                $o->delete();
            }
        }

        Schema::enableForeignKeyConstraints();

        // 5. Jalankan Ulang Seeder Impor 56 Santri Real
        $this->call(ImportKelasSiswaMtsMaSeeder::class);

        $totalSantri = PesertaDidik::count();
        $this->command->info("BERHASIL! Data Santri telah dibersihkan total dari data dummy/lama. Total Santri Resmi di Sistem Saat Ini: {$totalSantri} Santri.");
    }
}
