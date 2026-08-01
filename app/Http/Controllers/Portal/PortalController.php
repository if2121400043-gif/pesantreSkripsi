<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\HubunganKeluarga;
use App\Models\PesertaDidik;
use App\Models\Tagihan;
use App\Models\PresensiKelas;
use App\Models\NilaiRapor;
use App\Models\CatatanPelanggaran;
use App\Models\CatatanPrestasi;
use App\Models\Pembayaran;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    /**
     * Get the santri IDs linked to the logged-in wali user.
     */
    private function getAnakIds()
    {
        $user = auth()->user();
        $orangId = $user->orang_id;

        // Find all children linked through hubungan_keluarga
        $anakOrangIds = HubunganKeluarga::where('keluarga_id', $orangId)
            ->pluck('orang_id');

        // Get peserta_didik records for those orang IDs
        $anakList = PesertaDidik::whereIn('orang_id', $anakOrangIds)
            ->where('status', 'AKTIF')
            ->get();

        // Switch Child Logic
        if (request()->has('anak_id')) {
            $validAnak = $anakList->firstWhere('id', request('anak_id'));
            if ($validAnak) {
                session(['active_anak_id' => $validAnak->id]);
            }
        }

        return $anakList;
    }

    private function getActiveChild()
    {
        $anakList = $this->getAnakIds();
        $activeAnakId = session('active_anak_id') ?? ($anakList->first()->id ?? null);
        $activeAnak = $anakList->firstWhere('id', $activeAnakId);
        
        // Fallback for admin/testing user if no linked child found
        if (!$activeAnak) {
            $activeAnak = PesertaDidik::with(['orang', 'lembaga', 'rombelAktif', 'kamarAktif.asrama'])->where('status', 'AKTIF')->first();
        }

        return [$anakList, $activeAnak];
    }

    public function beranda(Request $request)
    {
        list($anakList, $activeAnak) = $this->getActiveChild();
        $activeTahun = TahunPelajaran::where('is_active', true)->first();

        // Kehadiran Stats
        $presensis = collect();
        $kehadiranStats = ['HADIR' => 0, 'SAKIT' => 0, 'IZIN' => 0, 'ALPHA' => 0];
        $kehadiranPersen = 100;
        if ($activeAnak && $activeTahun) {
            $presensis = PresensiKelas::where('peserta_didik_id', $activeAnak->id)
                ->whereHas('rombel', function($q) use ($activeTahun) {
                    $q->where('tahun_pelajaran_id', $activeTahun->id);
                })
                ->orderBy('tanggal', 'desc')
                ->get();

            $totalPresensi = $presensis->count();
            if ($totalPresensi > 0) {
                foreach ($presensis as $p) {
                    $statusUpper = strtoupper($p->status);
                    if (array_key_exists($statusUpper, $kehadiranStats)) {
                        $kehadiranStats[$statusUpper]++;
                    }
                }
                $kehadiranPersen = round(($kehadiranStats['HADIR'] / $totalPresensi) * 100);
            }
        }

        // Pelanggaran & Prestasi Stats
        $totalPoinPelanggaran = 0;
        $pelanggarans = collect();
        $prestasis = collect();
        if ($activeAnak && $activeTahun) {
            $pelanggarans = CatatanPelanggaran::with('jenisPelanggaran')
                ->where('peserta_didik_id', $activeAnak->id)
                ->where('tahun_pelajaran_id', $activeTahun->id)
                ->orderBy('tanggal', 'desc')
                ->get();
            
            $totalPoinPelanggaran = $pelanggarans->sum(function($p) {
                return $p->jenisPelanggaran->poin ?? 0;
            });

            $prestasis = CatatanPrestasi::where('peserta_didik_id', $activeAnak->id)
                ->where('tahun_pelajaran_id', $activeTahun->id)
                ->orderBy('tanggal', 'desc')
                ->get();
        }

        // Status Kedisiplinan
        if ($totalPoinPelanggaran === 0) {
            $statusKedisiplinan = 'Sangat Baik';
        } elseif ($totalPoinPelanggaran <= 15) {
            $statusKedisiplinan = 'Baik';
        } elseif ($totalPoinPelanggaran <= 50) {
            $statusKedisiplinan = 'Perlu Perhatian';
        } else {
            $statusKedisiplinan = 'SP / Panggilan';
        }

        // Keuangan Stats
        $sppStatus = 'Lunas';
        $tagihanTerakhirMsg = 'Belum Ada Transaksi';
        $totalTagihanBelumLunas = 0;
        $riwayatTagihan = collect();

        if ($activeAnak && $activeTahun) {
            $riwayatTagihan = Tagihan::with(['komponenBiaya', 'pembayaran'])
                ->where('peserta_didik_id', $activeAnak->id)
                ->where('tahun_pelajaran_id', $activeTahun->id)
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($riwayatTagihan as $tagihan) {
                $tagihan->refreshPaymentStatus();
                $tagihan->refresh();

                $dibayar = $tagihan->pembayaran->sum('jumlah');
                $sisa = max(0, (float) $tagihan->total - (float) $dibayar);
                $statusAktual = $sisa <= 0 ? 'LUNAS' : ($dibayar > 0 ? 'SEBAGIAN' : $tagihan->status);

                if ($statusAktual !== 'LUNAS' && $sisa > 0) {
                    $totalTagihanBelumLunas += $sisa;
                }

                $tagihan->status = $statusAktual;
            }

            // SPP Bulan Ini
            $monthsIndonesian = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $currentMonthName = $monthsIndonesian[now()->month];

            $sppBulanIni = $riwayatTagihan->first(function($t) use ($currentMonthName) {
                return $t->bulan === $currentMonthName;
            });

            if ($sppBulanIni) {
                $sppStatus = $sppBulanIni->status === 'LUNAS' ? 'Lunas' : 'Belum Lunas';
            }

            // Last payment
            $lastPembayaran = Pembayaran::whereHas('tagihan', function($q) use ($activeAnak) {
                $q->where('peserta_didik_id', $activeAnak->id);
            })->orderBy('tanggal_bayar', 'desc')->first();

            if ($lastPembayaran) {
                $tagihanTerakhirMsg = "Tagihan Terakhir: Dibayar " . $lastPembayaran->tanggal_bayar->format('d/m/Y');
            }
        }

        // Rincian Nilai
        $grades = collect();
        if ($activeAnak && $activeTahun) {
            $grades = NilaiRapor::with('mataPelajaran')
                ->where('peserta_didik_id', $activeAnak->id)
                ->where('tahun_pelajaran_id', $activeTahun->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $activeTab = $request->get('tab', 'nilai');

        return view('portal.beranda', compact(
            'anakList', 'activeAnak', 'activeTahun',
            'kehadiranStats', 'kehadiranPersen', 'presensis',
            'totalPoinPelanggaran', 'statusKedisiplinan', 'pelanggarans', 'prestasis',
            'sppStatus', 'tagihanTerakhirMsg', 'totalTagihanBelumLunas', 'riwayatTagihan',
            'grades', 'activeTab'
        ));
    }

    public function tagihan(Request $request)
    {
        list($anakList, $activeAnak) = $this->getActiveChild();
        $activeTahun = TahunPelajaran::where('is_active', true)->first();

        $tagihans = collect();
        $totalTagihanBelumLunas = 0;

        if ($activeAnak && $activeTahun) {
            $tagihans = Tagihan::with(['komponenBiaya', 'pembayaran', 'pesertaDidik.orang', 'tahunPelajaran'])
                ->where('peserta_didik_id', $activeAnak->id)
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            foreach ($tagihans as $tagihan) {
                $tagihan->refreshPaymentStatus();
                $dibayar = $tagihan->pembayaran->sum('jumlah');
                $sisa = max(0, (float) $tagihan->total - (float) $dibayar);
                if ($sisa > 0) {
                    $totalTagihanBelumLunas += $sisa;
                }
            }
        }

        return view('portal.tagihan', compact('anakList', 'activeAnak', 'activeTahun', 'tagihans', 'totalTagihanBelumLunas'));
    }

    public function presensi(Request $request)
    {
        list($anakList, $activeAnak) = $this->getActiveChild();
        $activeTahun = TahunPelajaran::where('is_active', true)->first();

        $presensis = collect();
        $kehadiranStats = ['HADIR' => 0, 'SAKIT' => 0, 'IZIN' => 0, 'ALPHA' => 0];
        $kehadiranPersen = 100;

        if ($activeAnak && $activeTahun) {
            $presensis = PresensiKelas::with('rombel')
                ->where('peserta_didik_id', $activeAnak->id)
                ->orderBy('tanggal', 'desc')
                ->paginate(20);

            $totalPresensi = $presensis->total();
            if ($totalPresensi > 0) {
                $allPresensi = PresensiKelas::where('peserta_didik_id', $activeAnak->id)->get();
                foreach ($allPresensi as $p) {
                    $statusUpper = strtoupper($p->status);
                    if (array_key_exists($statusUpper, $kehadiranStats)) {
                        $kehadiranStats[$statusUpper]++;
                    }
                }
                $kehadiranPersen = round(($kehadiranStats['HADIR'] / max(1, $allPresensi->count())) * 100);
            }
        }

        return view('portal.presensi', compact('anakList', 'activeAnak', 'activeTahun', 'presensis', 'kehadiranStats', 'kehadiranPersen'));
    }

    public function kedisiplinan(Request $request)
    {
        list($anakList, $activeAnak) = $this->getActiveChild();
        $activeTahun = TahunPelajaran::where('is_active', true)->first();

        $pelanggarans = collect();
        $prestasis = collect();
        $totalPoinPelanggaran = 0;

        if ($activeAnak && $activeTahun) {
            $pelanggarans = CatatanPelanggaran::with('jenisPelanggaran')
                ->where('peserta_didik_id', $activeAnak->id)
                ->orderBy('tanggal', 'desc')
                ->paginate(15);
            
            $allPelanggarans = CatatanPelanggaran::with('jenisPelanggaran')
                ->where('peserta_didik_id', $activeAnak->id)
                ->get();

            $totalPoinPelanggaran = $allPelanggarans->sum(function($p) {
                return $p->jenisPelanggaran->poin ?? 0;
            });

            $prestasis = CatatanPrestasi::where('peserta_didik_id', $activeAnak->id)
                ->orderBy('tanggal', 'desc')
                ->get();
        }

        if ($totalPoinPelanggaran === 0) {
            $statusKedisiplinan = 'Sangat Baik';
        } elseif ($totalPoinPelanggaran <= 15) {
            $statusKedisiplinan = 'Baik';
        } elseif ($totalPoinPelanggaran <= 50) {
            $statusKedisiplinan = 'Perlu Perhatian';
        } else {
            $statusKedisiplinan = 'SP / Panggilan';
        }

        return view('portal.kedisiplinan', compact(
            'anakList', 'activeAnak', 'activeTahun', 
            'pelanggarans', 'prestasis', 'totalPoinPelanggaran', 'statusKedisiplinan'
        ));
    }
}
