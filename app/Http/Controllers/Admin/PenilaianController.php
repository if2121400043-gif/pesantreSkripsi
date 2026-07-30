<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rombel;
use App\Models\MataPelajaran;
use App\Models\TahunPelajaran;
use App\Models\NilaiRapor;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    public function index(Request $request)
    {
        $tahunId = $request->get('tahun_pelajaran_id');
        $rombelId = $request->get('rombel_id');
        $mapelId = $request->get('mata_pelajaran_id');
        $semester = $request->get('semester', 'GANJIL');

        $tahuns = TahunPelajaran::orderBy('tanggal_mulai', 'desc')->get();
        $rombels = [];
        $mapels = [];
        $pesertaList = [];
        $nilaiMap = [];

        if ($tahunId) {
            $rombels = Rombel::where('tahun_pelajaran_id', $tahunId)->orderBy('nama')->get();
        }

        if ($rombelId) {
            $rombel = Rombel::find($rombelId);
            if ($rombel) {
                $mapels = MataPelajaran::where('lembaga_id', $rombel->lembaga_id)->where('is_active', true)->orderBy('nama')->get();
                
                if ($mapelId) {
                    $pesertaList = $rombel->riwayatPeserta()->with('pesertaDidik.orang')->where('status', 'AKTIF')->get();
                    
                    // Get existing grades
                    $nilais = NilaiRapor::where('rombel_id', $rombelId)
                        ->where('mata_pelajaran_id', $mapelId)
                        ->where('tahun_pelajaran_id', $tahunId)
                        ->where('semester', $semester)
                        ->get();
                        
                    foreach ($nilais as $n) {
                        $nilaiMap[$n->peserta_didik_id] = $n;
                    }
                }
            }
        }

        return view('admin.penilaian.index', compact(
            'tahuns', 'rombels', 'mapels', 'pesertaList', 'nilaiMap',
            'tahunId', 'rombelId', 'mapelId', 'semester'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_pelajaran_id' => 'required|exists:tahun_pelajaran,id',
            'rombel_id' => 'required|exists:rombel,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'semester' => 'required|in:GANJIL,GENAP',
            'nilai' => 'required|array',
            'nilai.*.peserta_didik_id' => 'required|exists:peserta_didik,id',
            'nilai.*.nilai_akhir' => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($request->nilai as $n) {
            if (isset($n['nilai_akhir']) && $n['nilai_akhir'] !== '') {
                NilaiRapor::updateOrCreate(
                    [
                        'peserta_didik_id' => $n['peserta_didik_id'],
                        'rombel_id' => $request->rombel_id,
                        'mata_pelajaran_id' => $request->mata_pelajaran_id,
                        'tahun_pelajaran_id' => $request->tahun_pelajaran_id,
                        'semester' => $request->semester,
                    ],
                    [
                        'nilai_tugas' => $n['nilai_tugas'] ?? null,
                        'nilai_uts' => $n['nilai_uts'] ?? null,
                        'nilai_uas' => $n['nilai_uas'] ?? null,
                        'nilai_akhir' => $n['nilai_akhir'],
                        'predikat' => $this->calculatePredikat($n['nilai_akhir']),
                        'catatan_guru' => $n['catatan_guru'] ?? null,
                    ]
                );
            }
        }

        return back()->with('success', 'Data Penilaian berhasil disimpan.');
    }

    private function calculatePredikat($nilai)
    {
        if ($nilai >= 90) return 'A';
        if ($nilai >= 80) return 'B';
        if ($nilai >= 70) return 'C';
        if ($nilai >= 60) return 'D';
        return 'E';
    }
}
