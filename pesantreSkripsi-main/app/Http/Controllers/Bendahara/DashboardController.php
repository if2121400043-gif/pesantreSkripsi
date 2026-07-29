<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\TahunPelajaran;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $namaBulan = Carbon::now()->locale('id')->translatedFormat('F Y');

        // Statistik Total Keuangan
        $totalPemasukan = Pembayaran::sum('jumlah');
        
        // Menghitung total sisa tagihan dari seluruh tagihan yang aktif
        $totalTagihan = Tagihan::sum('total');
        $totalTerbayar = Pembayaran::sum('jumlah');
        $totalTunggakan = max(0, $totalTagihan - $totalTerbayar);

        // Keuangan Bulan Ini
        $tagihanBulanIni = Tagihan::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->get();
        
        $tagihanBulanIniIds = $tagihanBulanIni->pluck('id');

        $totalTagihanBulanIni = $tagihanBulanIni->sum('total');
        $pemasukanBulanIni = Pembayaran::whereIn('tagihan_id', $tagihanBulanIniIds)->sum('jumlah');
        $tunggakanBulanIni = max(0, $totalTagihanBulanIni - $pemasukanBulanIni);
        
        $persenLunasBulanIni = $totalTagihanBulanIni > 0 ? round(($pemasukanBulanIni / $totalTagihanBulanIni) * 100) : 0;
        
        $rekapBulanIni = [
            'bulan' => $namaBulan,
            'total' => $totalTagihanBulanIni,
            'pemasukan' => $pemasukanBulanIni,
            'tunggakan' => $tunggakanBulanIni,
            'persenLunas' => $persenLunasBulanIni
        ];

        // Transaksi Pembayaran Terbaru
        $pembayaranTerbaru = Pembayaran::with(['tagihan.pesertaDidik.orang', 'tagihan.komponenBiaya'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Tagihan Belum Lunas Jatuh Tempo Terdekat
        $tunggakanJatuhTempo = Tagihan::with(['pesertaDidik.orang', 'komponenBiaya'])
            ->whereIn('status', ['BELUM_BAYAR', 'SEBAGIAN'])
            ->orderBy('jatuh_tempo', 'asc')
            ->limit(5)
            ->get();

        return view('bendahara.dashboard', compact(
            'tahunAktif',
            'totalPemasukan',
            'totalTunggakan',
            'rekapBulanIni',
            'pembayaranTerbaru',
            'tunggakanJatuhTempo'
        ));
    }
}
