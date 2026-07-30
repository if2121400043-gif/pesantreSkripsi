<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesantren;
use App\Models\Desa;
use Illuminate\Http\Request;

class KonfigurasiController extends Controller
{
    public function index()
    {
        $pesantren = Pesantren::with(['desa.kecamatan.kabupaten.provinsi', 'lembaga'])->first();
        $provinsis = \App\Models\Provinsi::orderBy('nama', 'asc')->get();

        return view('admin.pengaturan.konfigurasi.index', compact('pesantren', 'provinsis'));
    }

    public function update(Request $request)
    {
        $pesantren = Pesantren::firstOrFail();

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nspp' => 'nullable|string|max:50',
            'alamat' => 'nullable|string|max:500',
            'kode_pos' => 'nullable|string|max:10',
            'telepon' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|string|max:150',
            'nama_pimpinan' => 'nullable|string|max:200',
            'tahun_berdiri' => 'nullable|integer|min:1900|max:' . date('Y'),
            'visi' => 'nullable|string',
            'misi' => 'nullable|string',
            'sejarah' => 'nullable|string',
            'desa_id' => 'nullable|exists:desa,id',
        ]);

        $pesantren->update($validated);

        return back()->with('success', 'Profil pesantren berhasil diperbarui.');
    }
}
