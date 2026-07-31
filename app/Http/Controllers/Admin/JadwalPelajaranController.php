<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\Rombel;
use App\Models\MataPelajaran;
use App\Models\Pegawai;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;

class JadwalPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $tahuns = TahunPelajaran::orderBy('tanggal_mulai', 'desc')->get();
        $tahunId = $request->get('tahun_pelajaran_id');

        // Default ke tahun aktif jika tidak dipilih
        if (!$tahunId) {
            $tahunAktif = TahunPelajaran::where('is_active', true)->first();
            $tahunId = $tahunAktif?->id;
        }

        $rombels = Rombel::with('lembaga', 'tahunPelajaran')
            ->when($tahunId, fn($q) => $q->where('tahun_pelajaran_id', $tahunId))
            ->orderBy('nama')
            ->get();

        $rombelId = $request->get('rombel_id');
        $jadwals = [];
        $mapels = [];
        $gurus = [];

        if ($rombelId) {
            $rombel = Rombel::find($rombelId);
            if ($rombel) {
                // Get jadwals for this rombel
                $jadwalsQuery = JadwalPelajaran::with(['mataPelajaran', 'guru.orang'])
                    ->where('rombel_id', $rombelId)
                    ->orderBy('hari')
                    ->orderBy('jam_mulai')
                    ->get();
                    
                // Group by hari
                $jadwals = $jadwalsQuery->groupBy('hari');
                
                $mapels = MataPelajaran::where('lembaga_id', $rombel->lembaga_id)->where('is_active', true)->orderBy('nama')->get();
                $gurus = Pegawai::with('orang')->where('is_active', true)->whereIn('jenis_pegawai', ['GURU', 'USTADZ', 'PENGASUH'])->get();
            }
        }

        return view('admin.jadwal_pelajaran.index', compact('rombels', 'jadwals', 'mapels', 'gurus', 'rombelId', 'tahuns', 'tahunId'));
    }

    public function create(Request $request)
    {
        $rombelId = $request->get('rombel_id');
        $tahunId = $request->get('tahun_pelajaran_id');

        $rombel = null;
        $mapels = collect();
        $gurus = collect();

        if ($rombelId) {
            $rombel = Rombel::with('lembaga', 'tahunPelajaran')->find($rombelId);
            if ($rombel) {
                $mapels = MataPelajaran::where('lembaga_id', $rombel->lembaga_id)->where('is_active', true)->orderBy('nama')->get();
            }
        }

        $gurus = Pegawai::with('orang')->where('is_active', true)->whereIn('jenis_pegawai', ['GURU', 'USTADZ', 'PENGASUH'])->get();
        $rombels = Rombel::with('lembaga')->orderBy('nama')->get();

        return view('admin.jadwal_pelajaran.create', compact('rombel', 'rombels', 'mapels', 'gurus', 'rombelId', 'tahunId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rombel_id' => 'required|exists:rombel,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'pegawai_id' => 'nullable|exists:pegawai,id',
            'hari' => 'required|in:SENIN,SELASA,RABU,KAMIS,JUMAT,SABTU,AHAD',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        JadwalPelajaran::create($validated);

        $rombel = Rombel::find($validated['rombel_id']);

        return redirect()->route('admin.jadwal-pelajaran.index', [
            'rombel_id' => $validated['rombel_id'],
            'tahun_pelajaran_id' => $rombel?->tahun_pelajaran_id
        ])->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
    }

    public function destroy(JadwalPelajaran $jadwal)
    {
        $jadwal->delete();
        return back()->with('success', 'Jadwal pelajaran berhasil dihapus.');
    }
}
