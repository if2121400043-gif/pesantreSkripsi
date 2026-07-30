<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PesertaDidik;
use App\Models\Pegawai;
use App\Models\Rombel;
use App\Models\CalonSantri;
use App\Models\TahunPelajaran;
use App\Models\Lembaga;
use App\Models\Asrama;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\CatatanPelanggaran;
use App\Models\PerizinanKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        
        $totalSantri = PesertaDidik::where('status', 'AKTIF')->count();
        $totalPegawai = Pegawai::where('is_active', true)->count();
        $totalRombel = Rombel::count();
        $totalPendaftar = CalonSantri::count();

        // Statistik Perizinan (PEDATREN Visuals)
        $dalamMasaIzinCount = PerizinanKeluar::where('status', 'DISETUJUI')
            ->whereNull('waktu_kembali_aktual')
            ->where('waktu_kembali_rencana', '>=', Carbon::now())
            ->count();

        $telatBelumKembaliCount = PerizinanKeluar::where('status', 'DISETUJUI')
            ->whereNull('waktu_kembali_aktual')
            ->where('waktu_kembali_rencana', '<', Carbon::now())
            ->count();

        // 1. Data Santri per Lembaga
        $lembagas = Lembaga::orderBy('urutan')->get();
        $labelsLembaga = [];
        $dataPutra = [];
        $dataPutri = [];

        if ($tahunAktif) {
            foreach ($lembagas as $lembaga) {
                $labelsLembaga[] = $lembaga->singkatan ?? $lembaga->nama;
                
                $putra = DB::table('riwayat_rombel_peserta')
                    ->join('rombel', 'riwayat_rombel_peserta.rombel_id', '=', 'rombel.id')
                    ->join('peserta_didik', 'riwayat_rombel_peserta.peserta_didik_id', '=', 'peserta_didik.id')
                    ->join('orang', 'peserta_didik.orang_id', '=', 'orang.id')
                    ->where('rombel.lembaga_id', $lembaga->id)
                    ->where('riwayat_rombel_peserta.tahun_pelajaran_id', $tahunAktif->id)
                    ->where('riwayat_rombel_peserta.status', 'AKTIF')
                    ->where('peserta_didik.status', 'AKTIF')
                    ->where('orang.jenis_kelamin', 'L')
                    ->whereNull('peserta_didik.deleted_at')
                    ->count();

                $putri = DB::table('riwayat_rombel_peserta')
                    ->join('rombel', 'riwayat_rombel_peserta.rombel_id', '=', 'rombel.id')
                    ->join('peserta_didik', 'riwayat_rombel_peserta.peserta_didik_id', '=', 'peserta_didik.id')
                    ->join('orang', 'peserta_didik.orang_id', '=', 'orang.id')
                    ->where('rombel.lembaga_id', $lembaga->id)
                    ->where('riwayat_rombel_peserta.tahun_pelajaran_id', $tahunAktif->id)
                    ->where('riwayat_rombel_peserta.status', 'AKTIF')
                    ->where('peserta_didik.status', 'AKTIF')
                    ->where('orang.jenis_kelamin', 'P')
                    ->whereNull('peserta_didik.deleted_at')
                    ->count();

                $dataPutra[] = $putra;
                $dataPutri[] = $putri;
            }
        }

        // 2. Data Distribusi Asrama (Realtime)
        $asramas = Asrama::where('is_active', true)->withCount('kamar')->get();
        $labelsAsrama = [];
        $dataAsrama = [];

        foreach ($asramas as $asrama) {
            // Count active mukim students in this asrama
            $mukimCount = DB::table('peserta_mukim_tahun')
                ->join('kamar', 'peserta_mukim_tahun.kamar_id', '=', 'kamar.id')
                ->where('kamar.asrama_id', $asrama->id)
                ->where('peserta_mukim_tahun.status_mukim', 'MUKIM')
                ->count();
                
            // If no mukim record yet, count total kamar in asrama so chart displays current infrastructure distribution
            $displayValue = $mukimCount > 0 ? $mukimCount : $asrama->kamar_count;

            $labelsAsrama[] = $asrama->nama;
            $dataAsrama[] = $displayValue;
        }

        // 3. Rekapitulasi Tagihan (Bulan ini)
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $namaBulan = Carbon::now()->translatedFormat('F Y');
        
        $totalTagihanValue = Tagihan::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('total');

        $tagihanBulanIni = Tagihan::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->pluck('id');

        $totalTerbayarValue = Pembayaran::whereIn('tagihan_id', $tagihanBulanIni)->sum('jumlah');
        $sisaTagihanValue = max(0, $totalTagihanValue - $totalTerbayarValue);

        $persenLunas = $totalTagihanValue > 0 ? round(($totalTerbayarValue / $totalTagihanValue) * 100) : 0;
        
        $tunggakanValue = Tagihan::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->where('jatuh_tempo', '<', Carbon::now()->startOfDay())
            ->whereIn('status', ['BELUM_BAYAR', 'SEBAGIAN'])
            ->sum('total');
            
        $persenTunggakan = $totalTagihanValue > 0 ? round(($tunggakanValue / $totalTagihanValue) * 100) : 0;
        $persenBelumLunas = max(0, 100 - $persenLunas - $persenTunggakan);

        $rekapTagihan = [
            'bulan' => $namaBulan,
            'total' => $totalTagihanValue,
            'terbayar' => $totalTerbayarValue,
            'sisa' => $sisaTagihanValue,
            'persenLunas' => $persenLunas,
            'persenBelumLunas' => $persenBelumLunas,
            'persenTunggakan' => $persenTunggakan
        ];

        // 4. Aktivitas Terbaru
        $activities = [];

        $pendaftars = CalonSantri::orderBy('created_at', 'desc')->limit(3)->get();
        foreach($pendaftars as $p) {
            $activities[] = [
                'type' => 'pendaftaran',
                'title' => '<span class="font-semibold">' . htmlspecialchars($p->nama_lengkap) . '</span> terdaftar sebagai santri baru',
                'time' => $p->created_at,
                'icon' => 'user-plus',
                'bg_color' => 'bg-primary-100',
                'icon_color' => 'text-primary-600'
            ];
        }

        $pembayarans = Pembayaran::with('tagihan.pesertaDidik.orang')->orderBy('created_at', 'desc')->limit(3)->get();
        foreach($pembayarans as $p) {
            $nama = $p->tagihan->pesertaDidik->orang->nama_lengkap ?? 'Seseorang';
            $activities[] = [
                'type' => 'pembayaran',
                'title' => 'Pembayaran SPP <span class="font-semibold">' . htmlspecialchars($nama) . '</span> — Rp ' . number_format($p->jumlah, 0, ',', '.'),
                'time' => $p->created_at,
                'icon' => 'wallet',
                'bg_color' => 'bg-accent-100',
                'icon_color' => 'text-accent-700'
            ];
        }

        $pelanggarans = CatatanPelanggaran::with('pesertaDidik.orang')->orderBy('created_at', 'desc')->limit(3)->get();
        foreach($pelanggarans as $p) {
            $nama = $p->pesertaDidik->orang->nama_lengkap ?? 'Seseorang';
            $activities[] = [
                'type' => 'pelanggaran',
                'title' => 'Pelanggaran dicatat untuk <span class="font-semibold">' . htmlspecialchars($nama) . '</span>',
                'time' => $p->created_at,
                'icon' => 'shield-alert',
                'bg_color' => 'bg-danger-50',
                'icon_color' => 'text-danger-500'
            ];
        }

        $perizinans = PerizinanKeluar::with('pesertaDidik.orang')->orderBy('created_at', 'desc')->limit(3)->get();
        foreach($perizinans as $p) {
            $nama = $p->pesertaDidik->orang->nama_lengkap ?? 'Seseorang';
            $activities[] = [
                'type' => 'perizinan',
                'title' => 'Izin keluar <span class="font-semibold">' . htmlspecialchars($nama) . '</span> disetujui',
                'time' => $p->created_at,
                'icon' => 'door-open',
                'bg_color' => 'bg-success-50',
                'icon_color' => 'text-success-700'
            ];
        }

        usort($activities, function($a, $b) {
            return $b['time'] <=> $a['time'];
        });

        $recentActivities = array_slice($activities, 0, 5);

        return view('admin.dashboard', compact(
            'tahunAktif',
            'totalSantri',
            'totalPegawai',
            'totalRombel',
            'totalPendaftar',
            'labelsLembaga',
            'dataPutra',
            'dataPutri',
            'labelsAsrama',
            'dataAsrama',
            'rekapTagihan',
            'recentActivities',
            'dalamMasaIzinCount',
            'telatBelumKembaliCount'
        ));
    }
}
