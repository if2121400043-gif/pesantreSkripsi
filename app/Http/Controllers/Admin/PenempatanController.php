<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rombel;
use App\Models\Lembaga;
use App\Models\TahunPelajaran;
use App\Models\PesertaDidik;
use App\Models\RiwayatRombelPeserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenempatanController extends Controller
{
    public function index(Request $request)
    {
        $lembagaId = $request->get('lembaga_id');
        $tahunId = $request->get('tahun_pelajaran_id');
        $tingkat = $request->get('tingkat');

        $lembagas = Lembaga::where('is_active', true)->orderBy('urutan')->get();
        $tahuns = TahunPelajaran::orderBy('tanggal_mulai', 'desc')->get();
        
        $rombels = [];
        $pesertaBelumDitempatkan = [];

        if ($lembagaId && $tahunId) {
            // Get classes for this lembaga and year
            $queryRombel = Rombel::withCount('riwayatPeserta')
                ->where('lembaga_id', $lembagaId)
                ->where('tahun_pelajaran_id', $tahunId);
                
            if ($tingkat) {
                $queryRombel->where('tingkat', $tingkat);
            }
            
            $rombels = $queryRombel->orderBy('tingkat')->orderBy('nama')->get();

            // Get students who are NOT in any active rombel FOR THIS SPECIFIC LEMBAGA in this tahun_pelajaran AND are AKTIF
            $pesertaBelumDitempatkanQuery = PesertaDidik::with('orang')
                ->where('status', 'AKTIF')
                ->whereDoesntHave('riwayatRombel', function ($q) use ($tahunId, $lembagaId) {
                    $q->where('tahun_pelajaran_id', $tahunId)
                      ->where('status', 'AKTIF')
                      ->whereHas('rombel', function($rq) use ($lembagaId) {
                          $rq->where('lembaga_id', $lembagaId);
                      });
                });
                
            if ($request->filled('search')) {
                $search = $request->search;
                $pesertaBelumDitempatkanQuery->whereHas('orang', function($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('niup', 'like', "%{$search}%");
                });
            }

            $pesertaBelumDitempatkan = $pesertaBelumDitempatkanQuery->get();
        }

        return view('admin.penempatan.index', compact(
            'lembagas', 'tahuns', 'rombels', 'pesertaBelumDitempatkan', 
            'lembagaId', 'tahunId', 'tingkat'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rombel_id' => 'required|exists:rombel,id',
            'peserta_ids' => 'required|array|min:1',
            'peserta_ids.*' => 'exists:peserta_didik,id'
        ]);

        $rombel = Rombel::findOrFail($request->rombel_id);
        
        DB::beginTransaction();
        try {
            foreach ($request->peserta_ids as $pesertaId) {
                // Only deactivate previous active rombel IN THE SAME LEMBAGA for this academic year
                RiwayatRombelPeserta::where('peserta_didik_id', $pesertaId)
                    ->where('tahun_pelajaran_id', $rombel->tahun_pelajaran_id)
                    ->whereHas('rombel', function ($q) use ($rombel) {
                        $q->where('lembaga_id', $rombel->lembaga_id);
                    })
                    ->update([
                        'status' => 'PINDAH',
                        'tanggal_keluar' => now()
                    ]);

                // Create new placement
                RiwayatRombelPeserta::create([
                    'peserta_didik_id' => $pesertaId,
                    'rombel_id' => $rombel->id,
                    'tahun_pelajaran_id' => $rombel->tahun_pelajaran_id,
                    'tanggal_masuk' => now(),
                    'status' => 'AKTIF'
                ]);
            }
            DB::commit();
            return back()->with('success', count($request->peserta_ids) . ' Santri berhasil ditempatkan di ' . $rombel->nama);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses penempatan.');
        }
    }

    public function destroyRombelPeserta(Request $request)
    {
        $request->validate([
            'riwayat_id' => 'required|exists:riwayat_rombel_peserta,id'
        ]);

        $riwayat = RiwayatRombelPeserta::findOrFail($request->riwayat_id);
        
        // Soft delete/update status rather than actual delete to keep history
        if ($request->has('hard_delete')) {
            $riwayat->delete();
            $msg = 'Santri berhasil dihapus dari kelas (permanen).';
        } else {
            $riwayat->update([
                'status' => 'PINDAH',
                'tanggal_keluar' => now()
            ]);
            $msg = 'Status santri di kelas ini telah dinonaktifkan (dikeluarkan).';
        }

        return back()->with('success', $msg);
    }
}
