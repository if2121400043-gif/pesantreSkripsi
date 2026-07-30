<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asrama;
use App\Models\Kamar;
use App\Models\Pesantren;
use Illuminate\Http\Request;

class AsramaController extends Controller
{
    // Menampilkan halaman daftar asrama
    public function index(Request $request)
    {
        $pesantren = Pesantren::first();
        $wilayahs = \App\Models\WilayahPesantren::where('is_active', true)->orderBy('nama')->get();
        
        $query = Asrama::with(['wilayahPesantren'])->withCount('kamar');

        if ($request->filled('wilayah_id')) {
            $query->where('wilayah_pesantren_id', $request->wilayah_id);
        }

        $asramas = $query->orderBy('nama')->get();
        
        return view('admin.asrama.index', compact('asramas', 'pesantren', 'wilayahs'));
    }

    // Menyimpan data asrama baru ke database
    public function store(Request $request)
    {
        $pesantren = Pesantren::first();
        
        $validated = $request->validate([
            'wilayah_pesantren_id' => 'nullable|exists:wilayah_pesantren,id',
            'nama' => 'required|string|max:100',
            'kode' => 'nullable|string|max:20',
            'jenis_kelamin' => 'required|in:L,P,CAMPURAN',
            'kapasitas' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        if (empty($validated['kode'])) {
            $validated['kode'] = \App\Helpers\CodeGenerator::generate($validated['nama'], 'asrama', 'kode');
        } else {
            $validated['kode'] = \App\Helpers\CodeGenerator::generate($validated['kode'], 'asrama', 'kode');
        }

        $validated['pesantren_id'] = $pesantren->id;
        $validated['is_active'] = $request->has('is_active');

        Asrama::create($validated);

        return redirect()->route('admin.asrama.index')->with('success', 'Asrama berhasil ditambahkan.');
    }

    public function update(Request $request, Asrama $asrama)
    {
        $validated = $request->validate([
            'wilayah_pesantren_id' => 'nullable|exists:wilayah_pesantren,id',
            'nama' => 'required|string|max:100',
            'kode' => 'nullable|string|max:20',
            'jenis_kelamin' => 'required|in:L,P,CAMPURAN',
            'kapasitas' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        if (empty($validated['kode'])) {
            $validated['kode'] = \App\Helpers\CodeGenerator::generate($validated['nama'], 'asrama', 'kode', $asrama->id);
        } else {
            $validated['kode'] = \App\Helpers\CodeGenerator::generate($validated['kode'], 'asrama', 'kode', $asrama->id);
        }

        $validated['is_active'] = $request->has('is_active');

        $asrama->update($validated);

        return redirect()->route('admin.asrama.index')->with('success', 'Asrama berhasil diperbarui.');
    }

    // Menghapus data asrama
    public function destroy(Asrama $asrama)
    {
        // LOGIKA PROTEKSI: Mengecek apakah asrama ini masih memiliki kamar di dalamnya.
        // Jika masih ada (jumlah kamar > 0), maka asrama tidak boleh dihapus untuk mencegah error relasi database.
        if ($asrama->kamar()->count() > 0) {
            // Kembali ke halaman sebelumnya dengan pesan error
            return back()->with('error', 'Tidak dapat menghapus asrama yang masih memiliki kamar.');
        }

        // Jika aman (kamar kosong), hapus asrama
        $asrama->delete();
        
        return redirect()->route('admin.asrama.index')->with('success', 'Asrama berhasil dihapus.');
    }

    // ── Manajemen Kamar ──
    
    // Menampilkan detail asrama beserta daftar kamarnya
    public function show(Asrama $asrama)
    {
        // Get active Tahun Pelajaran
        $activeTahun = \App\Models\TahunPelajaran::where('is_active', true)->first();

        // Mengambil seluruh data kamar yang spesifik hanya dimiliki oleh asrama tersebut.
        // Diurutkan berdasarkan nama kamar (A-Z).
        $kamars = $asrama->kamar()
            ->withCount(['penghuni as terisi_count' => function ($q) use ($activeTahun) {
                if ($activeTahun) {
                    $q->where('tahun_pelajaran_id', $activeTahun->id)
                      ->where('status_mukim', 'MUKIM');
                }
            }])
            ->orderBy('nama')
            ->get();
        
        // Menampilkan view detail dengan membawa data spesifik asrama, daftar kamarnya, dan tahun pelajaran aktif
        return view('admin.asrama.show', compact('asrama', 'kamars', 'activeTahun'));
    }

    // Menyimpan data kamar baru untuk asrama tertentu
    public function storeKamar(Request $request, Asrama $asrama)
    {
        // Validasi input kamar
        $validated = $request->validate([
            'nama' => 'required|string|max:50', // Wajib, teks, max 50 char
            'lantai' => 'nullable|string|max:10', // Boleh kosong (misal tidak ada lantai bertingkat)
            'kapasitas' => 'required|integer|min:1', // Kapasitas minimal 1 orang
        ]);

        // Cek status aktif kamar
        $validated['is_active'] = $request->has('is_active');
        
        // Menyimpan kamar BARU langsung melalui relasi asrama-nya.
        // Cara ini otomatis akan mengisi kolom 'asrama_id' di tabel kamar tanpa perlu kita tulis manual.
        $asrama->kamar()->create($validated);

        // Kembali ke halaman sebelumnya (halaman detail asrama) dengan pesan sukses
        return back()->with('success', 'Kamar berhasil ditambahkan.');
    }

    // Memperbarui data kamar yang spesifik
    public function updateKamar(Request $request, Asrama $asrama, Kamar $kamar)
    {
        // Validasi data baru
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
            'lantai' => 'nullable|string|max:10',
            'kapasitas' => 'required|integer|min:1',
        ]);

        $validated['is_active'] = $request->has('is_active');
        
        // Update data pada record kamar tersebut
        $kamar->update($validated);

        return back()->with('success', 'Kamar berhasil diperbarui.');
    }

    // Menghapus data kamar
    public function destroyKamar(Asrama $asrama, Kamar $kamar)
    {
        // LOGIKA PROTEKSI: Mengecek apakah kamar ini memiliki penghuni (santri/siswa).
        // Jika masih ada orangnya, kamar tidak bisa dihapus.
        if ($kamar->penghuni()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus kamar yang memiliki penghuni.');
        }

        // Jika kamar kosong dari penghuni, hapus kamar
        $kamar->delete();
        
        return back()->with('success', 'Kamar berhasil dihapus.');
    }
}