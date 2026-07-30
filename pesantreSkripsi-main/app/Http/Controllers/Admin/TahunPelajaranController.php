<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunPelajaran;
use App\Models\Pesantren;
use Illuminate\Http\Request;

class TahunPelajaranController extends Controller
{
    public function index()
    {
        $pesantren = $this->getPesantren();
        $tahunPelajaran = TahunPelajaran::orderBy('tanggal_mulai', 'desc')->get();
        
        return view('admin.tahun_pelajaran.index', compact('tahunPelajaran', 'pesantren'));
    }

    public function store(Request $request)
    {
        $pesantren = $this->getPesantren();
        
        $validated = $request->validate([
            'nama' => 'required|string|max:20',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        ]);

        $validated['pesantren_id'] = $pesantren->id;
        
        // If this one is set as active, deactivate all others
        $isActive = $request->has('is_active');
        if ($isActive) {
            TahunPelajaran::query()->update(['is_active' => false]);
        }
        $validated['is_active'] = $isActive;

        TahunPelajaran::create($validated);

        return redirect()->route('admin.tahun-pelajaran.index')->with('success', 'Tahun Pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, TahunPelajaran $tahunPelajaran)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:20',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        ]);

        $isActive = $request->has('is_active');
        if ($isActive && !$tahunPelajaran->is_active) {
            TahunPelajaran::query()->update(['is_active' => false]);
        }
        $validated['is_active'] = $isActive;

        $tahunPelajaran->update($validated);

        return redirect()->route('admin.tahun-pelajaran.index')->with('success', 'Tahun Pelajaran berhasil diperbarui.');
    }

    public function destroy(TahunPelajaran $tahunPelajaran)
    {
        $tahunPelajaran->delete();
        return redirect()->route('admin.tahun-pelajaran.index')->with('success', 'Tahun Pelajaran berhasil dihapus.');
    }
}
