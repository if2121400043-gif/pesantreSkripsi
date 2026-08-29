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
use App\Jobs\SendWhatsAppMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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

        $berita_terbaru = Berita::published()
            ->berita()
            ->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        // Pengumuman terbaru untuk banner
        $pengumuman_terbaru = Berita::published()
            ->pengumuman()
            ->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->take(5)
            ->get();
            
        $lembagas = Lembaga::where('is_active', true)->orderBy('urutan')->get();

        return view('frontend.home', compact('pesantren', 'totalSantri', 'totalPegawai', 'totalRombel', 'berita_terbaru', 'pengumuman_terbaru', 'lembagas', 'isPsbBuka'));
    }

    // ── Halaman Profil Pesantren ──
    public function profil()
    {
        $pesantren = Pesantren::with('desa.kecamatan.kabupaten.provinsi')->first();
        return view('frontend.profil', compact('pesantren'));
    }

    // ── Halaman Publikasi (Berita & Pengumuman) ──
    public function publikasi(Request $request)
    {
        $pesantren = Pesantren::first();

        // Pencarian
        $searchQuery = $request->filled('q') ? $request->q : null;

        // Query Berita
        $beritaQuery = Berita::published()->berita();
        if ($searchQuery) {
            $beritaQuery->where(function($q) use ($searchQuery) {
                $q->where('judul', 'like', '%' . $searchQuery . '%')
                  ->orWhere('ringkasan', 'like', '%' . $searchQuery . '%');
            });
        }
        $beritas = $beritaQuery->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->paginate(4, ['*'], 'berita_page')
            ->withQueryString();

        // Query Pengumuman
        $pengumumanQuery = Berita::published()->pengumuman();
        if ($searchQuery) {
            $pengumumanQuery->where(function($q) use ($searchQuery) {
                $q->where('judul', 'like', '%' . $searchQuery . '%')
                  ->orWhere('ringkasan', 'like', '%' . $searchQuery . '%');
            });
        }
        $pengumumans = $pengumumanQuery->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->paginate(4, ['*'], 'pengumuman_page')
            ->withQueryString();
            
        return view('frontend.berita.index', compact('pesantren', 'beritas', 'pengumumans'));
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

        $captcha_num1 = random_int(10, 99);
        $captcha_num2 = random_int(10, 99);
        session(['captcha_answer' => $captcha_num1 + $captcha_num2]);
        session(['captcha_created_at' => now()->timestamp]);

        $lembagas = Lembaga::where('is_active', true)->orderBy('urutan')->get();

        return view('frontend.psb.daftar', compact('pesantren', 'tahunAktif', 'gelombangAktif', 'captcha_num1', 'captcha_num2', 'lembagas'));
    }

    // ── KODE YANG DIUBAH (PERBAIKAN LOGIKA) ──
    public function storePsb(Request $request)
    {
        $rateLimitKey = 'psb-registration|' . $request->ip();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            return back()->with('error', 'Terlalu banyak percobaan pendaftaran. Silakan coba lagi dalam beberapa menit.')
                ->withInput();
        }
        RateLimiter::hit($rateLimitKey, 60);

        if (!empty($request->website_url_website)) {
            return redirect()->route('frontend.psb')->with('success', 'Pendaftaran berhasil disubmit!');
        }

        if (
            !session('captcha_created_at')
            || now()->timestamp - (int) session('captcha_created_at') > 600
            || !hash_equals((string) session('captcha_answer'), (string) $request->captcha_answer)
        ) {
            return back()->with('error', 'Jawaban keamanan matematika tidak tepat atau sudah kedaluwarsa. Silakan muat ulang formulir.')
                ->withInput();
        }

        $validated = $request->validate([
            'gelombang_id' => 'required|exists:gelombang_psb,id',
            'nik' => 'required|digits:16|unique:calon_santri,nik|unique:orang,nik',
            'kk' => 'required|digits:16',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'asal_sekolah' => 'nullable|string|max:150',
            'alamat' => 'nullable|string',
            // Data Ayah
            'nama_ayah' => 'nullable|string|max:150',
            'nik_ayah' => 'nullable|digits:16',
            'tahun_lahir_ayah' => 'nullable|string|max:4',
            'pendidikan_ayah' => 'nullable|string|max:50',
            'pekerjaan_ayah' => 'nullable|string|max:100',
            'penghasilan_ayah' => 'nullable|string|max:50',
            'no_hp_ayah' => ['nullable', 'regex:/^(?:\+62|62|0)[0-9]{8,13}$/'],
            // Data Ibu
            'nama_ibu' => 'nullable|string|max:150',
            'nik_ibu' => 'nullable|digits:16',
            'tahun_lahir_ibu' => 'nullable|string|max:4',
            'pendidikan_ibu' => 'nullable|string|max:50',
            'pekerjaan_ibu' => 'nullable|string|max:100',
            'penghasilan_ibu' => 'nullable|string|max:50',
            'no_hp_ibu' => ['nullable', 'regex:/^(?:\+62|62|0)[0-9]{8,13}$/'],
            // Wali & Kontak
            'telepon_wali' => ['required', 'regex:/^(?:\+62|62|0)[0-9]{8,13}$/'],
            'tinggal_bersama' => 'nullable|string|max:50',
            'nama_wali' => 'nullable|string|max:150',
            'nik_wali' => 'nullable|digits:16',
            'tahun_lahir_wali' => 'nullable|string|max:4',
            'pendidikan_wali' => 'nullable|string|max:50',
            'pekerjaan_wali' => 'nullable|string|max:100',
            'penghasilan_wali' => 'nullable|string|max:50',
            'no_hp_wali' => ['nullable', 'regex:/^(?:\+62|62|0)[0-9]{8,13}$/'],
            'hubungan_wali' => 'nullable|string|max:50',
            'lembaga_tujuan_id' => 'nullable|exists:lembaga,id',
        ], [
            'nik.unique' => 'NIK calon santri sudah terdaftar. Gunakan NIK yang berbeda atau hubungi panitia.',
            'nik.digits' => 'NIK calon santri harus terdiri dari tepat 16 digit angka.',
            'kk.digits' => 'Nomor KK harus terdiri dari tepat 16 digit angka.',
            '*.digits' => 'Nomor identitas harus terdiri dari tepat 16 digit angka.',
            '*.regex' => 'Nomor telepon harus menggunakan format Indonesia yang valid, misalnya 081234567890.',
        ]);

        try {
            $calonSantri = DB::transaction(function () use ($validated) {
                $gelombang = GelombangPsb::whereKey($validated['gelombang_id'])
                    ->where('is_active', true)
                    ->whereDate('tanggal_buka', '<=', today())
                    ->whereDate('tanggal_tutup', '>=', today())
                    ->lockForUpdate()
                    ->first();

                if (!$gelombang) {
                    throw new \RuntimeException('Pendaftaran untuk gelombang ini sudah ditutup.');
                }

                if ($gelombang->kuota > 0 && $gelombang->pendaftar()->count() >= $gelombang->kuota) {
                    throw new \RuntimeException('Kuota pendaftaran untuk gelombang ini sudah penuh.');
                }

                if (
                    CalonSantri::where('nik', $validated['nik'])->lockForUpdate()->exists()
                    || Orang::where('nik', $validated['nik'])->lockForUpdate()->exists()
                ) {
                    throw new \RuntimeException('NIK calon santri sudah terdaftar. Gunakan NIK yang berbeda atau hubungi panitia.');
                }

                $data = $validated;
                $data['no_kk'] = $data['kk'] ?? null;
                unset($data['kk']);
                $data['status_workflow'] = 'DRAFT';

                return CalonSantri::create($data);
            });
            session()->forget(['captcha_answer', 'captcha_created_at']);

            if ($calonSantri->telepon_wali) {
                SendWhatsAppMessage::dispatch('registration_received', $calonSantri->telepon_wali, [
                    'santri_nama' => $calonSantri->nama_lengkap,
                    'no_pendaftaran' => $calonSantri->no_pendaftaran,
                    'status' => 'Draft',
                    'status_url' => route('frontend.psb.status', ['no_pendaftaran' => $calonSantri->no_pendaftaran]),
                ]);
            }

            return redirect()->route('frontend.psb.upload', ['no_pendaftaran' => $calonSantri->no_pendaftaran])
                ->with('success', 'Formulir berhasil disimpan. Silakan lanjutkan dengan mengunggah berkas persyaratan.');

        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        } catch (\Throwable $e) {
            Log::error('PSB registration failed', ['exception' => $e]);
            return back()->with('error', 'Terjadi kesalahan saat mendaftar. Silakan coba lagi atau hubungi panitia.')->withInput();
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
        DB::transaction(function () use ($request, $calonSantri, $berkasTypes) {
            foreach ($berkasTypes as $jenis) {
                if (!$request->hasFile($jenis)) {
                    continue;
                }

                $existingDoc = DokumenPsb::where('calon_santri_id', $calonSantri->id)
                    ->where('jenis_dokumen', $jenis)
                    ->first();
                $path = $request->file($jenis)->store('psb/dokumen/' . date('Y/m'), 'local');

                if ($existingDoc) {
                    if ($existingDoc->file_path) {
                        if (Storage::disk('local')->exists($existingDoc->file_path)) {
                            Storage::disk('local')->delete($existingDoc->file_path);
                        } elseif (Storage::disk('public')->exists($existingDoc->file_path)) {
                            Storage::disk('public')->delete($existingDoc->file_path);
                        }
                    }
                    $existingDoc->update(['file_path' => $path, 'is_verified' => false]);
                } else {
                    DokumenPsb::create([
                        'calon_santri_id' => $calonSantri->id,
                        'jenis_dokumen' => $jenis,
                        'file_path' => $path,
                        'is_verified' => false,
                    ]);
                }
            }

            $uploadedTypes = $calonSantri->dokumen()->pluck('jenis_dokumen')->all();
            $isComplete = !array_diff($berkasTypes, $uploadedTypes);
            $calonSantri->update([
                'status_workflow' => $isComplete ? 'MENUNGGU_VERIFIKASI' : 'TIDAK_LENGKAP',
            ]);
        });

        $calonSantri->refresh();
        if ($calonSantri->workflow_status === 'MENUNGGU_VERIFIKASI' && $calonSantri->telepon_wali) {
            SendWhatsAppMessage::dispatch('registration_received', $calonSantri->telepon_wali, [
                'santri_nama' => $calonSantri->nama_lengkap,
                'no_pendaftaran' => $calonSantri->no_pendaftaran,
                'status_url' => route('frontend.psb.status', ['no_pendaftaran' => $calonSantri->no_pendaftaran]),
            ]);
        }

        return redirect()->route('frontend.psb.selesai', ['no_pendaftaran' => $calonSantri->no_pendaftaran]);
    }

    public function status(Request $request)
    {
        $result = null;
        if ($request->filled('no_pendaftaran') && $request->filled('nik')) {
            $validated = $request->validate([
                'no_pendaftaran' => 'required|string|max:30',
                'nik' => 'required|digits:16',
            ]);

            $result = CalonSantri::where('no_pendaftaran', $validated['no_pendaftaran'])
                ->where('nik', $validated['nik'])
                ->first();
        }

        return view('frontend.psb.status', compact('result'));
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