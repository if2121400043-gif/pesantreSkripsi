<?php

namespace App\Http\Controllers\PanitiaPsb;

use App\Http\Controllers\Controller;
use App\Models\CalonSantri;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        // Statistik pendaftaran
        $totalPendaftar = CalonSantri::count();
        $baruMasuk = CalonSantri::where('status', 'BARU_MASUK')->count();
        $hadirTes = CalonSantri::where('status', 'HADIR_TES')->count();
        $diterima = CalonSantri::where('status', 'DITERIMA')->count();
        $tidakLulus = CalonSantri::where('status', 'TIDAK_LULUS')->count();
        $dibatalkan = CalonSantri::where('status', 'DIBATALKAN')->count();

        // Pendaftar terbaru
        $pendaftarTerbaru = CalonSantri::with(['lembagaTujuan'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('panitia-psb.dashboard', compact(
            'tahunAktif',
            'totalPendaftar',
            'baruMasuk',
            'hadirTes',
            'diterima',
            'tidakLulus',
            'dibatalkan',
            'pendaftarTerbaru'
        ));
    }
}
