<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HubunganKeluarga;
use App\Models\Orang;
use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
use App\Services\WhatsAppService;
use App\Jobs\SendWhatsAppMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KeluargaController extends Controller
{
    public function index(Request $request)
    {
        $query = HubunganKeluarga::with(['anak', 'orangTuaAtauWali']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('anak', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%");
            })->orWhereHas('orangTuaAtauWali', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%");
            });
        }

        $hubungannya = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        $semuaOrang = Orang::where('is_active', true)->orderBy('nama_lengkap')->get(['id', 'nama_lengkap', 'niup']);

        return view('admin.keluarga.index', compact('hubungannya', 'semuaOrang'));
    }

    public function create()
    {
        $semuaOrang = Orang::where('is_active', true)->orderBy('nama_lengkap')->get(['id', 'nama_lengkap', 'niup']);
        return view('admin.keluarga.create', compact('semuaOrang'));
    }

    public function store(Request $request)
    {
        $mode = $request->input('mode_wali', 'existing');

        if ($mode === 'new') {
            $validated = $request->validate([
                'orang_id' => 'required|exists:orang,id',
                'nama_wali' => 'required|string|max:200',
                'telepon_wali' => 'required|string|max:20',
                'email_wali' => 'nullable|email|max:255',
                'alamat_wali' => 'nullable|string',
                'hubungan' => 'required|in:AYAH,IBU,WALI,KAKAK,ADIK,PAMAN,BIBI,KAKEK,NENEK,LAINNYA',
                'catatan' => 'nullable|string',
            ]);

            DB::beginTransaction();
            try {
                // 1. Create Orang untuk Wali
                $wali = Orang::create([
                    'nama_lengkap' => $validated['nama_wali'],
                    'telepon' => $validated['telepon_wali'],
                    'alamat_lengkap' => $validated['alamat_wali'],
                    'jenis_kelamin' => in_array($validated['hubungan'], ['AYAH', 'KAKEK', 'PAMAN', 'KAKAK']) ? 'L' : 'P',
                ]);

                // 2. Create User Portal
                $user = User::create([
                    'orang_id' => $wali->id,
                    'username' => $validated['telepon_wali'],
                    'email' => $validated['email_wali'] ?? null,
                    'password' => Hash::make($validated['telepon_wali']),
                    'is_active' => true,
                ]);

                // Assign Role WALI_SANTRI
                $role = Role::where('nama', 'WALI_SANTRI')->first();
                if ($role) {
                    UserRole::create([
                        'user_id' => $user->id,
                        'role_id' => $role->id,
                        'is_active' => true,
                        'is_default' => true,
                    ]);
                }

                // 3. Set Hubungan
                $keluarga_id = $wali->id;

                $santri = Orang::find($validated['orang_id']);
                $portalUrl = url('/portal/beranda');
                $pesan = "Assalamu'alaikum Wr. Wb.\n\nYth. Bapak/Ibu {$wali->nama_lengkap},\nData Anda telah didaftarkan sebagai Wali Santri dari ananda *{$santri->nama_lengkap}*.\n\nBerikut adalah akses Portal Wali Anda:\nLogin: {$portalUrl}\nUsername: {$user->username}\nPassword: {$validated['telepon_wali']}\n\nHarap segera login dan mengubah password Anda demi keamanan.\n\nJazakumullahu Khairan.";
                SendWhatsAppMessage::dispatch('custom', $wali->telepon, ['message' => $pesan]);

            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Gagal membuat Wali Baru: ' . $e->getMessage());
            }
        } else {
            $request->validate([
                'orang_id' => 'required|exists:orang,id',
                'keluarga_id' => 'required|exists:orang,id|different:orang_id',
                'hubungan' => 'required|in:AYAH,IBU,WALI,KAKAK,ADIK,PAMAN,BIBI,KAKEK,NENEK,LAINNYA',
                'catatan' => 'nullable|string',
            ]);
            $keluarga_id = $request->keluarga_id;
        }

        // Common logic for both modes
        $isWaliUtama = $request->has('is_wali_utama');
        if ($isWaliUtama) {
            HubunganKeluarga::where('orang_id', $request->orang_id)->update(['is_wali_utama' => false]);
        }

        $exists = HubunganKeluarga::where('orang_id', $request->orang_id)
                                 ->where('keluarga_id', $keluarga_id)
                                 ->exists();
        
        if ($exists) {
            if ($mode === 'new') DB::rollBack();
            return back()->with('error', 'Relasi antara dua orang ini sudah ada.');
        }

        HubunganKeluarga::create([
            'orang_id' => $request->orang_id,
            'keluarga_id' => $keluarga_id,
            'hubungan' => $request->input('hubungan'),
            'catatan' => $request->input('catatan'),
            'is_mahrom' => $request->has('is_mahrom'),
            'boleh_jemput' => $request->has('boleh_jemput'),
            'boleh_kunjungi' => $request->has('boleh_kunjungi'),
            'boleh_komunikasi' => $request->has('boleh_komunikasi'),
            'is_wali_utama' => $isWaliUtama,
        ]);

        if ($mode === 'new') {
            DB::commit();
            return redirect()->route('admin.keluarga.index')->with('success', 'Wali baru berhasil dibuat dan direlasikan, serta akun portal telah dikirim via WhatsApp.');
        }

        return redirect()->route('admin.keluarga.index')->with('success', 'Relasi keluarga berhasil ditambahkan.');
    }

    public function editWali(Orang $orang)
    {
        return view('admin.keluarga.edit_wali', compact('orang'));
    }

    public function updateWali(Request $request, Orang $orang)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:200',
            'telepon' => 'required|string|max:20',
            'alamat_lengkap' => 'nullable|string',
        ]);

        $orang->update($validated);

        return redirect()->route('admin.keluarga.index')->with('success', 'Profil Wali berhasil diperbarui.');
    }

    public function resetPassword(Request $request, Orang $orang)
    {
        if (empty($orang->telepon)) {
            return back()->with('error', 'Gagal mereset password: Wali ini tidak memiliki nomor telepon.');
        }

        $user = User::where('orang_id', $orang->id)->first();
        
        if (!$user) {
            // Create user if not exists
            $user = User::create([
                'orang_id' => $orang->id,
                'username' => $orang->telepon,
                'password' => Hash::make($orang->telepon),
                'is_active' => true,
            ]);

            $role = Role::where('nama', 'WALI_SANTRI')->first();
            if ($role) {
                UserRole::create([
                    'user_id' => $user->id,
                    'role_id' => $role->id,
                    'is_active' => true,
                    'is_default' => true,
                ]);
            }
            $message = 'Akun Portal berhasil dibuat dan password di-set ke nomor HP.';
        } else {
            $user->update([
                'password' => Hash::make($orang->telepon),
            ]);
            $message = 'Password berhasil direset ke nomor HP wali.';
        }

        return back()->with('success', $message);
    }

    public function update(Request $request, HubunganKeluarga $keluarga)
    {
        $validated = $request->validate([
            'hubungan' => 'required|in:AYAH,IBU,WALI,KAKAK,ADIK,PAMAN,BIBI,KAKEK,NENEK,LAINNYA',
            'catatan' => 'nullable|string',
        ]);

        $validated['is_mahrom'] = $request->has('is_mahrom');
        $validated['boleh_jemput'] = $request->has('boleh_jemput');
        $validated['boleh_kunjungi'] = $request->has('boleh_kunjungi');
        $validated['boleh_komunikasi'] = $request->has('boleh_komunikasi');
        
        $isWaliUtama = $request->has('is_wali_utama');
        if ($isWaliUtama && !$keluarga->is_wali_utama) {
            HubunganKeluarga::where('orang_id', $keluarga->orang_id)->update(['is_wali_utama' => false]);
        }
        $validated['is_wali_utama'] = $isWaliUtama;

        $keluarga->update($validated);

        return redirect()->route('admin.keluarga.index')->with('success', 'Relasi keluarga berhasil diperbarui.');
    }

    public function destroy(HubunganKeluarga $keluarga)
    {
        $keluarga->delete();
        return back()->with('success', 'Relasi keluarga berhasil dihapus.');
    }
}
