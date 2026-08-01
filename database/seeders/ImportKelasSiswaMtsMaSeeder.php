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

        // Data TEPAT 53 Santri MTs dari DATA SEMENTARA SISWA MTS NURUL FURQON DESA TIMU.xlsx
        $siswaDataDetailed = [
            // KELAS VII (24 Santri)
            ['nama' => 'Achmad Mukhizan Amran', 'nisn' => '0132868735', 'kelas' => 'KELAS VII', 'nik' => '7407012311130000', 'tgl' => '2013-11-23', 'ortu' => 'Amdani Agus Irawan Amran, SH dan Wa Ode Marhani'],
            ['nama' => 'Selvi Nadila', 'nisn' => '3135415105', 'kelas' => 'KELAS VII', 'nik' => '9117056102130001', 'tgl' => '2013-02-21', 'ortu' => 'Sule dan Nofianti'],
            ['nama' => 'Nazril Sutriyono', 'nisn' => '0145798742', 'kelas' => 'KELAS VII', 'nik' => '7407071806140002', 'tgl' => '2014-07-18', 'ortu' => 'Suryono dan Suti Wabula'],
            ['nama' => 'Edwar Syafaat', 'nisn' => '0133799399', 'kelas' => 'KELAS VII', 'nik' => '7407031911130001', 'tgl' => '2013-11-19', 'ortu' => 'Samsuddin Ane dan Nur Asyiani'],
            ['nama' => 'Wa Ode Eka Aprilia Kaimuddin', 'nisn' => '340440278', 'kelas' => 'KELAS VII', 'nik' => '8106016804140001', 'tgl' => '2014-04-28', 'ortu' => 'La Ode Ahmad Yani Kaimuddin dan Wa Adelia bugis'],
            ['nama' => 'Nazmi Khumairah', 'nisn' => '0143521049', 'kelas' => 'KELAS VII', 'nik' => '7407075801140001', 'tgl' => '2014-01-18', 'ortu' => 'Muh. Syaiful, S.Kom dan Fitriasari'],
            ['nama' => 'Nazwa Azqia Ferdiang', 'nisn' => '3141298168', 'kelas' => 'KELAS VII', 'nik' => '9118016101140002', 'tgl' => '2014-01-21', 'ortu' => 'Ferdiang Agung, ST dan Wa Ode Sardiana'],
            ['nama' => 'Wa Ode Meysya Humairoh', 'nisn' => '0142047073', 'kelas' => 'KELAS VII', 'nik' => '7407076306140001', 'tgl' => '2014-05-23', 'ortu' => 'La Ode Jakaria dan Asmah'],
            ['nama' => 'Anisa', 'nisn' => '0147113815', 'kelas' => 'KELAS VII', 'nik' => '7407076601140001', 'tgl' => '2014-01-26', 'ortu' => 'La Ali Ane dan Asriani'],
            ['nama' => 'Al-Vazhar Tryadi', 'nisn' => null, 'kelas' => 'KELAS VII', 'nik' => '7407070911130001', 'tgl' => '2013-11-09', 'ortu' => 'Dudy Hamsar dan Rusni'],
            ['nama' => 'La Ode Muh. Raihan Akbar', 'nisn' => '0146827669', 'kelas' => 'KELAS VII', 'nik' => '7407072003140001', 'tgl' => '2014-03-20', 'ortu' => 'Darfudin dan Nurma'],
            ['nama' => 'Muhammad Afnan Syamil', 'nisn' => '0131578267', 'kelas' => 'KELAS VII', 'nik' => '7407071009130002', 'tgl' => '2013-08-10', 'ortu' => 'Nyong Hardianto dan Maryam'],
            ['nama' => 'Azwar Rizqi Al-Sidiq', 'nisn' => '3132678644', 'kelas' => 'KELAS VII', 'nik' => '7407071911130001', 'tgl' => '2013-11-19', 'ortu' => 'Asbar dan Hamsatu'],
            ['nama' => 'Muh. Fahri Herdiansyah', 'nisn' => '0149893599', 'kelas' => 'KELAS VII', 'nik' => '7407072001140001', 'tgl' => '2014-01-20', 'ortu' => 'Amiruddin T dan Nurhayati'],
            ['nama' => 'La Ode Muhammad Afiq', 'nisn' => '0143010538', 'kelas' => 'KELAS VII', 'nik' => '7407071704140001', 'tgl' => '2014-04-17', 'ortu' => 'La Ui dan Wa Kala'],
            ['nama' => 'Puja Samsudin', 'nisn' => '3149844594', 'kelas' => 'KELAS VII', 'nik' => '7407057009140001', 'tgl' => '2014-09-30', 'ortu' => 'Wa Ode Anisari dan Emi Yuningsih'],
            ['nama' => 'Adiba Kheyla Az-zahra', 'nisn' => '0145489982', 'kelas' => 'KELAS VII', 'nik' => '7407075206140001', 'tgl' => '2014-06-12', 'ortu' => 'Rahmat Hidayat Prasyad, SH dan Harsia'],
            ['nama' => 'Muh. Rhofi', 'nisn' => '0135222458', 'kelas' => 'KELAS VII', 'nik' => '7407070508130001', 'tgl' => '2013-08-05', 'ortu' => 'Syarif dan Darmawati'],
            ['nama' => 'Agustian', 'nisn' => '0131394946', 'kelas' => 'KELAS VII', 'nik' => '7407032308130001', 'tgl' => '2013-08-23', 'ortu' => 'Kusnadi dan Wa Sartina Abuhasa'],
            ['nama' => 'Lathifah Faisal', 'nisn' => '0145258586', 'kelas' => 'KELAS VII', 'nik' => '7407074205140000', 'tgl' => '2014-05-02', 'ortu' => 'Faisal Kasim dan Arliani'],
            ['nama' => 'Nabila Khumairah Gole', 'nisn' => '3146493183', 'kelas' => 'KELAS VII', 'nik' => '7407076803140000', 'tgl' => '2014-03-28', 'ortu' => 'Saharlan Gole dan Sitin Mujiatun'],
            ['nama' => 'Ayla Rahma', 'nisn' => '3145504948', 'kelas' => 'KELAS VII', 'nik' => '7407075802140001', 'tgl' => '2014-07-18', 'ortu' => 'Thamrin dan Sehawati'],
            ['nama' => 'Nailah Triana Hafidza', 'nisn' => '0138348370', 'kelas' => 'KELAS VII', 'nik' => '7407036012130001', 'tgl' => '2013-12-20', 'ortu' => 'Jumiadin, SP.M.Si dan Mahasriwati'],
            ['nama' => 'Rajwa Zaidatul Ar Raq', 'nisn' => '0139947149', 'kelas' => 'KELAS VII', 'nik' => '7407075109130001', 'tgl' => '2013-09-11', 'ortu' => 'Muh Rizal dan Mariani'],

            // KELAS VIII (13 Santri)
            ['nama' => 'MUH.  FAIZIN', 'nisn' => '3129541569', 'kelas' => 'KELAS VIII', 'nik' => '7407071910120001', 'tgl' => '2012-10-19', 'ortu' => 'Sahirudin Hamu dan jumiara'],
            ['nama' => 'NAZIHA RAZAK', 'nisn' => '0136278260', 'kelas' => 'KELAS VIII', 'nik' => '7407074903130001', 'tgl' => '2013-03-09', 'ortu' => 'La Andi Razak dan Yusmawati'],
            ['nama' => 'NAZMI AINUN SABRI', 'nisn' => '0138906095', 'kelas' => 'KELAS VIII', 'nik' => '7407076403130001', 'tgl' => '2013-03-24', 'ortu' => 'Sabri dan Mursida'],
            ['nama' => 'SYAKILLA AULIA PUTRI SUSIANTO', 'nisn' => '0129962146', 'kelas' => 'KELAS VIII', 'nik' => '7407074911120001', 'tgl' => '2012-11-09', 'ortu' => 'Heri Susianto dan Erlin Syuherlin Musa'],
            ['nama' => 'BENING KHOIRUNNISA', 'nisn' => '0135674657', 'kelas' => 'KELAS VIII', 'nik' => '7407075605130001', 'tgl' => '2013-05-16', 'ortu' => 'La Ode Nursalim, S.Sos dan Jusniati'],
            ['nama' => 'REZA ASMAWAN. F', 'nisn' => '0126818731', 'kelas' => 'KELAS VIII', 'nik' => '7407070511120001', 'tgl' => '2012-11-05', 'ortu' => 'Ferdy Satriawan S S.Pd dan Asriana'],
            ['nama' => 'IFFAT ANWARI', 'nisn' => '0123276332', 'kelas' => 'KELAS VIII', 'nik' => '7404111010120005', 'tgl' => '2012-10-10', 'ortu' => 'Usman Nurdin dan Saltia'],
            ['nama' => 'AFRIL WARDANA', 'nisn' => '0123430416', 'kelas' => 'KELAS VIII', 'nik' => '7407030412120001', 'tgl' => '2012-12-04', 'ortu' => 'Jufri M. dan Wa Zainab R'],
            ['nama' => 'MUHAMMAD ATHO\'ILLAH ROBBANI ISA', 'nisn' => '3134312095', 'kelas' => 'KELAS VIII', 'nik' => '7407071706130001', 'tgl' => '2013-06-17', 'ortu' => 'Sumail, S.Pd.I.M.Pd dan Afriani'],
            ['nama' => 'AHMAD ALIFUL ADZIM AL-CHAK', 'nisn' => '0132198244', 'kelas' => 'KELAS VIII', 'nik' => '7407030203130001', 'tgl' => '2013-03-02', 'ortu' => 'Muh. Sobirin Chasbi dan Hamsana'],
            ['nama' => 'FARZAN ATHARIZZ', 'nisn' => '0134688725', 'kelas' => 'KELAS VIII', 'nik' => '7407030707130001', 'tgl' => '2013-07-07', 'ortu' => 'Animbaun dan Ernawati'],
            ['nama' => 'ALDIANSYAH PUTRA', 'nisn' => '0138926965', 'kelas' => 'KELAS VIII', 'nik' => '7407070107130001', 'tgl' => '2013-07-01', 'ortu' => 'Abdul Majid dan Sunarmiyanti'],
            ['nama' => 'ANGGITA GADING', 'nisn' => '0129171193', 'kelas' => 'KELAS VIII', 'nik' => '7407065408120001', 'tgl' => '2012-08-14', 'ortu' => 'La Aca dan Pratiwi'],

            // KELAS IX (16 Santri)
            ['nama' => 'ALIF AHMAD RAMADHAN', 'nisn' => '0124390232', 'kelas' => 'KELAS IX', 'nik' => '7407070608120001', 'tgl' => '2012-08-06', 'ortu' => 'Samuddin dan Yusriani'],
            ['nama' => 'ASHABUL KAHFI', 'nisn' => '0119940514', 'kelas' => 'KELAS IX', 'nik' => '7407071609110002', 'tgl' => '2011-09-16', 'ortu' => 'Didin dan Masnia'],
            ['nama' => 'DIELLZY ARUMI KAIMUDDIN', 'nisn' => '0119699920', 'kelas' => 'KELAS IX', 'nik' => '8171024909110004', 'tgl' => '2011-09-09', 'ortu' => 'Suwardi dan Ismi Asma Ali'],
            ['nama' => 'FAIZAL AKBAR', 'nisn' => '0122698905', 'kelas' => 'KELAS IX', 'nik' => '7407070201120001', 'tgl' => '2012-01-02', 'ortu' => 'Aris dan Husliani'],
            ['nama' => 'HAFIDYA HIRASWATI', 'nisn' => '0123545359', 'kelas' => 'KELAS IX', 'nik' => '7407076101120001', 'tgl' => '2012-01-21', 'ortu' => 'La Isa dan Wa Ona'],
            ['nama' => 'HAIKAL BRAYEN', 'nisn' => '0127452266', 'kelas' => 'KELAS IX', 'nik' => '7405101301140001', 'tgl' => '2012-01-12', 'ortu' => 'Rayendra dan Eka Ratnasari'],
            ['nama' => 'MUH. AL FIKRA PRATAMA', 'nisn' => '0116601266', 'kelas' => 'KELAS IX', 'nik' => '7407071009110001', 'tgl' => '2011-09-10', 'ortu' => 'Jarni dan Wa Jumi'],
            ['nama' => 'MUHAMMAD ADRIAN', 'nisn' => '0122897065', 'kelas' => 'KELAS IX', 'nik' => '7407070605120001', 'tgl' => '2012-05-06', 'ortu' => 'Anwar dan Mariati'],
            ['nama' => 'MUHAMMAD ARYA SAIFULLAH', 'nisn' => '0125134860', 'kelas' => 'KELAS IX', 'nik' => '7407072404120002', 'tgl' => '2012-04-24', 'ortu' => 'Samsul dan Nur Hayati'],
            ['nama' => 'SYAFIRA AZZAHRA', 'nisn' => '0111200750', 'kelas' => 'KELAS IX', 'nik' => '7407077112100002', 'tgl' => '2011-09-28', 'ortu' => 'L.M. Usman dan Sartika'],
            ['nama' => 'NUR FATMAWATI', 'nisn' => '0128456190', 'kelas' => 'KELAS IX', 'nik' => '7407036803120001', 'tgl' => '2012-03-28', 'ortu' => 'Basraruddin dan Maliana'],
            ['nama' => 'SUHAILAH AFRIATUN. B', 'nisn' => '0126228831', 'kelas' => 'KELAS IX', 'nik' => '7407036304120001', 'tgl' => '2012-04-23', 'ortu' => 'Basri dan Surni'],
            ['nama' => 'MUHAMMAD AL FATIH', 'nisn' => '0126609421', 'kelas' => 'KELAS IX', 'nik' => '7407070508120001', 'tgl' => '2012-08-05', 'ortu' => 'Abdul Haji dan Zul Asma'],
            ['nama' => 'SRI YULAN', 'nisn' => '0117746402', 'kelas' => 'KELAS IX', 'nik' => '7407034904120001', 'tgl' => '2012-04-09', 'ortu' => 'Agus Tiawan dan Suniati'],
            ['nama' => 'NURFATMA MIZRA', 'nisn' => '0123340063', 'kelas' => 'KELAS IX', 'nik' => '7407035506120001', 'tgl' => '2012-06-15', 'ortu' => 'Ademaun Badiun dan Nurma'],
            ['nama' => 'MAULANA SYAMSIDH ZAIN PUTRA', 'nisn' => '0117607841', 'kelas' => 'KELAS IX', 'nik' => '7407033009110001', 'tgl' => '2011-09-30', 'ortu' => 'Ahmad Zain dan Sumiati'],
        ];

        $rombelMap = [
            'KELAS VII' => $rombelMts7->id,
            'KELAS VIII' => $rombelMts8->id,
            'KELAS IX' => $rombelMts9->id,
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
                    'status' => 'AKTIF',
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
                $ortu = Orang::where('nama_lengkap', 'like', "%{$ortuNama}%")->first();
                if (!$ortu) {
                    $ortu = Orang::create([
                        'nama_lengkap' => $ortuNama,
                        'niup' => 'WLI-' . date('Y') . '-' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
                        'is_active' => true,
                    ]);
                }
                HubunganKeluarga::firstOrCreate([
                    'keluarga_id' => $ortu->id,
                    'orang_id' => $orang->id,
                ], [
                    'hubungan' => 'WALI',
                    'is_wali_utama' => true,
                ]);
            }

            // 4. Plotting ke Rombel / Kelas
            if ($rombelId && $peserta) {
                RiwayatRombelPeserta::firstOrCreate([
                    'peserta_didik_id' => $peserta->id,
                    'rombel_id' => $rombelId,
                    'tahun_pelajaran_id' => $tahunAktif->id,
                ], [
                    'status' => 'AKTIF',
                ]);
                $countPlotting++;
            }
        }

        $this->command->info("BERHASIL! Mengimpor {$countSiswa} Santri MTs dan melakukan plotting ke Rombel MTs ({$countPlotting} penempatan kelas).");
    }
}
