<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lembaga;
use App\Models\TahunPelajaran;
use App\Models\Rombel;
use App\Models\Orang;
use App\Models\PesertaDidik;
use App\Models\HubunganKeluarga;
use App\Models\RiwayatRombelPeserta;
use Carbon\Carbon;

class ImportKelasSiswaMtsMaSeeder extends Seeder
{
    public function run()
    {
        // 1. Tahun Pelajaran Aktif
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        if (!$tahunAktif) {
            $tahunAktif = TahunPelajaran::firstOrCreate(
                ['nama' => '2026/2027'],
                ['semester' => 'GANJIL', 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2027-06-30', 'is_active' => true]
            );
        }

        // 2. Lembaga MTs dan MA
        $lembagaMts = Lembaga::firstOrCreate(
            ['singkatan' => 'MTs'],
            ['nama' => 'Madrasah Tsanawiyah Nurul Furqon', 'jenjang' => 'MTS', 'is_active' => true]
        );

        $lembagaMa = Lembaga::firstOrCreate(
            ['singkatan' => 'MA'],
            ['nama' => 'Madrasah Aliyah Nurul Furqon', 'jenjang' => 'MA', 'is_active' => true]
        );

        // 3. Buat Rombel / Kelas MTs & MA
        $rombelMts7 = Rombel::firstOrCreate(
            ['nama' => 'KELAS VII', 'tahun_pelajaran_id' => $tahunAktif->id],
            ['lembaga_id' => $lembagaMts->id, 'tingkat' => 'VII']
        );
        $rombelMts8 = Rombel::firstOrCreate(
            ['nama' => 'KELAS VIII', 'tahun_pelajaran_id' => $tahunAktif->id],
            ['lembaga_id' => $lembagaMts->id, 'tingkat' => 'VIII']
        );
        $rombelMts9 = Rombel::firstOrCreate(
            ['nama' => 'KELAS IX', 'tahun_pelajaran_id' => $tahunAktif->id],
            ['lembaga_id' => $lembagaMts->id, 'tingkat' => 'IX']
        );

        $rombelMa11 = Rombel::firstOrCreate(
            ['nama' => 'KELAS XI', 'tahun_pelajaran_id' => $tahunAktif->id],
            ['lembaga_id' => $lembagaMa->id, 'tingkat' => 'XI']
        );

        // Data Siswa Lengkap MTs (dari DATA SEMENTARA SISWA MTS NURUL FURQON DESA TIMU.xlsx)
        $siswaDataDetailed = [
            // KELAS VII
            ['nama' => 'Achmad Mukhizan Amran', 'nisn' => '0132868735', 'kelas' => 'KELAS VII', 'nik' => '7407012311130000', 'tgl' => '2013-11-23', 'ortu' => 'Amdani Agus Irawan Amran, SH'],
            ['nama' => 'Selvi Nadila', 'nisn' => '3135415105', 'kelas' => 'KELAS VII', 'nik' => '9117056102130001', 'tgl' => '2013-02-21', 'ortu' => 'Sule dan Nofianti'],
            ['nama' => 'Nazril Sutriyono', 'nisn' => '0145798742', 'kelas' => 'KELAS VII', 'nik' => '7407071806140002', 'tgl' => '2014-07-18', 'ortu' => 'Suryono dan Suti Wabula'],
            ['nama' => 'Edwar Syafaat', 'nisn' => '0133799399', 'kelas' => 'KELAS VII', 'nik' => '7407031911130001', 'tgl' => '2013-11-19', 'ortu' => 'Samsuddin Ane dan Nur Asyiani'],
            ['nama' => 'Wa Ode Eka Aprilia Kaimuddin', 'nisn' => '340440278', 'kelas' => 'KELAS VII', 'nik' => '8106016804140001', 'tgl' => '2014-04-28', 'ortu' => 'La Ode Ahmad Yani Kaimuddin'],
            ['nama' => 'Nazmi Khumairah', 'nisn' => '0143521049', 'kelas' => 'KELAS VII', 'nik' => '7407075801140001', 'tgl' => '2014-01-18', 'ortu' => 'Muh. Syaiful, S.Kom'],
            ['nama' => 'Nazwa Azqia Ferdiang', 'nisn' => '3141298168', 'kelas' => 'KELAS VII', 'nik' => '9118016101140002', 'tgl' => '2014-01-21', 'ortu' => 'Ferdiang Agung, ST'],
            ['nama' => 'Wa Ode Meysya Humairoh', 'nisn' => '0142047073', 'kelas' => 'KELAS VII', 'nik' => '7407076306140001', 'tgl' => '2014-05-23', 'ortu' => 'La Ode Jakaria'],
            ['nama' => 'Anisa', 'nisn' => '0147113815', 'kelas' => 'KELAS VII', 'nik' => '7407076601140001', 'tgl' => '2014-01-26', 'ortu' => 'La Ali Ane'],
            ['nama' => 'Al-Vazhar Tryadi', 'nisn' => null, 'kelas' => 'KELAS VII', 'nik' => '7407070911130001', 'tgl' => '2013-11-09', 'ortu' => 'Dudy Hamsar'],
            ['nama' => 'La Ode Muh. Raihan Akbar', 'nisn' => '0146827669', 'kelas' => 'KELAS VII', 'nik' => '7407072003140001', 'tgl' => '2014-03-20', 'ortu' => 'Darfudin'],
            ['nama' => 'Muhammad Afnan Syamil', 'nisn' => '0131578267', 'kelas' => 'KELAS VII', 'nik' => '7407071009130002', 'tgl' => '2013-08-10', 'ortu' => 'Nyong Hardianto'],
            ['nama' => 'Azwar Rizqi Al-Sidiq', 'nisn' => '3132678644', 'kelas' => 'KELAS VII', 'nik' => '7407071911130001', 'tgl' => '2013-11-19', 'ortu' => 'Asbar'],
            ['nama' => 'Muh. Fahri Herdiansyah', 'nisn' => '0149893599', 'kelas' => 'KELAS VII', 'nik' => '7407072001140001', 'tgl' => '2014-01-20', 'ortu' => 'Amiruddin T'],
            ['nama' => 'La Ode Muhammad Afiq', 'nisn' => '0143010538', 'kelas' => 'KELAS VII', 'nik' => '7407071704140001', 'tgl' => '2014-04-17', 'ortu' => 'La Ui'],
            ['nama' => 'Puja Samsudin', 'nisn' => '3149844594', 'kelas' => 'KELAS VII', 'nik' => '7407057009140001', 'tgl' => '2014-09-30', 'ortu' => 'Wa Ode Anisari'],
            ['nama' => 'Adiba Kheyla Az-zahra', 'nisn' => '0145489982', 'kelas' => 'KELAS VII', 'nik' => '7407075206140001', 'tgl' => '2014-06-12', 'ortu' => 'Rahmat Hidayat Prasyad, SH'],
            ['nama' => 'Muh. Rhofi', 'nisn' => '0135222458', 'kelas' => 'KELAS VII', 'nik' => '7407070508130001', 'tgl' => '2013-08-05', 'ortu' => 'Syarif'],
            ['nama' => 'Agustian', 'nisn' => '0131394946', 'kelas' => 'KELAS VII', 'nik' => '7407032308130001', 'tgl' => '2013-08-23', 'ortu' => 'Kusnadi'],
            ['nama' => 'Lathifah Faisal', 'nisn' => '0145258586', 'kelas' => 'KELAS VII', 'nik' => '7407074205140000', 'tgl' => '2014-05-02', 'ortu' => 'Faisal Kasim'],
            ['nama' => 'Nabila Khumairah Gole', 'nisn' => '3146493183', 'kelas' => 'KELAS VII', 'nik' => '7407076803140000', 'tgl' => '2014-03-28', 'ortu' => 'Saharlan Gole'],
            ['nama' => 'Ayla Rahma', 'nisn' => '3145504948', 'kelas' => 'KELAS VII', 'nik' => '7407075802140001', 'tgl' => '2014-07-18', 'ortu' => 'Thamrin'],
            ['nama' => 'Nailah Triana Hafidza', 'nisn' => '0138348370', 'kelas' => 'KELAS VII', 'nik' => '7407036012130001', 'tgl' => '2013-12-20', 'ortu' => 'Jumiadin, SP.M.Si'],
            ['nama' => 'Rajwa Zaidatul Ar Raq', 'nisn' => '0139947149', 'kelas' => 'KELAS VII', 'nik' => '7407075109130001', 'tgl' => '2013-09-11', 'ortu' => 'Muh Rizal'],

            // KELAS VIII
            ['nama' => 'MUH. FAIZIN', 'nisn' => '3129541569', 'kelas' => 'KELAS VIII', 'nik' => '7407071910120001', 'tgl' => '2012-10-19', 'ortu' => 'Sahirudin Hamu'],
            ['nama' => 'NAZIHA RAZAK', 'nisn' => '0136278260', 'kelas' => 'KELAS VIII', 'nik' => '7407074903130001', 'tgl' => '2013-03-09', 'ortu' => 'La Andi Razak'],
            ['nama' => 'NAZMI AINUN SABRI', 'nisn' => '0138906095', 'kelas' => 'KELAS VIII', 'nik' => '7407076403130001', 'tgl' => '2013-03-24', 'ortu' => 'Sabri'],
            ['nama' => 'SYAKILLA AULIA PUTRI SUSIANTO', 'nisn' => '0129962146', 'kelas' => 'KELAS VIII', 'nik' => '7407074911120001', 'tgl' => '2012-11-09', 'ortu' => 'Heri Susianto'],
            ['nama' => 'BENING KHOIRUNNISA', 'nisn' => '0135674657', 'kelas' => 'KELAS VIII', 'nik' => '7407075605130001', 'tgl' => '2013-05-16', 'ortu' => 'La Ode Nursalim, S.Sos'],
            ['nama' => 'REZA ASMAWAN. F', 'nisn' => '0126818731', 'kelas' => 'KELAS VIII', 'nik' => '7407070511120001', 'tgl' => '2012-11-05', 'ortu' => 'Ferdy Satriawan S S.Pd'],
            ['nama' => 'IFFAT ANWARI', 'nisn' => '0123276332', 'kelas' => 'KELAS VIII', 'nik' => '7404111010120005', 'tgl' => '2012-10-10', 'ortu' => 'Usman Nurdin'],
            ['nama' => 'AFRIL WARDANA', 'nisn' => '0123430416', 'kelas' => 'KELAS VIII', 'nik' => '7407030412120001', 'tgl' => '2012-12-04', 'ortu' => 'Jufri M.'],
            ['nama' => 'Ach. Aliful Adzim', 'nisn' => null, 'kelas' => 'KELAS VIII', 'nik' => null, 'tgl' => null, 'ortu' => null],
            ['nama' => 'Anggita Gading', 'nisn' => null, 'kelas' => 'KELAS VIII', 'nik' => null, 'tgl' => null, 'ortu' => null],
            ['nama' => 'Aldiansyah', 'nisn' => null, 'kelas' => 'KELAS VIII', 'nik' => null, 'tgl' => null, 'ortu' => null],
            ['nama' => 'Muh. Athoillah', 'nisn' => null, 'kelas' => 'KELAS VIII', 'nik' => null, 'tgl' => null, 'ortu' => null],
            ['nama' => 'Farzan Atharis', 'nisn' => null, 'kelas' => 'KELAS VIII', 'nik' => null, 'tgl' => null, 'ortu' => null],

            // KELAS IX
            ['nama' => 'Arya Syaifullah', 'nisn' => null, 'kelas' => 'KELAS IX', 'nik' => null, 'tgl' => null, 'ortu' => null],
            ['nama' => 'Alif AR', 'nisn' => null, 'kelas' => 'KELAS IX', 'nik' => null, 'tgl' => null, 'ortu' => null],
            ['nama' => 'Ashabul Kahfi', 'nisn' => null, 'kelas' => 'KELAS IX', 'nik' => null, 'tgl' => null, 'ortu' => null],
            ['nama' => 'Dielzi Arumika', 'nisn' => null, 'kelas' => 'KELAS IX', 'nik' => null, 'tgl' => null, 'ortu' => null],
            ['nama' => 'Haikal Brayen', 'nisn' => null, 'kelas' => 'KELAS IX', 'nik' => null, 'tgl' => null, 'ortu' => null],
            ['nama' => 'Hazidzia Hiraswati', 'nisn' => null, 'kelas' => 'KELAS IX', 'nik' => null, 'tgl' => null, 'ortu' => null],
            ['nama' => 'Maulana S.Z.P', 'nisn' => null, 'kelas' => 'KELAS IX', 'nik' => null, 'tgl' => null, 'ortu' => null],
            ['nama' => 'Muh. Adrian', 'nisn' => null, 'kelas' => 'KELAS IX', 'nik' => null, 'tgl' => null, 'ortu' => null],
            ['nama' => 'Muh. Alfikra', 'nisn' => null, 'kelas' => 'KELAS IX', 'nik' => null, 'tgl' => null, 'ortu' => null],
            ['nama' => 'Muh. AlFatih', 'nisn' => null, 'kelas' => 'KELAS IX', 'nik' => null, 'tgl' => null, 'ortu' => null],
            ['nama' => 'Nur Fatma Mizra', 'nisn' => null, 'kelas' => 'KELAS IX', 'nik' => null, 'tgl' => null, 'ortu' => null],
            ['nama' => 'Nur Fatma Wati', 'nisn' => null, 'kelas' => 'KELAS IX', 'nik' => null, 'tgl' => null, 'ortu' => null],
            ['nama' => 'Sri Yulan', 'nisn' => null, 'kelas' => 'KELAS IX', 'nik' => null, 'tgl' => null, 'ortu' => null],
            ['nama' => 'Suhaila Afriyatun', 'nisn' => null, 'kelas' => 'KELAS IX', 'nik' => null, 'tgl' => null, 'ortu' => null],
            ['nama' => 'Syafira A.Z', 'nisn' => null, 'kelas' => 'KELAS IX', 'nik' => null, 'tgl' => null, 'ortu' => null],

            // KELAS XI (MA)
            ['nama' => 'Alim Nur Qodim', 'nisn' => null, 'kelas' => 'KELAS XI', 'nik' => null, 'tgl' => null, 'ortu' => null],
            ['nama' => 'Awan', 'nisn' => null, 'kelas' => 'KELAS XI', 'nik' => null, 'tgl' => null, 'ortu' => null],
            ['nama' => 'Selvi Aulia', 'nisn' => null, 'kelas' => 'KELAS XI', 'nik' => null, 'tgl' => null, 'ortu' => null],
            ['nama' => 'Siti Marwa', 'nisn' => null, 'kelas' => 'KELAS XI', 'nik' => null, 'tgl' => null, 'ortu' => null],
            ['nama' => 'Mawar Anjani', 'nisn' => null, 'kelas' => 'KELAS XI', 'nik' => null, 'tgl' => null, 'ortu' => null],
            ['nama' => 'Nur Furqon', 'nisn' => null, 'kelas' => 'KELAS XI', 'nik' => null, 'tgl' => null, 'ortu' => null],
        ];

        $rombelMap = [
            'KELAS VII' => $rombelMts7->id,
            'KELAS VIII' => $rombelMts8->id,
            'KELAS IX' => $rombelMts9->id,
            'KELAS XI' => $rombelMa11->id,
        ];

        $countSiswa = 0;
        $countPlotting = 0;

        foreach ($siswaDataDetailed as $s) {
            $nama = trim($s['nama']);
            $rombelId = $rombelMap[$s['kelas']] ?? null;

            // 1. Cari atau buat Orang
            $orang = Orang::where('nama_lengkap', 'like', "%{$nama}%")->first();
            if (!$orang) {
                $orang = Orang::create([
                    'nama_lengkap' => $nama,
                    'nik' => $s['nik'] ?: null,
                    'tanggal_lahir' => $s['tgl'] ?: null,
                    'niup' => 'ST-' . rand(10000, 99999),
                    'is_active' => true,
                ]);
            } else {
                if ($s['nik']) $orang->nik = $s['nik'];
                if ($s['tgl']) $orang->tanggal_lahir = $s['tgl'];
                $orang->save();
            }

            // 2. Cari atau buat Peserta Didik
            $peserta = PesertaDidik::firstOrCreate(
                ['orang_id' => $orang->id],
                [
                    'nisn' => $s['nisn'] ?: null,
                    'is_active' => true,
                ]
            );
            if ($s['nisn'] && !$peserta->nisn) {
                $peserta->nisn = $s['nisn'];
                $peserta->save();
            }

            $countSiswa++;

            // 3. Tambahkan Orang Tua jika ada
            if ($s['ortu']) {
                $ortuNama = trim($s['ortu']);
                $ortu = Orang::firstOrCreate(
                    ['nama_lengkap' => $ortuNama],
                    ['is_active' => true]
                );
                HubunganKeluarga::firstOrCreate([
                    'keluarga_id' => $ortu->id,
                    'orang_id' => $orang->id,
                ], [
                    'jenis_hubungan' => 'ORANG_TUA',
                ]);
            }

            // 4. Plotting ke Rombel / Kelas
            if ($rombelId && $peserta) {
                RiwayatRombelPeserta::firstOrCreate([
                    'peserta_didik_id' => $peserta->id,
                    'rombel_id' => $rombelId,
                ], [
                    'is_active' => true,
                ]);
                $countPlotting++;
            }
        }

        $this->command->info("BERHASIL! Mengimpor {$countSiswa} Santri dan melakukan plotting ke Rombel MTs & MA ({$countPlotting} penempatan kelas).");
    }
}
