<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalonSantri;
use App\Models\GelombangPsb;
use App\Models\Lembaga;
use App\Models\Orang;
use App\Models\PesertaDidik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\HubunganKeluarga;
use App\Jobs\SendWhatsAppMessage;


class CalonSantriController extends Controller
{
    public function index(Request $request)
    {
        $query = CalonSantri::with(['gelombang', 'lembagaTujuan']);

        if ($request->filled('gelombang_id')) {
            $query->where('gelombang_id', $request->gelombang_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('no_pendaftaran', 'like', "%{$search}%");
            });
        }

        $calonSantris = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $gelombangs = GelombangPsb::orderBy('tanggal_buka', 'desc')->get();

        return view('admin.psb.calon_santri.index', compact('calonSantris', 'gelombangs'));
    }

    public function create()
    {
        $gelombangs = GelombangPsb::where('is_active', true)
            ->where('tanggal_tutup', '>=', now()->toDateString())
            ->get();
        $lembagas = Lembaga::where('is_active', true)->get();
        
        return view('admin.psb.calon_santri.create', compact('gelombangs', 'lembagas'));
    }

    // ── KODE YANG DIUBAH ──
    public function store(Request $request)
    {
        $validated = $request->validate([
            'gelombang_id' => 'required|exists:gelombang_psb,id',
            'nama_lengkap' => 'required|string|max:200',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'nik' => 'nullable|string|max:20|unique:calon_santri,nik|unique:orang,nik',
            'asal_sekolah' => 'nullable|string|max:200',
            'nisn' => 'nullable|string|max:20',
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
            'telepon_wali' => 'nullable|string|max:20',
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

        // Kini otomatis ditangani oleh Model Events (creating) di CalonSantri.php
        CalonSantri::create($validated);

        return redirect()->route('admin.psb.calon-santri.index')->with('success', 'Pendaftaran Calon Santri berhasil disimpan.');
    }
    // ──────────────────────

    public function show(CalonSantri $calonSantri)
    {
        $calonSantri->load(['gelombang', 'lembagaTujuan', 'verifikator', 'dokumen']);
        return view('admin.psb.calon_santri.show', compact('calonSantri'));
    }

    public function verifikasi(Request $request, CalonSantri $calonSantri)
    {
        $request->validate([
            'status' => 'required|in:HADIR_TES,DITERIMA,TIDAK_LULUS,DIBATALKAN',
            'catatan_verifikasi' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $calonSantri->status = $request->status;
            $calonSantri->catatan_verifikasi = $request->catatan_verifikasi;
            $calonSantri->diverifikasi_oleh = auth()->id();
            $calonSantri->tanggal_verifikasi = now();
            
            if ($request->status === 'DITERIMA') {
                // ═══════════════════════════════════════════════
                // TAHAP 1: Buat Profil Santri (Anak)
                // ═══════════════════════════════════════════════
                $orangSantri = Orang::create([
                    'nama_lengkap' => $calonSantri->nama_lengkap,
                    'jenis_kelamin' => $calonSantri->jenis_kelamin,
                    'tempat_lahir' => $calonSantri->tempat_lahir,
                    'tanggal_lahir' => $calonSantri->tanggal_lahir,
                    'nik' => $calonSantri->nik,
                    'no_kk' => $calonSantri->no_kk,
                    'alamat_lengkap' => $calonSantri->alamat,
                    'is_active' => true,
                ]);
                
                PesertaDidik::create([
                    'orang_id' => $orangSantri->id,
                    'nisn' => $calonSantri->nisn,
                    'status' => 'AKTIF',
                    'tanggal_masuk' => now()->toDateString(),
                    'catatan' => 'Asal sekolah: ' . ($calonSantri->asal_sekolah ?? '-'),
                ]);

                // ═══════════════════════════════════════════════
                // TAHAP 2: Buat/Temukan Profil Orang Tua & Wali
                // ═══════════════════════════════════════════════
                $isWaliMode = $calonSantri->tinggal_bersama === 'Wali';

                // --- Proses Data Ayah ---
                if ($calonSantri->nama_ayah) {
                    $orangAyah = $this->findOrCreateKeluarga(
                        $calonSantri->nama_ayah,
                        'L',
                        $calonSantri->nik_ayah,
                        $calonSantri->no_hp_ayah,
                        $calonSantri->tahun_lahir_ayah
                    );

                    HubunganKeluarga::create([
                        'orang_id' => $orangSantri->id,
                        'keluarga_id' => $orangAyah->id,
                        'hubungan' => 'AYAH',
                        'is_wali_utama' => !$isWaliMode, // Wali utama jika tinggal bersama Orang Tua
                        'is_mahrom' => true,
                        'boleh_jemput' => true,
                        'boleh_kunjungi' => true,
                        'boleh_komunikasi' => true,
                    ]);
                }

                // --- Proses Data Ibu ---
                if ($calonSantri->nama_ibu) {
                    $orangIbu = $this->findOrCreateKeluarga(
                        $calonSantri->nama_ibu,
                        'P',
                        $calonSantri->nik_ibu,
                        $calonSantri->no_hp_ibu,
                        $calonSantri->tahun_lahir_ibu
                    );

                    HubunganKeluarga::create([
                        'orang_id' => $orangSantri->id,
                        'keluarga_id' => $orangIbu->id,
                        'hubungan' => 'IBU',
                        'is_wali_utama' => false,
                        'is_mahrom' => true,
                        'boleh_jemput' => true,
                        'boleh_kunjungi' => true,
                        'boleh_komunikasi' => true,
                    ]);
                }

                // --- Proses Data Wali (hanya jika tinggal bersama Wali) ---
                if ($isWaliMode && $calonSantri->nama_wali) {
                    $orangWali = $this->findOrCreateKeluarga(
                        $calonSantri->nama_wali,
                        null, // Jenis kelamin wali tidak dikumpulkan di form
                        $calonSantri->nik_wali,
                        $calonSantri->no_hp_wali,
                        $calonSantri->tahun_lahir_wali
                    );

                    // Mapping frontend string to DB enum
                    $hubunganDb = 'WALI';
                    $hubunganInput = strtolower($calonSantri->hubungan_wali ?? '');
                    if (str_contains($hubunganInput, 'kakek') || str_contains($hubunganInput, 'nenek')) $hubunganDb = 'KAKEK';
                    elseif (str_contains($hubunganInput, 'paman') || str_contains($hubunganInput, 'bibi')) $hubunganDb = 'PAMAN';
                    elseif (str_contains($hubunganInput, 'kakak')) $hubunganDb = 'KAKAK';
                    elseif (str_contains($hubunganInput, 'adik')) $hubunganDb = 'ADIK';
                    elseif (str_contains($hubunganInput, 'lainnya') || str_contains($hubunganInput, 'saudara')) $hubunganDb = 'LAINNYA';

                    HubunganKeluarga::create([
                        'orang_id' => $orangSantri->id,
                        'keluarga_id' => $orangWali->id,
                        'hubungan' => $hubunganDb,
                        'is_wali_utama' => true, // Wali utama karena tinggal bersama Wali
                        'is_mahrom' => false,
                        'boleh_jemput' => true,
                        'boleh_kunjungi' => true,
                        'boleh_komunikasi' => true,
                    ]);
                }

                // ═══════════════════════════════════════════════
                // TAHAP 3: Auto-Generate Akun Wali & WhatsApp
                // ═══════════════════════════════════════════════
                $waliUtamaOrang = $isWaliMode ? ($orangWali ?? null) : ($orangAyah ?? $orangIbu ?? null);
                $noHpWali = $calonSantri->telepon_wali ?? $calonSantri->no_hp_ayah ?? $calonSantri->no_hp_ibu ?? $calonSantri->no_hp_wali;
                
                // Fallback: Jika pendaftar sengaja mengosongkan SELURUH nama orang tua/wali di form,
                // kita tetap buatkan profil "Wali" anonim agar mereka tetap mendapat akun Portal Wali.
                if (!$waliUtamaOrang && $noHpWali) {
                    $waliUtamaOrang = Orang::create([
                        'nama_lengkap' => 'Wali Dari ' . $calonSantri->nama_lengkap,
                        'jenis_kelamin' => 'L',
                        'telepon' => $noHpWali,
                        'is_active' => true,
                    ]);

                    HubunganKeluarga::create([
                        'orang_id' => $orangSantri->id,
                        'keluarga_id' => $waliUtamaOrang->id,
                        'hubungan' => 'WALI',
                        'is_wali_utama' => true,
                        'is_mahrom' => false,
                        'boleh_jemput' => true,
                        'boleh_kunjungi' => true,
                        'boleh_komunikasi' => true,
                    ]);
                }

                if ($waliUtamaOrang && $noHpWali) {
                    $existingUser = \App\Models\User::where('username', $noHpWali)->first();

                    if ($existingUser) {
                        // Linked Account
                        SendWhatsAppMessage::dispatch('welcome', $noHpWali, [
                            'santri_nama' => $orangSantri->nama_lengkap,
                            'username' => $noHpWali,
                            'password' => null,
                        ]);
                    } else {
                        // Generate new User
                        $passwordRaw = substr(preg_replace('/[^0-9]/', '', $noHpWali), -6);
                        if (strlen($passwordRaw) < 6) {
                            $passwordRaw = '123456'; // Fallback
                        }

                        $newUser = \App\Models\User::create([
                            'orang_id' => $waliUtamaOrang->id,
                            'username' => $noHpWali,
                            'email' => 'wali_' . $noHpWali . '@pesantren.local',
                            'password' => \Illuminate\Support\Facades\Hash::make($passwordRaw),
                            'is_active' => true,
                            'must_change_password' => true,
                        ]);

                        // Attach Role WALI_SANTRI
                        $roleWali = \App\Models\Role::where('nama', 'WALI_SANTRI')->first();
                        if ($roleWali) {
                            \App\Models\UserRole::create([
                                'user_id' => $newUser->id,
                                'role_id' => $roleWali->id,
                                'is_default' => true,
                                'is_active' => true,
                            ]);
                        }

                        // Call WhatsApp Notification
                        SendWhatsAppMessage::dispatch('welcome', $noHpWali, [
                            'santri_nama' => $orangSantri->nama_lengkap,
                            'username' => $noHpWali,
                            'password' => $passwordRaw,
                        ]);
                    }
                }

                // ═══════════════════════════════════════════════
                // TAHAP 4: Simpan & Commit
                // ═══════════════════════════════════════════════
                $calonSantri->save();
                
                DB::commit();

                // Susun pesan sukses dengan info akun wali
                $pesanSukses = "✅ Calon santri DITERIMA! Profil santri (NIUP: {$orangSantri->niup}) berhasil dibuat.";
                if (isset($passwordRaw) && isset($noHpWali)) {
                    $pesanSukses .= " | 🔑 AKUN WALI — Username: {$noHpWali} | Password: {$passwordRaw}";
                } elseif (isset($existingUser) && $existingUser) {
                    $pesanSukses .= " | ℹ️ Wali sudah memiliki akun (Username: {$noHpWali}). Santri otomatis terhubung.";
                }

                return back()->with('success', $pesanSukses);
            }

            $calonSantri->save();
            DB::commit();
            
            return back()->with('success', 'Status pendaftaran berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses verifikasi: ' . $e->getMessage());
        }
    }

    public function verifikasiDokumen(Request $request, CalonSantri $calonSantri, \App\Models\DokumenPsb $dokumen)
    {
        if ($dokumen->calon_santri_id !== $calonSantri->id) {
            abort(404);
        }

        $request->validate([
            'is_verified' => 'required|boolean'
        ]);

        $dokumen->update([
            'is_verified' => $request->is_verified
        ]);

        $status = $request->is_verified ? 'valid' : 'ditolak';
        return back()->with('success', 'Dokumen ' . str_replace('_', ' ', $dokumen->jenis_dokumen) . ' berhasil ditandai sebagai ' . $status . '.');
    }

    public function destroy(CalonSantri $calonSantri)
    {
        $calonSantri->delete();
        return redirect()->route('admin.psb.calon-santri.index')->with('success', 'Data calon santri berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════════
    // Helper: Cari atau Buat profil Orang Tua/Wali di master_orang
    // Antisipasi duplikasi: cek NIK dulu, lalu Nomor HP.
    // ══════════════════════════════════════════════════════════════
    private function findOrCreateKeluarga(
        string $nama,
        ?string $jenisKelamin,
        ?string $nik,
        ?string $telepon,
        ?string $tahunLahir
    ): Orang {
        // 1. Cek berdasarkan NIK (paling akurat)
        if ($nik) {
            $existing = Orang::where('nik', $nik)->first();
            if ($existing) {
                return $existing;
            }
        }

        // 2. Cek berdasarkan Nomor HP (fallback jika NIK kosong)
        if ($telepon) {
            $existing = Orang::where('telepon', $telepon)->first();
            if ($existing) {
                return $existing;
            }
        }

        // 3. Tidak ditemukan → Buat profil baru (NIUP otomatis di-generate oleh Model Events)
        return Orang::create([
            'nama_lengkap' => $nama,
            'jenis_kelamin' => $jenisKelamin ?? 'L',
            'nik' => $nik,
            'telepon' => $telepon,
            'tanggal_lahir' => $tahunLahir ? "{$tahunLahir}-01-01" : null,
            'is_active' => true,
        ]);
    }
}