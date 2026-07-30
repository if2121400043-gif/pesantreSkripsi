<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rombel;
use App\Models\Lembaga;
use App\Models\TahunPelajaran;
use App\Models\Pegawai;
use Illuminate\Http\Request;

class RombelController extends Controller
{
    public function index(Request $request)
    {
        $query = Rombel::with(['lembaga', 'tahunPelajaran', 'waliKelas.orang'])->withCount('riwayatPeserta');

        if ($request->filled('lembaga_id')) {
            $query->where('lembaga_id', $request->lembaga_id);
        }
        
        if ($request->filled('tahun_pelajaran_id')) {
            $query->where('tahun_pelajaran_id', $request->tahun_pelajaran_id);
        } else {
            // Default to active academic year
            $activeTahun = TahunPelajaran::where('is_active', true)->first();
            if ($activeTahun) {
                $query->where('tahun_pelajaran_id', $activeTahun->id);
            }
        }

        $rombels = $query->orderBy('tingkat')->orderBy('nama')->paginate(15)->withQueryString();
        
        $lembagas = Lembaga::where('is_active', true)->orderBy('urutan')->get();
        $tahuns = TahunPelajaran::orderBy('tanggal_mulai', 'desc')->get();

        return view('admin.rombel.index', compact('rombels', 'lembagas', 'tahuns'));
    }

    public function create()
    {
        $lembagas = Lembaga::where('is_active', true)->orderBy('urutan')->get();
        $tahuns = TahunPelajaran::orderBy('tanggal_mulai', 'desc')->get();
        $pegawais = Pegawai::with('orang')->where('is_active', true)->get();

        return view('admin.rombel.create', compact('lembagas', 'tahuns', 'pegawais'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lembaga_id' => 'required|exists:lembaga,id',
            'tahun_pelajaran_id' => 'required|exists:tahun_pelajaran,id',
            'tingkat' => 'nullable|string|max:10',
            'nama' => 'required|string|max:50',
            'wali_kelas_id' => 'nullable|exists:pegawai,id',
            'kapasitas' => 'required|integer|min:1',
        ]);

        // Check if combination already exists
        $exists = Rombel::where('lembaga_id', $validated['lembaga_id'])
                        ->where('tahun_pelajaran_id', $validated['tahun_pelajaran_id'])
                        ->where('nama', $validated['nama'])
                        ->exists();
        
        if ($exists) {
            return back()->withInput()->with('error', 'Rombongan belajar (kelas) ini sudah terdaftar pada lembaga dan tahun pelajaran yang sama.');
        }

        Rombel::create($validated);

        return redirect()->route('admin.rombel.index')->with('success', 'Rombongan Belajar berhasil ditambahkan.');
    }

    public function show(Rombel $rombel)
    {
        $rombel->load(['lembaga', 'tahunPelajaran', 'waliKelas.orang', 'riwayatPeserta.pesertaDidik.orang']);
        return view('admin.rombel.show', compact('rombel'));
    }

    public function edit(Rombel $rombel)
    {
        $lembagas = Lembaga::where('is_active', true)->orderBy('urutan')->get();
        $tahuns = TahunPelajaran::orderBy('tanggal_mulai', 'desc')->get();
        $pegawais = Pegawai::with('orang')->where('is_active', true)->get();

        return view('admin.rombel.edit', compact('rombel', 'lembagas', 'tahuns', 'pegawais'));
    }

    public function update(Request $request, Rombel $rombel)
    {
        $validated = $request->validate([
            'lembaga_id' => 'required|exists:lembaga,id',
            'tahun_pelajaran_id' => 'required|exists:tahun_pelajaran,id',
            'tingkat' => 'nullable|string|max:10',
            'nama' => 'required|string|max:50',
            'wali_kelas_id' => 'nullable|exists:pegawai,id',
            'kapasitas' => 'required|integer|min:1',
        ]);

        $rombel->update($validated);

        return redirect()->route('admin.rombel.index')->with('success', 'Data Rombongan Belajar berhasil diperbarui.');
    }

    public function destroy(Rombel $rombel)
    {
        if ($rombel->riwayatPeserta()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus Rombel yang masih memiliki peserta didik.');
        }

        $rombel->delete();
        return redirect()->route('admin.rombel.index')->with('success', 'Rombongan Belajar berhasil dihapus.');
    }
}
