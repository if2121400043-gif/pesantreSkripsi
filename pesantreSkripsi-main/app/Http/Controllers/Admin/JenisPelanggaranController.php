<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisPelanggaran;
use App\Models\Pesantren;
use Illuminate\Http\Request;

class JenisPelanggaranController extends Controller
{
    public function index()
    {
        $jenisPelanggarans = JenisPelanggaran::with('pesantren')->orderBy('kategori')->orderBy('nama')->get();
        $pesantrens = Pesantren::all();

        return view('admin.kedisiplinan.jenis_pelanggaran.index', compact('jenisPelanggarans', 'pesantrens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pesantren_id' => 'required|exists:pesantren,id',
            'nama' => 'required|string|max:150',
            'kategori' => 'required|in:RINGAN,SEDANG,BERAT',
            'poin' => 'required|integer|min:0',
        ]);

        JenisPelanggaran::create($validated);

        return back()->with('success', 'Master Pelanggaran berhasil ditambahkan.');
    }

    public function update(Request $request, JenisPelanggaran $jenisPelanggaran)
    {
        $validated = $request->validate([
            'pesantren_id' => 'required|exists:pesantren,id',
            'nama' => 'required|string|max:150',
            'kategori' => 'required|in:RINGAN,SEDANG,BERAT',
            'poin' => 'required|integer|min:0',
        ]);

        $jenisPelanggaran->update($validated);

        return back()->with('success', 'Master Pelanggaran berhasil diperbarui.');
    }

    public function destroy(JenisPelanggaran $jenisPelanggaran)
    {
        // For production, check if used in catatan_pelanggaran before delete
        $jenisPelanggaran->delete();
        return back()->with('success', 'Master Pelanggaran berhasil dihapus.');
    }
}
