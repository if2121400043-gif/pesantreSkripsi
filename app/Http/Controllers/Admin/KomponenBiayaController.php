<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KomponenBiaya;
use App\Models\Pesantren;
use Illuminate\Http\Request;

class KomponenBiayaController extends Controller
{
    public function index()
    {
        $komponenBiayas = KomponenBiaya::with('pesantren')->orderBy('pesantren_id')->orderBy('jenis')->orderBy('nama')->get();
        $pesantrens = Pesantren::all();

        return view('admin.komponen_biaya.index', compact('komponenBiayas', 'pesantrens'));
    }

    public function store(Request $request)
    {
        // 1. pesantren_id dihapus dari validasi request karena akan di-generate otomatis
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'jenis' => 'required|in:BULANAN,TAHUNAN,SEKALI',
            'nominal' => 'required|numeric|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // 2. Injeksi otomatis pesantren_id
        $pesantren = Pesantren::first();
        if ($pesantren) {
            $validated['pesantren_id'] = $pesantren->id;
        }

        KomponenBiaya::create($validated);

        return back()->with('success', 'Komponen Biaya berhasil ditambahkan.');
    }

    public function update(Request $request, KomponenBiaya $komponenBiaya)
    {
        // 1. Sama seperti store, hapus pesantren_id dari aturan validasi
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'jenis' => 'required|in:BULANAN,TAHUNAN,SEKALI',
            'nominal' => 'required|numeric|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // 2. Injeksi otomatis pesantren_id agar tidak kosong (null) saat di-update
        $pesantren = Pesantren::first();
        if ($pesantren) {
            $validated['pesantren_id'] = $pesantren->id;
        }

        $komponenBiaya->update($validated);

        return back()->with('success', 'Komponen Biaya berhasil diperbarui.');
    }

    public function destroy(KomponenBiaya $komponenBiaya)
    {
        // Add check if tagihan exists for this komponen, prevent deletion if true.
        if ($komponenBiaya->pesantren_id) { // Dummy check for now, later add $komponenBiaya->tagihans()->exists()
           // For robust apps, soft deletes are better, but we follow standard cascade for now
        }
        $komponenBiaya->delete();
        
        return back()->with('success', 'Komponen Biaya berhasil dihapus.');
    }
}