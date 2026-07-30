<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lembaga;
use App\Models\Pesantren;
use Illuminate\Http\Request;

class LembagaController extends Controller
{
    public function index()
    {
        // For simplicity, we assume there is only 1 Pesantren profile
        $pesantren = Pesantren::first();
        $lembagas = Lembaga::with('pesantren')->orderBy('urutan')->get();
        
        return view('admin.lembaga.index', compact('lembagas', 'pesantren'));
    }

    public function store(Request $request)
    {
        $pesantren = Pesantren::first();
        if (!$pesantren) {
            return back()->with('error', 'Profil pesantren belum diatur.');
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:200',
            'singkatan' => 'nullable|string|max:30',
            'jenjang' => 'required|in:PAUD,SD,SMP,SMA,MADIN,TAHFIDZ,PERGURUAN_TINGGI,NON_FORMAL,LAINNYA',
            'tipe' => 'required|in:FORMAL,NON_FORMAL,PONDOK',
            'npsn' => 'nullable|string|max:20',
        ]);

        $validated['pesantren_id'] = $pesantren->id;
        $validated['urutan'] = Lembaga::max('urutan') + 1;
        $validated['is_active'] = $request->has('is_active');

        Lembaga::create($validated);

        return redirect()->route('admin.lembaga.index')->with('success', 'Lembaga berhasil ditambahkan.');
    }

    public function update(Request $request, Lembaga $lembaga)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:200',
            'singkatan' => 'nullable|string|max:30',
            'jenjang' => 'required|in:PAUD,SD,SMP,SMA,MADIN,TAHFIDZ,PERGURUAN_TINGGI,NON_FORMAL,LAINNYA',
            'tipe' => 'required|in:FORMAL,NON_FORMAL,PONDOK',
            'npsn' => 'nullable|string|max:20',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $lembaga->update($validated);

        return redirect()->route('admin.lembaga.index')->with('success', 'Lembaga berhasil diperbarui.');
    }

    public function destroy(Lembaga $lembaga)
    {
        $lembaga->delete();
        return redirect()->route('admin.lembaga.index')->with('success', 'Lembaga berhasil dihapus.');
    }
}
