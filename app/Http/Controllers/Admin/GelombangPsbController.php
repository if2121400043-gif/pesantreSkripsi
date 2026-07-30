<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GelombangPsb;
use App\Models\Pesantren;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;

class GelombangPsbController extends Controller
{
    public function index(Request $request)
    {
        $query = GelombangPsb::with(['pesantren', 'tahunPelajaran'])->withCount('calonSantri');
        
        if ($request->filled('tahun_pelajaran_id')) {
            $query->where('tahun_pelajaran_id', $request->tahun_pelajaran_id);
        }

        $gelombangs = $query->orderBy('tanggal_buka', 'desc')->get();
        $pesantrens = Pesantren::all();
        $tahuns = TahunPelajaran::orderBy('tanggal_mulai', 'desc')->get();

        return view('admin.psb.gelombang.index', compact('gelombangs', 'pesantrens', 'tahuns'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pesantren_id' => 'required|exists:pesantren,id',
            'tahun_pelajaran_id' => 'required|exists:tahun_pelajaran,id',
            'nama' => 'required|string|max:100',
            'tanggal_buka' => 'required|date',
            'tanggal_tutup' => 'required|date|after_or_equal:tanggal_buka',
            'tanggal_seleksi_awal' => 'nullable|date|after_or_equal:tanggal_buka',
            'tanggal_seleksi_akhir' => 'nullable|date|after_or_equal:tanggal_seleksi_awal',
            'tanggal_daftar_ulang_awal' => 'nullable|date|after_or_equal:tanggal_tutup',
            'tanggal_daftar_ulang_akhir' => 'nullable|date|after_or_equal:tanggal_daftar_ulang_awal',
            'kuota' => 'required|integer|min:0',
            'biaya_pendaftaran' => 'required|numeric|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

        GelombangPsb::create($validated);

        return back()->with('success', 'Gelombang pendaftaran berhasil ditambahkan.');
    }

    public function update(Request $request, GelombangPsb $gelombang)
    {
        $validated = $request->validate([
            'pesantren_id' => 'required|exists:pesantren,id',
            'tahun_pelajaran_id' => 'required|exists:tahun_pelajaran,id',
            'nama' => 'required|string|max:100',
            'tanggal_buka' => 'required|date',
            'tanggal_tutup' => 'required|date|after_or_equal:tanggal_buka',
            'tanggal_seleksi_awal' => 'nullable|date|after_or_equal:tanggal_buka',
            'tanggal_seleksi_akhir' => 'nullable|date|after_or_equal:tanggal_seleksi_awal',
            'tanggal_daftar_ulang_awal' => 'nullable|date|after_or_equal:tanggal_tutup',
            'tanggal_daftar_ulang_akhir' => 'nullable|date|after_or_equal:tanggal_daftar_ulang_awal',
            'kuota' => 'required|integer|min:0',
            'biaya_pendaftaran' => 'required|numeric|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $gelombang->update($validated);

        return back()->with('success', 'Gelombang pendaftaran berhasil diperbarui.');
    }

    public function destroy(GelombangPsb $gelombang)
    {
        if ($gelombang->calonSantri()->exists()) {
            return back()->with('error', 'Gelombang tidak dapat dihapus karena sudah memiliki data calon santri terdaftar. Silakan nonaktifkan saja.');
        }
        
        $gelombang->delete();
        return back()->with('success', 'Gelombang pendaftaran berhasil dihapus.');
    }
}
