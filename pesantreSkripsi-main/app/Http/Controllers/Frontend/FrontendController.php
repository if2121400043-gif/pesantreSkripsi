<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pesantren;
use App\Models\Berita;
use App\Models\GelombangPsb;
use App\Models\TahunPelajaran;
use App\Models\CalonSantri;
use App\Models\Orang;
use App\Models\PesertaDidik;
use App\Models\Rombel;
use App\Models\Pegawai;
use App\Models\Lembaga;
use App\Models\DokumenPsb;
use App\Models\Media;

class FrontendController extends Controller
{
    // ── Halaman Beranda Utama ──
    public function index()
    {
        $pesantren = Pesantren::first();
        
        // Cek status Pendaftaran Santri Baru (PSB)
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $isPsbBuka = false;
        if ($tahunAktif) {
            $isPsbBuka = GelombangPsb::where('tahun_pelajaran_id', $tahunAktif->id)
                ->where('is_active', true)
                ->whereDate('tanggal_buka', '<=', now())
                ->whereDate('tanggal_tutup', '>=', now())
                ->exists();
        }
        
        $totalSantri = PesertaDidik::where('status', 'AKTIF')->count();
        $totalPegawai = Pegawai::where('is_active', true)->count();
        
        $totalRombel = Rombel::whereHas('tahunPelajaran', function($q) {
            $q->where('is_active', true);
        })->count();

        $berita_terbaru = Berita::where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();
            
        $lembagas = Lembaga::where('is_active', true)->orderBy('urutan')->get();

        return view('frontend.home', compact('pesantren', 'totalSantri', 'totalPegawai', 'totalRombel', 'berita_terbaru', 'lembagas', 'isPsbBuka'));
    }

    // ── Halaman Profil Pesantren ──
    public function profil()
    {
        $pesantren = Pesantren::with('desa.kecamatan.kabupaten.provinsi')->first();
        return view('frontend.profil', compact('pesantren'));
    }

    // ── Halaman Daftar Berita ──
    public function berita()
    {
        $pesantren = Pesantren::first();
        $beritas = Berita::where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->paginate(9);
            
        return view('frontend.berita.index', compact('pesantren', 'beritas'));
    }

    // ── Halaman Detail Berita ──
    public function showBerita($slug)
    {
        $pesantren = Pesantren::first();
        $berita = Berita::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();
            
        $berita->increment('view_count');
            
        $beritaLainnya = Berita::where('is_published', true)
            ->where('id', '!=', $berita->id)
            ->orderBy('published_at', 'desc')
            ->take(4)
            ->get();

        return view('frontend.berita.show', compact('pesantren', 'berita', 'beritaLainnya'));
    }

    // ── Halaman Informasi Penerimaan Santri Baru (PSB) ──
    public function psb()
    {
        $pesantren = Pesantren::first();
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        
        $gelombangsAktif = collect();
        if ($tahunAktif) {
            $gelombangsAktif = GelombangPsb::where('tahun_pelajaran_id', $tahunAktif->id)
                ->where('is_active', true)
                ->whereDate('tanggal_buka', '<=', now())
                ->whereDate('tanggal_tutup', '>=', now())
                ->get();
        }

        return view('frontend.psb.landing', compact('pesantren', 'tahunAktif', 'gelombangsAktif'));
    }

    // ── Halaman Form Pendaftaran PSB ──
    public function daftar(Request $request)
    {
        $pesantren = Pesantren::first();
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        
        $gelombangAktif = null;
        if ($tahunAktif) {
            $query = GelombangPsb::where('tahun_pelajaran_id', $tahunAktif->id)
                ->where('is_active', true)
                ->whereDate('tanggal_buka', '<=', now())
                ->whereDate('tanggal_tutup', '>=', now());
                
            if ($request->has('gelombang_id')) {
                $gelombangAktif = clone $query;
                $gelombangAktif = $gelombangAktif->where('id', $request->gelombang_id)->first();
            }
            
            if (!$gelombangAktif) {
                $gelombangAktif = $query->first();
            }
        }

        if (!$gelombangAktif) {
            return redirect()->route('frontend.psb')->with('error', 'Pendaftaran saat ini sedang ditutup.');
        }

        $captcha_num1 = rand(1, 10);
        $captcha_num2 = rand(1, 10);
        session(['captcha_answer' => $captcha_num1 + $captcha_num2]);

        $lembagas = Lembaga::where('is_active', true)->orderBy('urutan')->get();

        return view('frontend.psb.daftar', compact('pesantren', 'tahunAktif', 'gelombangAktif', 'captcha_num1', 'captcha_num2', 'lembagas'));
    }

