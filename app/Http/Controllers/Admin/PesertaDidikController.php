<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PesertaDidik;
use App\Models\Orang;
use App\Models\Lembaga;
use App\Models\TahunPelajaran;
use App\Models\RiwayatStatusSantri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PesertaDidikController extends Controller
{
    public function index(Request $request)
    {
        $query = PesertaDidik::with('orang');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('orang', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('niup', 'like', "%{$search}%");
            })->orWhere('nisn', 'like', "%{$search}%")
              ->orWhere('nis', 'like', "%{$search}%");
        }

        // Filter berdasarkan tab status
        $tab = $request->get('tab', 'aktif');
        if ($tab === 'alumni') {
            $query->where('status', 'LULUS');
        } elseif ($tab === 'keluar') {
            $query->whereIn('status', ['MUTASI_KELUAR', 'DIKELUARKAN', 'MENGUNDURKAN_DIRI', 'MENINGGAL']);
        } elseif ($tab === 'semua') {
            // Tampilkan semua — tidak ada filter
        } else {
            $query->where('status', 'AKTIF');
        }

        // Filter lembaga (dari riwayat rombel)
        if ($request->filled('lembaga_id')) {
            $query->whereHas('riwayatRombel.rombel', function($q) use ($request) {
                $q->where('lembaga_id', $request->lembaga_id);
            });
        }

        // Filter angkatan (tahun masuk)
        if ($request->filled('angkatan')) {
            $query->whereYear('tanggal_masuk', $request->angkatan);
        }

        $pesertaDidiks = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $lembagas = Lembaga::where('is_active', true)->orderBy('urutan')->get();

        // Hitung jumlah per tab untuk badge
        $countAktif = PesertaDidik::where('status', 'AKTIF')->count();
        $countAlumni = PesertaDidik::where('status', 'LULUS')->count();
        $countKeluar = PesertaDidik::whereIn('status', ['MUTASI_KELUAR', 'DIKELUARKAN', 'MENGUNDURKAN_DIRI', 'MENINGGAL'])->count();

        return view('admin.peserta_didik.index', compact('pesertaDidiks', 'lembagas', 'tab', 'countAktif', 'countAlumni', 'countKeluar'));
    }

    public function create(Request $request)
    {
        // If an orang_id is passed, we select them by default
        $selectedOrangId = $request->get('orang_id');
        
        // Get Orang that are not yet Peserta Didik
        $calonPeserta = Orang::whereDoesntHave('pesertaDidik')
            ->where('is_active', true)
            ->orderBy('nama_lengkap')
            ->get();

        return view('admin.peserta_didik.create', compact('calonPeserta', 'selectedOrangId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'orang_id' => 'required|exists:orang,id|unique:peserta_didik,orang_id',
            'nis' => 'nullable|string|max:30',
            'nisn' => 'nullable|string|size:10|unique:peserta_didik,nisn',
            'tanggal_masuk' => 'required|date',
            'status' => 'required|in:AKTIF,LULUS,MUTASI_KELUAR,DIKELUARKAN,MENGUNDURKAN_DIRI,MENINGGAL',
            'catatan' => 'nullable|string',
        ]);

        $pesertaDidik = PesertaDidik::create($validated);

        // Auto-create User account for login if not exist
        $orang = Orang::find($validated['orang_id']);
        if ($orang && !$orang->user) {
            $username = strtolower(str_replace(['-', ' '], '', $orang->niup));
            $user = \App\Models\User::create([
                'orang_id' => $orang->id,
                'username' => $username,
                'email' => $orang->email ?? ($username . '@nurulfurqon.app'),
                'password' => \Illuminate\Support\Facades\Hash::make('pesantren2026'),
                'is_active' => true,
            ]);

            $santriRole = \App\Models\Role::where('nama', 'SANTRI')->first();
            if ($santriRole) {
                \App\Models\UserRole::create([
                    'user_id' => $user->id,
                    'role_id' => $santriRole->id,
                    'is_default' => true,
                    'is_active' => true,
                ]);
            }
        }

        return redirect()->route('admin.peserta-didik.index')->with('success', 'Peserta Didik berhasil didaftarkan. Akun login otomatis dibuat (Password default: pesantren2026).');
    }

    public function show(PesertaDidik $pesertaDidik)
    {
        $pesertaDidik->load([
            'orang.desa.kecamatan.kabupaten.provinsi',
            'riwayatLembaga.lembaga', 
            'riwayatLembaga.tahunPelajaran',
            'riwayatRombel.rombel.lembaga',
            'riwayatRombel.tahunPelajaran',
            'riwayatMukim.kamar.asrama',
            'riwayatMukim.tahunPelajaran',
            'riwayatStatus.pengubah',
        ]);

        // Data keluarga
        $keluarga = \App\Models\HubunganKeluarga::with('orangTuaAtauWali')
            ->where('orang_id', $pesertaDidik->orang_id)
            ->get();

        return view('admin.peserta_didik.show', compact('pesertaDidik', 'keluarga'));
    }

    public function edit(PesertaDidik $pesertaDidik)
    {
        $pesertaDidik->load('orang');
        return view('admin.peserta_didik.edit', compact('pesertaDidik'));
    }

    public function update(Request $request, PesertaDidik $pesertaDidik)
    {
        $validated = $request->validate([
            'nis' => 'nullable|string|max:30',
            'nisn' => 'nullable|string|size:10|unique:peserta_didik,nisn,' . $pesertaDidik->id,
            'tanggal_masuk' => 'required|date',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_masuk',
            'status' => 'required|in:AKTIF,LULUS,MUTASI_KELUAR,DIKELUARKAN,MENGUNDURKAN_DIRI,MENINGGAL',
            'catatan' => 'nullable|string',
            'keterangan_status' => 'nullable|string|max:500',
        ]);

        // Auto-log perubahan status ke riwayat_status_santri
        $statusLama = $pesertaDidik->status;
        $statusBaru = $validated['status'];

        if ($statusLama !== $statusBaru) {
            RiwayatStatusSantri::create([
                'peserta_didik_id' => $pesertaDidik->id,
                'status_lama' => $statusLama,
                'status_baru' => $statusBaru,
                'tanggal_perubahan' => now()->toDateString(),
                'keterangan' => $validated['keterangan_status'] ?? null,
                'diubah_oleh' => Auth::id(),
            ]);

            // Jika status bukan AKTIF, set tanggal_keluar otomatis
            if ($statusBaru !== 'AKTIF' && empty($validated['tanggal_keluar'])) {
                $validated['tanggal_keluar'] = now()->toDateString();
            }
        }

        unset($validated['keterangan_status']);
        $pesertaDidik->update($validated);

        return redirect()->route('admin.peserta-didik.index')->with('success', 'Data Peserta Didik berhasil diperbarui.');
    }

    public function destroy(PesertaDidik $pesertaDidik)
    {
        $pesertaDidik->delete();
        return redirect()->route('admin.peserta-didik.index')->with('success', 'Data Peserta Didik berhasil dihapus.');
    }
}
