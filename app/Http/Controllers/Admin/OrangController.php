<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Orang;
use App\Models\Provinsi;
use App\Models\Desa;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrangController extends Controller
{
    public function index(Request $request)
    {
        $query = Orang::with(['pesertaDidik', 'pegawai', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('niup', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
        }

        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        $orangs = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        return view('admin.orang.index', compact('orangs'));
    }

    public function create()
    {
        // Get all provinsis for the dropdown
        $provinsis = Provinsi::orderBy('nama')->get();
        return view('admin.orang.create', compact('provinsis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:200',
            'nama_panggilan' => 'nullable|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'nik' => 'nullable|string|size:16|unique:orang,nik',
            'no_kk' => 'nullable|string|size:16',
            'no_paspor' => 'nullable|string|max:30',
            'golongan_darah' => 'nullable|in:A,B,AB,O',
            'kewarganegaraan' => 'required|string|max:50',
            'anak_ke' => 'nullable|integer|min:1',
            'jumlah_saudara' => 'nullable|integer|min:0',
            'alamat_lengkap' => 'nullable|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'desa_id' => 'nullable|exists:desa,id',
            'kode_pos' => 'nullable|string|max:10',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
        ]);

        DB::beginTransaction();
        try {
            $validated['is_active'] = $request->has('is_active');

            // NIUP sekarang otomatis di-generate oleh Model Events (creating) di Orang.php
            $orang = Orang::create($validated);
            DB::commit();

            return redirect()->route('admin.orang.index')->with('success', 'Data Identitas Induk berhasil ditambahkan. NIUP: ' . $orang->niup);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function show(Orang $orang)
    {
        $orang->load(['desa.kecamatan.kabupaten.provinsi', 'pesertaDidik', 'pegawai', 'user']);
        return view('admin.orang.show', compact('orang'));
    }

    public function edit(Orang $orang)
    {
        $provinsis = Provinsi::orderBy('nama')->get();
        $orang->load('desa.kecamatan.kabupaten.provinsi');
        return view('admin.orang.edit', compact('orang', 'provinsis'));
    }

    public function update(Request $request, Orang $orang)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:200',
            'nama_panggilan' => 'nullable|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'nik' => 'nullable|string|size:16|unique:orang,nik,' . $orang->id,
            'no_kk' => 'nullable|string|size:16',
            'no_paspor' => 'nullable|string|max:30',
            'golongan_darah' => 'nullable|in:A,B,AB,O',
            'kewarganegaraan' => 'required|string|max:50',
            'anak_ke' => 'nullable|integer|min:1',
            'jumlah_saudara' => 'nullable|integer|min:0',
            'alamat_lengkap' => 'nullable|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'desa_id' => 'nullable|exists:desa,id',
            'kode_pos' => 'nullable|string|max:10',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $orang->update($validated);

        return redirect()->route('admin.orang.index')->with('success', 'Data Identitas berhasil diperbarui.');
    }

    public function destroy(Orang $orang)
    {
        if ($orang->user || $orang->pesertaDidik || $orang->pegawai) {
            return back()->with('error', 'Tidak dapat menghapus data yang terhubung dengan entitas Santri, Pegawai, atau Akun User.');
        }

        $orang->delete();
        return redirect()->route('admin.orang.index')->with('success', 'Data Identitas berhasil dihapus.');
    }

    // API for region dropdowns
    public function getKabupaten(Provinsi $provinsi)
    {
        return response()->json($provinsi->kabupaten()->orderBy('nama')->get());
    }

    public function getKecamatan($kabupaten_id)
    {
        return response()->json(\App\Models\Kecamatan::where('kabupaten_id', $kabupaten_id)->orderBy('nama')->get());
    }

    public function getDesa($kecamatan_id)
    {
        return response()->json(\App\Models\Desa::where('kecamatan_id', $kecamatan_id)->orderBy('nama')->get());
    }
}
