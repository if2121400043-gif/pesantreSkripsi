<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PresensiKelas;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Models\RiwayatRombelPeserta;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $rombels = Rombel::with('lembaga')->where('tahun_pelajaran_id', $tahunAktif?->id)->orderBy('nama')->get();
        
        $selectedRombel = null;
        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $peserta = collect();
        $presensiHariIni = collect();

        if ($request->filled('rombel_id')) {
            $selectedRombel = Rombel::find($request->rombel_id);
            
            // Get all active students in this rombel
            $riwayat = RiwayatRombelPeserta::with(['pesertaDidik.orang'])
                ->where('rombel_id', $request->rombel_id)
                ->where('status', 'AKTIF')
                ->get();
                
            $peserta = $riwayat->pluck('pesertaDidik')->sortBy(function($peserta) {
                return $peserta->orang->nama;
            });
            
            // Get existing presensi for this date
            $presensiHariIni = PresensiKelas::where('rombel_id', $request->rombel_id)
                ->whereDate('tanggal', $tanggal)
                ->get()
                ->keyBy('peserta_didik_id');
        }

        return view('admin.akademik.presensi.index', compact('rombels', 'selectedRombel', 'tanggal', 'peserta', 'presensiHariIni', 'tahunAktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rombel_id' => 'required|exists:rombel,id',
            'tanggal' => 'required|date',
            'presensi' => 'required|array',
            'presensi.*.status' => 'required|in:HADIR,SAKIT,IZIN,ALPHA',
            'presensi.*.keterangan' => 'nullable|string'
        ]);

        $rombelId = $request->rombel_id;
        $tanggal = $request->tanggal;
        $dicatatOleh = auth()->id() ?? 1;

        foreach ($request->presensi as $pesertaId => $data) {
            PresensiKelas::updateOrCreate(
                [
                    'peserta_didik_id' => $pesertaId,
                    'rombel_id' => $rombelId,
                    'tanggal' => $tanggal
                ],
                [
                    'status' => $data['status'],
                    'keterangan' => $data['keterangan'] ?? null,
                    'dicatat_oleh' => $dicatatOleh
                ]
            );
        }

        return redirect()->route('admin.presensi.index', [
            'rombel_id' => $rombelId, 
            'tanggal' => $tanggal
        ])->with('success', 'Data presensi berhasil disimpan.');
    }
}
