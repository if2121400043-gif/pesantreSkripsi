<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WilayahPesantren;
use App\Models\Pesantren;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    /**
     * Main page: Shows all Wilayah Pesantren (Zona/Daerah) with Asrama summary
     */
    public function index(Request $request)
    {
        $query = WilayahPesantren::with(['asrama.kamar'])->withCount('asrama');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%");
        }

        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        $wilayahs = $query->orderBy('nama')->paginate(10)->withQueryString();
        $pesantren = Pesantren::first();

        return view('admin.wilayah.index', compact('wilayahs', 'pesantren'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kode' => 'nullable|string|max:20',
            'jenis_kelamin' => 'required|in:L,P,CAMPURAN',
            'keterangan' => 'nullable|string',
        ]);

        $pesantren = Pesantren::first();
        if (!$pesantren) {
            return back()->with('error', 'Data Pesantren belum diinisialisasi.');
        }

        $validated['pesantren_id'] = $pesantren->id;
        $validated['is_active'] = true;

        WilayahPesantren::create($validated);

        return redirect()->route('admin.wilayah.index')->with('success', 'Wilayah Pesantren berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $wilayah = WilayahPesantren::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kode' => 'nullable|string|max:20',
            'jenis_kelamin' => 'required|in:L,P,CAMPURAN',
            'keterangan' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $wilayah->update($validated);

        return redirect()->route('admin.wilayah.index')->with('success', 'Wilayah Pesantren berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $wilayah = WilayahPesantren::findOrFail($id);

        if ($wilayah->asrama()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus Wilayah Pesantren yang masih memiliki Asrama.');
        }

        $wilayah->delete();

        return redirect()->route('admin.wilayah.index')->with('success', 'Wilayah Pesantren berhasil dihapus.');
    }
}