    // ── KODE YANG DIUBAH (PERBAIKAN LOGIKA) ──
    public function storePsb(Request $request)
    {
        if (!empty($request->website_url_website)) {
            return redirect()->route('frontend.psb')->with('success', 'Pendaftaran berhasil disubmit!');
        }

        if ((int)$request->captcha_answer !== session('captcha_answer')) {
            return back()->with('error', 'Jawaban keamanan matematika tidak tepat. Silakan coba lagi.')->withInput();
        }

        $request->validate([
            'gelombang_id' => 'required|exists:gelombang_psb,id',
            'nik' => 'required|string|size:16|unique:calon_santri,nik|unique:orang,nik', 
            'kk' => 'nullable|string|max:20',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'asal_sekolah' => 'nullable|string|max:150',
            'alamat' => 'nullable|string',
            // Data Ayah
            'nama_ayah' => 'nullable|string|max:150',
            'nik_ayah' => 'nullable|string|max:20',
            'tahun_lahir_ayah' => 'nullable|string|max:4',
            'pendidikan_ayah' => 'nullable|string|max:50',
            'pekerjaan_ayah' => 'nullable|string|max:100',
            'penghasilan_ayah' => 'nullable|string|max:50',
            'no_hp_ayah' => 'nullable|string|max:20',
            // Data Ibu
            'nama_ibu' => 'nullable|string|max:150',
            'nik_ibu' => 'nullable|string|max:20',
            'tahun_lahir_ibu' => 'nullable|string|max:4',
            'pendidikan_ibu' => 'nullable|string|max:50',
            'pekerjaan_ibu' => 'nullable|string|max:100',
            'penghasilan_ibu' => 'nullable|string|max:50',
            'no_hp_ibu' => 'nullable|string|max:20',
            // Wali & Kontak
            'telepon_wali' => 'required|string|max:20',
            'tinggal_bersama' => 'nullable|string|max:50',
            'nama_wali' => 'nullable|string|max:150',
            'nik_wali' => 'nullable|string|max:20',
            'tahun_lahir_wali' => 'nullable|string|max:4',
            'pendidikan_wali' => 'nullable|string|max:50',
            'pekerjaan_wali' => 'nullable|string|max:100',
            'penghasilan_wali' => 'nullable|string|max:50',
            'no_hp_wali' => 'nullable|string|max:20',
            'hubungan_wali' => 'nullable|string|max:50',
            'lembaga_tujuan_id' => 'nullable|exists:lembaga,id',
        ]);

        try {
            $calonSantri = CalonSantri::create([
                'gelombang_id' => $request->gelombang_id,
                'lembaga_tujuan_id' => $request->lembaga_tujuan_id,
                'nama_lengkap' => $request->nama_lengkap, 
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'nik' => $request->nik,
                'no_kk' => $request->kk,
                'asal_sekolah' => $request->asal_sekolah,
                'alamat' => $request->alamat,
                // Data Ayah
                'nama_ayah' => $request->nama_ayah,
                'nik_ayah' => $request->nik_ayah,
                'tahun_lahir_ayah' => $request->tahun_lahir_ayah,
                'pendidikan_ayah' => $request->pendidikan_ayah,
                'pekerjaan_ayah' => $request->pekerjaan_ayah,
                'penghasilan_ayah' => $request->penghasilan_ayah,
                'no_hp_ayah' => $request->no_hp_ayah,
                // Data Ibu
                'nama_ibu' => $request->nama_ibu,
                'nik_ibu' => $request->nik_ibu,
                'tahun_lahir_ibu' => $request->tahun_lahir_ibu,
                'pendidikan_ibu' => $request->pendidikan_ibu,
                'pekerjaan_ibu' => $request->pekerjaan_ibu,
                'penghasilan_ibu' => $request->penghasilan_ibu,
                'no_hp_ibu' => $request->no_hp_ibu,
                // Wali & Kontak
                'telepon_wali' => $request->telepon_wali,
                'tinggal_bersama' => $request->tinggal_bersama,
                'nama_wali' => $request->nama_wali,
                'nik_wali' => $request->nik_wali,
                'tahun_lahir_wali' => $request->tahun_lahir_wali,
                'pendidikan_wali' => $request->pendidikan_wali,
                'pekerjaan_wali' => $request->pekerjaan_wali,
                'penghasilan_wali' => $request->penghasilan_wali,
                'no_hp_wali' => $request->no_hp_wali,
                'hubungan_wali' => $request->hubungan_wali,
            ]);

            return redirect()->route('frontend.psb.upload', ['no_pendaftaran' => $calonSantri->no_pendaftaran])
                ->with('success', 'Formulir berhasil disimpan. Silakan lanjutkan dengan mengunggah berkas persyaratan.');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mendaftar: ' . $e->getMessage())->withInput();
        }
    }
    // ───────────────────────────────────────

    // ── Halaman Form Upload Berkas ──
    public function uploadBerkas($no_pendaftaran)
    {
        $pesantren = Pesantren::first();
        $calonSantri = CalonSantri::where('no_pendaftaran', $no_pendaftaran)->firstOrFail();
        
        return view('frontend.psb.upload', compact('pesantren', 'calonSantri'));
    }

