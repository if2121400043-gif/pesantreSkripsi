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
    public function index()
    {
        // Mengambil data pesantren pertama di database (asumsinya sistem ini untuk 1 pesantren tunggal)
        $pesantren = Pesantren::first();
        
        // Mengambil semua data asrama.
        // 'withCount('kamar')' akan secara otomatis menghitung jumlah relasi kamar untuk setiap asrama.
        // 'orderBy('nama')' akan mengurutkan data asrama berdasarkan abjad (A-Z).
        // 'get()' mengeksekusi query dan mengambil hasilnya.
        $asramas = Asrama::withCount('kamar')->orderBy('nama')->get();
        
        // Mengirim data $asramas dan $pesantren ke tampilan (view) 'admin.asrama.index'
        return view('admin.asrama.index', compact('asramas', 'pesantren'));
    }

    // Menyimpan data asrama baru ke database
    public function store(Request $request)
    {
        // Mengambil id pesantren untuk relasi asrama
        $pesantren = Pesantren::first();
        
        // Memvalidasi data yang dikirimkan melalui form.
        // Jika tidak sesuai aturan ini, proses akan berhenti dan kembali ke form dengan pesan error.
        $validated = $request->validate([
            'nama' => 'required|string|max:100', // Wajib diisi, berupa teks, maksimal 100 karakter
            'kode' => 'nullable|string|max:20',  // Boleh kosong, teks, maksimal 20 karakter
            'jenis_kelamin' => 'required|in:L,P,CAMPURAN', // Wajib, dan nilainya harus antara L, P, atau CAMPURAN
            'kapasitas' => 'required|integer|min:0', // Wajib, berupa angka bulat, minimal 0
            'keterangan' => 'nullable|string', // Boleh kosong, berupa teks
        ]);

        // Menambahkan data pesantren_id ke dalam array data yang sudah divalidasi
        $validated['pesantren_id'] = $pesantren->id;
        
        // Menangani input checkbox 'is_active'.
        // Jika dicentang, form mengirim data dan bernilai true. Jika tidak, bernilai false.
        $validated['is_active'] = $request->has('is_active');

        // Menyimpan data ke database melalui Model Asrama (Mass Assignment)
        Asrama::create($validated);

        // Mengarahkan pengguna kembali ke halaman daftar asrama dengan pesan sukses
        return redirect()->route('admin.asrama.index')->with('success', 'Asrama berhasil ditambahkan.');
    }

    // Memperbarui data asrama yang sudah ada
    // Parameter 'Asrama $asrama' otomatis mencari data di database berdasarkan ID di URL (Route Model Binding)
    public function update(Request $request, Asrama $asrama)
    {
        // Proses validasinya sama dengan saat membuat (store)
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kode' => 'nullable|string|max:20',
            'jenis_kelamin' => 'required|in:L,P,CAMPURAN',
            'kapasitas' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        // Update status aktif/tidak berdasarkan checkbox
        $validated['is_active'] = $request->has('is_active');

        // Memperbarui record spesifik yang ditemukan di database dengan data baru
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