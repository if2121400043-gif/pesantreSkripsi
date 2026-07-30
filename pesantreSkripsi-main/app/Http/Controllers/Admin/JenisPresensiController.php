<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisPresensi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JenisPresensiController extends Controller
{
    public function index()
    {
        $jenisPresensi = JenisPresensi::orderBy('urutan')->get();
        return view('admin.akademik.jenis-presensi.index', compact('jenisPresensi'));
    }

    public function create()
    {
        $jenisPresensi = null;
        $lastUrutan = JenisPresensi::max('urutan') ?? 0;
        return view('admin.akademik.jenis-presensi.form', compact('jenisPresensi', 'lastUrutan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kode' => 'required|string|max:30|unique:jenis_presensi,kode',
            'deskripsi' => 'nullable|string',
            'target_gender' => 'required|in:SEMUA,PUTRA,PUTRI',
            'tipe_target' => 'required|in:SEMUA_SANTRI,PER_ROMBEL,PER_ASRAMA',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i',
            'is_active' => 'boolean',
            'urutan' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['urutan'] = $validated['urutan'] ?? (JenisPresensi::max('urutan') + 1);

        JenisPresensi::create($validated);

        return redirect()->route('admin.jenis-presensi.index')
            ->with('success', 'Jenis presensi berhasil ditambahkan.');
    }

    public function edit(JenisPresensi $jenisPresensi)
    {
        $lastUrutan = JenisPresensi::max('urutan') ?? 0;
        return view('admin.akademik.jenis-presensi.form', compact('jenisPresensi', 'lastUrutan'));
    }

    public function update(Request $request, JenisPresensi $jenisPresensi)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kode' => ['required', 'string', 'max:30', Rule::unique('jenis_presensi', 'kode')->ignore($jenisPresensi->id)],
            'deskripsi' => 'nullable|string',
            'target_gender' => 'required|in:SEMUA,PUTRA,PUTRI',
            'tipe_target' => 'required|in:SEMUA_SANTRI,PER_ROMBEL,PER_ASRAMA',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i',
            'is_active' => 'boolean',
            'urutan' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $jenisPresensi->update($validated);

        return redirect()->route('admin.jenis-presensi.index')
            ->with('success', 'Jenis presensi berhasil diperbarui.');
    }

    public function destroy(JenisPresensi $jenisPresensi)
    {
        // Check if there are attendance records using this type
        if ($jenisPresensi->presensi()->exists()) {
            // Soft delete: just deactivate
            $jenisPresensi->update(['is_active' => false]);
            return redirect()->route('admin.jenis-presensi.index')
                ->with('success', 'Jenis presensi dinonaktifkan karena masih memiliki data presensi.');
        }

        $jenisPresensi->delete();
        return redirect()->route('admin.jenis-presensi.index')
            ->with('success', 'Jenis presensi berhasil dihapus.');
    }
}
