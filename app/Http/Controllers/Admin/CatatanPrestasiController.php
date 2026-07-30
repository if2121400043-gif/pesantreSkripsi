<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatatanPrestasi;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;

class CatatanPrestasiController extends Controller
{
    public function index(Request $request)
    {
        $query = CatatanPrestasi::with(['pesertaDidik.orang', 'tahunPelajaran']);

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

        $prestasis = $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $tahuns = TahunPelajaran::orderBy('tanggal_mulai', 'desc')->get();

        return view('admin.kedisiplinan.prestasi.index', compact('prestasis', 'tahuns'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'peserta_didik_id' => 'required|exists:peserta_didik,id',
            'tahun_pelajaran_id' => 'required|exists:tahun_pelajaran,id',
            'judul' => 'required|string|max:200',
            'tingkat' => 'required|in:INTERNAL,KECAMATAN,KABUPATEN,PROVINSI,NASIONAL,INTERNASIONAL',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        CatatanPrestasi::create($validated);

        return back()->with('success', 'Catatan prestasi santri berhasil ditambahkan.');
    }

    public function destroy(CatatanPrestasi $prestasi)
    {
        $prestasi->delete();
        return back()->with('success', 'Catatan prestasi berhasil dihapus.');
    }
}
