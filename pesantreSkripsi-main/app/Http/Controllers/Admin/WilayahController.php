<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Desa;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    /**
     * Main page: Shows all Provinsi with expandable hierarchy
     */
    public function index(Request $request)
    {
        $provinsis = Provinsi::withCount('kabupaten')
            ->orderBy('nama')
            ->paginate(10, ['*'], 'prov_page')
            ->withQueryString();

        // If a provinsi is selected, load its children
        $selectedProvinsi = null;
        $kabupatens = collect();
        $selectedKabupaten = null;
        $kecamatans = collect();
        $selectedKecamatan = null;
        $desas = collect();

        if ($request->filled('provinsi_id')) {
            $selectedProvinsi = Provinsi::find($request->provinsi_id);
            if ($selectedProvinsi) {
                $kabupatens = Kabupaten::where('provinsi_id', $request->provinsi_id)
                    ->withCount('kecamatan')
                    ->orderBy('nama')
                    ->paginate(10, ['*'], 'kab_page')
                    ->withQueryString();
            }
        }

        if ($request->filled('kabupaten_id')) {
            $selectedKabupaten = Kabupaten::find($request->kabupaten_id);
            if ($selectedKabupaten) {
                $kecamatans = Kecamatan::where('kabupaten_id', $request->kabupaten_id)
                    ->withCount('desa')
                    ->orderBy('nama')
                    ->paginate(10, ['*'], 'kec_page')
                    ->withQueryString();
            }
        }

        if ($request->filled('kecamatan_id')) {
            $selectedKecamatan = Kecamatan::find($request->kecamatan_id);
            if ($selectedKecamatan) {
                $desas = Desa::where('kecamatan_id', $request->kecamatan_id)
                    ->orderBy('nama')
                    ->paginate(15, ['*'], 'desa_page')
                    ->withQueryString();
            }
        }

        return view('admin.wilayah.index', compact(
            'provinsis', 'kabupatens', 'kecamatans', 'desas',
            'selectedProvinsi', 'selectedKabupaten', 'selectedKecamatan'
        ));
    }

    // ── PROVINSI CRUD ──

    public function storeProvinsi(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:10|unique:provinsi,kode',
            'nama' => 'required|string|max:100',
        ]);

        Provinsi::create($validated);
        return back()->with('success', 'Provinsi berhasil ditambahkan.');
    }

    public function updateProvinsi(Request $request, Provinsi $provinsi)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:10|unique:provinsi,kode,' . $provinsi->id,
            'nama' => 'required|string|max:100',
        ]);

        $provinsi->update($validated);
        return back()->with('success', 'Provinsi berhasil diperbarui.');
    }

    public function destroyProvinsi(Provinsi $provinsi)
    {
        if ($provinsi->kabupaten()->count() > 0) {
            return back()->with('error', 'Provinsi tidak dapat dihapus karena masih memiliki data Kabupaten/Kota.');
        }
        $provinsi->delete();
        return back()->with('success', 'Provinsi berhasil dihapus.');
    }

    // ── KABUPATEN CRUD ──

    public function storeKabupaten(Request $request)
    {
        $validated = $request->validate([
            'provinsi_id' => 'required|exists:provinsi,id',
            'kode' => 'required|string|max:10|unique:kabupaten,kode',
            'nama' => 'required|string|max:100',
        ]);

        Kabupaten::create($validated);
        return back()->with('success', 'Kabupaten/Kota berhasil ditambahkan.');
    }

    public function updateKabupaten(Request $request, Kabupaten $kabupaten)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:10|unique:kabupaten,kode,' . $kabupaten->id,
            'nama' => 'required|string|max:100',
        ]);

        $kabupaten->update($validated);
        return back()->with('success', 'Kabupaten/Kota berhasil diperbarui.');
    }

    public function destroyKabupaten(Kabupaten $kabupaten)
    {
        if ($kabupaten->kecamatan()->count() > 0) {
            return back()->with('error', 'Kabupaten tidak dapat dihapus karena masih memiliki data Kecamatan.');
        }
        $kabupaten->delete();
        return redirect()->route('admin.wilayah.index', ['provinsi_id' => $kabupaten->provinsi_id])->with('success', 'Kabupaten/Kota berhasil dihapus.');
    }

    // ── KECAMATAN CRUD ──

    public function storeKecamatan(Request $request)
    {
        $validated = $request->validate([
            'kabupaten_id' => 'required|exists:kabupaten,id',
            'kode' => 'required|string|max:10|unique:kecamatan,kode',
            'nama' => 'required|string|max:100',
        ]);

        Kecamatan::create($validated);
        return back()->with('success', 'Kecamatan berhasil ditambahkan.');
    }

    public function updateKecamatan(Request $request, Kecamatan $kecamatan)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:10|unique:kecamatan,kode,' . $kecamatan->id,
            'nama' => 'required|string|max:100',
        ]);

        $kecamatan->update($validated);
        return back()->with('success', 'Kecamatan berhasil diperbarui.');
    }

    public function destroyKecamatan(Kecamatan $kecamatan)
    {
        if ($kecamatan->desa()->count() > 0) {
            return back()->with('error', 'Kecamatan tidak dapat dihapus karena masih memiliki data Desa/Kelurahan.');
        }
        $kabId = $kecamatan->kabupaten_id;
        $provId = $kecamatan->kabupaten->provinsi_id;
        $kecamatan->delete();
        return redirect()->route('admin.wilayah.index', ['provinsi_id' => $provId, 'kabupaten_id' => $kabId])->with('success', 'Kecamatan berhasil dihapus.');
    }

    // ── DESA CRUD ──

    public function storeDesa(Request $request)
    {
        $validated = $request->validate([
            'kecamatan_id' => 'required|exists:kecamatan,id',
            'kode' => 'required|string|max:15|unique:desa,kode',
            'nama' => 'required|string|max:100',
        ]);

        Desa::create($validated);
        return back()->with('success', 'Desa/Kelurahan berhasil ditambahkan.');
    }

    public function updateDesa(Request $request, Desa $desa)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:15|unique:desa,kode,' . $desa->id,
            'nama' => 'required|string|max:100',
        ]);

        $desa->update($validated);
        return back()->with('success', 'Desa/Kelurahan berhasil diperbarui.');
    }

    public function destroyDesa(Desa $desa)
    {
        $kecId = $desa->kecamatan_id;
        $kabId = $desa->kecamatan->kabupaten_id;
        $provId = $desa->kecamatan->kabupaten->provinsi_id;
        $desa->delete();
        return redirect()->route('admin.wilayah.index', [
            'provinsi_id' => $provId,
            'kabupaten_id' => $kabId,
            'kecamatan_id' => $kecId
        ])->with('success', 'Desa/Kelurahan berhasil dihapus.');
    }
}