    // ── Proses Menyimpan Berkas yang Diupload ──
    public function storeBerkas(Request $request, $no_pendaftaran)
    {
        $calonSantri = CalonSantri::where('no_pendaftaran', $no_pendaftaran)->firstOrFail();

        $request->validate([
            'kartu_keluarga' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'akta_kelahiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'ijazah' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'pas_foto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'ktp_orangtua' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $berkasTypes = ['kartu_keluarga', 'akta_kelahiran', 'ijazah', 'pas_foto', 'ktp_orangtua'];

        foreach ($berkasTypes as $jenis) {
            if ($request->hasFile($jenis)) {
                // Cari dokumen lama terlebih dahulu
                $existingDoc = DokumenPsb::where('calon_santri_id', $calonSantri->id)
                    ->where('jenis_dokumen', $jenis)
                    ->first();

                $file = $request->file($jenis);
                // Store on private 'local' disk instead of 'public' disk
                $path = $file->store('psb/dokumen/' . date('Y/m'), 'local');

                if ($existingDoc) {
                    // Hapus file lama dari disk
                    if ($existingDoc->file_path) {
                        // Check if old file exists on local or public disk and delete it
                        if (\Illuminate\Support\Facades\Storage::disk('local')->exists($existingDoc->file_path)) {
                            \Illuminate\Support\Facades\Storage::disk('local')->delete($existingDoc->file_path);
                        } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($existingDoc->file_path)) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($existingDoc->file_path);
                        } else {
                            // Fallback for legacy database paths starting with 'public/'
                            if (str_starts_with($existingDoc->file_path, 'public/')) {
                                \Illuminate\Support\Facades\Storage::disk('local')->delete($existingDoc->file_path);
                            } else {
                                \Illuminate\Support\Facades\Storage::disk('public')->delete($existingDoc->file_path);
                            }
                        }
                    }
                    
                    // Update data
                    $existingDoc->update([
                        'file_path' => $path,
                        'is_verified' => false, // Reset verifikasi jika upload ulang
                    ]);
                } else {
                    // Buat dokumen baru
                    DokumenPsb::create([
                        'calon_santri_id' => $calonSantri->id,
                        'jenis_dokumen' => $jenis,
                        'file_path' => $path,
                        'is_verified' => false,
                    ]);
                }
            }
        }

        return redirect()->route('frontend.psb.selesai', ['no_pendaftaran' => $calonSantri->no_pendaftaran]);
    }

    // ── KODE YANG DIUBAH ──
    public function selesai($no_pendaftaran)
    {
        $pesantren = Pesantren::first();
        
        // Dihapus relasi ->with('orang') karena CalonSantri saat ini belum terhubung dengan tabel Orang
        $calonSantri = CalonSantri::where('no_pendaftaran', $no_pendaftaran)->firstOrFail();
        
        return view('frontend.psb.selesai', compact('pesantren', 'calonSantri'));
    }

    // ── Halaman Galeri Media ──
    public function media(Request $request)
    {
        $pesantren = Pesantren::first();
        
        $query = Media::where('is_active', true);

        if ($request->has('kategori') && $request->kategori != '') {
            $query->where('kategori', $request->kategori);
        }

        $medias = $query->orderBy('created_at', 'desc')->paginate(12);
        
        // Ambil kategori unik untuk filter
        $categories = Media::where('is_active', true)
            ->whereNotNull('kategori')
            ->distinct()
            ->pluck('kategori');

        return view('frontend.media.index', compact('pesantren', 'medias', 'categories'));
    }

    // ── Mengakses Dokumen Secara Aman (Private Storage) ──
    public function serveSecureDokumen($id)
    {
        $dokumen = DokumenPsb::findOrFail($id);

        $user = auth()->user();
        if (!$user) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        $activeRole = $user->active_role;
        $roleName = $activeRole ? $activeRole->role->nama : null;

        if ($roleName !== 'SUPER_ADMIN' && $roleName !== 'PANITIA_PSB') {
            abort(403, 'Akses ditolak. Anda tidak memiliki wewenang melihat dokumen ini.');
        }

        $filePath = $dokumen->file_path;

        // Cari file di disk local (private) terlebih dahulu
        if (\Illuminate\Support\Facades\Storage::disk('local')->exists($filePath)) {
            $path = \Illuminate\Support\Facades\Storage::disk('local')->path($filePath);
        } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
            $path = \Illuminate\Support\Facades\Storage::disk('public')->path($filePath);
        } else {
            // Fallback penanganan path lama yang memiliki prefix 'public/'
            if (str_starts_with($filePath, 'public/') && \Illuminate\Support\Facades\Storage::disk('local')->exists(substr($filePath, 7))) {
                $path = \Illuminate\Support\Facades\Storage::disk('local')->path(substr($filePath, 7));
            } else {
                abort(404, 'Berkas fisik tidak ditemukan.');
            }
        }

        return response()->file($path);
    }
}