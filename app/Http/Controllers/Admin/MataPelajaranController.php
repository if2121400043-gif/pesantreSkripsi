<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use App\Models\Lembaga;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $query = MataPelajaran::with('lembaga');

        if ($request->filled('lembaga_id')) {
            $query->where('lembaga_id', $request->lembaga_id);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('kode', 'like', "%{$request->search}%");
            });
        }

        $mapels = $query->orderBy('lembaga_id')->orderBy('tingkat')->orderBy('nama')->paginate(15)->withQueryString();
        $lembagas = Lembaga::where('is_active', true)->orderBy('urutan')->get();

        return view('admin.mata_pelajaran.index', compact('mapels', 'lembagas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lembaga_id' => 'required|exists:lembaga,id',
            'kode_mapel' => 'nullable|string|max:20',
            'nama_mapel' => 'required|string|max:100',
            'kelompok_mapel' => 'nullable|string|max:50',
        ]);

        $kodeMapel = $validated['kode_mapel'];
        if (empty($kodeMapel)) {
            $kodeMapel = \App\Helpers\CodeGenerator::generate($validated['nama_mapel'], 'mata_pelajaran', 'kode');
        } else {
            $kodeMapel = \App\Helpers\CodeGenerator::generate($kodeMapel, 'mata_pelajaran', 'kode');
        }

        MataPelajaran::create([
            'lembaga_id' => $validated['lembaga_id'],
            'kode' => $kodeMapel,
            'nama' => $validated['nama_mapel'],
            'tingkat' => $validated['kelompok_mapel'],
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'Mata Pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        $validated = $request->validate([
            'lembaga_id' => 'required|exists:lembaga,id',
            'kode_mapel' => 'nullable|string|max:20',
            'nama_mapel' => 'required|string|max:100',
            'kelompok_mapel' => 'nullable|string|max:50',
        ]);

        $kodeMapel = $validated['kode_mapel'];
        if (empty($kodeMapel)) {
            $kodeMapel = \App\Helpers\CodeGenerator::generate($validated['nama_mapel'], 'mata_pelajaran', 'kode', $mataPelajaran->id);
        } else {
            $kodeMapel = \App\Helpers\CodeGenerator::generate($kodeMapel, 'mata_pelajaran', 'kode', $mataPelajaran->id);
        }

        $mataPelajaran->update([
            'lembaga_id' => $validated['lembaga_id'],
            'kode' => $kodeMapel,
            'nama' => $validated['nama_mapel'],
            'tingkat' => $validated['kelompok_mapel'],
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'Mata Pelajaran berhasil diperbarui.');
    }

    public function destroy(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->delete();
        return back()->with('success', 'Mata Pelajaran berhasil dihapus.');
    }
}
