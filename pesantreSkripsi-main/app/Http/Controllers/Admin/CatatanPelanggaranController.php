<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatatanPelanggaran;
use App\Models\JenisPelanggaran;
use App\Models\PesertaDidik;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatatanPelanggaranController extends Controller
{
    public function index(Request $request)
    {
        $query = CatatanPelanggaran::with(['pesertaDidik.orang', 'jenisPelanggaran', 'tahunPelajaran', 'pencatat']);

        if ($request->filled('tahun_pelajaran_id')) {
            $query->where('tahun_pelajaran_id', $request->tahun_pelajaran_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pesertaDidik.orang', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('niup', 'like', "%{$search}%");
            });
        }

        $pelanggarans = $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        $tahuns = TahunPelajaran::orderBy('tanggal_mulai', 'desc')->get();
        $jenisList = JenisPelanggaran::orderBy('nama')->get();

        return view('admin.kedisiplinan.pelanggaran.index', compact('pelanggarans', 'tahuns', 'jenisList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'peserta_didik_id' => 'required|exists:peserta_didik,id',
            'jenis_pelanggaran_id' => 'required|exists:jenis_pelanggaran,id',
            'tahun_pelajaran_id' => 'required|exists:tahun_pelajaran,id',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
            'tindakan' => 'nullable|string|max:200',
        ]);

        $validated['dicatat_oleh'] = Auth::id();

        CatatanPelanggaran::create($validated);

        return back()->with('success', 'Catatan pelanggaran santri berhasil ditambahkan.');
    }

    public function destroy(CatatanPelanggaran $pelanggaran)
    {
        $pelanggaran->delete();
        return back()->with('success', 'Catatan pelanggaran berhasil dihapus.');
    }
    
    // API endpoint for searching student by NIUP/Name in the modal
    public function searchSantri(Request $request)
    {
        $search = $request->get('q');
        
        if (strlen($search) < 3) return response()->json([]);
        
        $santris = PesertaDidik::with(['orang', 'riwayatRombel.rombel'])
            ->where('status', 'AKTIF')
            ->whereHas('orang', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('niup', 'like', "%{$search}%");
            })
            ->take(10)
            ->get()
            ->map(function($santri) {
                $kelas = $santri->riwayatRombel->first() ? $santri->riwayatRombel->first()->rombel->nama : 'Belum ada kelas';
                return [
                    'id' => $santri->id,
                    'text' => $santri->orang->nama_lengkap . ' (' . $santri->orang->niup . ') - ' . $kelas
                ];
            });
            
        return response()->json($santris);
    }
}
