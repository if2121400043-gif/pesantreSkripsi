<?php

namespace App\Http\Controllers\Bendahara;

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

        return view('bendahara.komponen_biaya.index', compact('komponenBiayas', 'pesantrens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'jenis' => 'required|in:BULANAN,TAHUNAN,SEKALI',
            'nominal' => 'required|numeric|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $pesantren = Pesantren::first();
        if ($pesantren) {
            $validated['pesantren_id'] = $pesantren->id;
        }

        KomponenBiaya::create($validated);

        return back()->with('success', 'Komponen Biaya berhasil ditambahkan.');
    }

    public function update(Request $request, KomponenBiaya $komponenBiaya)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'jenis' => 'required|in:BULANAN,TAHUNAN,SEKALI',
            'nominal' => 'required|numeric|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $pesantren = Pesantren::first();
        if ($pesantren) {
            $validated['pesantren_id'] = $pesantren->id;
        }

        $komponenBiaya->update($validated);

        return back()->with('success', 'Komponen Biaya berhasil diperbarui.');
    }

    public function destroy(KomponenBiaya $komponenBiaya)
    {
        $komponenBiaya->delete();
        
        return back()->with('success', 'Komponen Biaya berhasil dihapus.');
    }
}
