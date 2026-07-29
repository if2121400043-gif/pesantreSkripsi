<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Orang;
use App\Models\RiwayatJabatanPegawai;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $query = Pegawai::with('orang');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('orang', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('niup', 'like', "%{$search}%");
            })->orWhere('nip', 'like', "%{$search}%")
              ->orWhere('nuptk', 'like', "%{$search}%");
        }

        if ($request->filled('jenis_pegawai')) {
            $query->where('jenis_pegawai', $request->jenis_pegawai);
        }

        // Filter berdasarkan tab status aktif/non-aktif
        $tab = $request->get('tab', 'aktif');
        if ($tab === 'nonaktif') {
            $query->where('is_active', false);
        } elseif ($tab === 'semua') {
            // Tampilkan semua
        } else {
            $query->where('is_active', true);
        }

        $pegawais = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        $countAktif = Pegawai::where('is_active', true)->count();
        $countNonAktif = Pegawai::where('is_active', false)->count();

        return view('admin.pegawai.index', compact('pegawais', 'tab', 'countAktif', 'countNonAktif'));
    }

    public function create(Request $request)
    {
        $selectedOrangId = $request->get('orang_id');
        
        // Get Orang that are not yet Pegawai
        $calonPegawai = Orang::whereDoesntHave('pegawai')
            ->where('is_active', true)
            ->orderBy('nama_lengkap')
            ->get();

        return view('admin.pegawai.create', compact('calonPegawai', 'selectedOrangId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'orang_id' => 'required|exists:orang,id|unique:pegawai,orang_id',
            'nip' => 'nullable|string|max:30',
            'nuptk' => 'nullable|string|size:16|unique:pegawai,nuptk',
            'jenis_pegawai' => 'required|in:GURU,USTADZ,PENGASUH,STAFF_ADMIN,TENAGA_KEBERSIHAN,KEAMANAN,LAINNYA',
            'jabatan' => 'nullable|string|max:100',
            'status_kepegawaian' => 'required|in:TETAP,KONTRAK,HONORER,SUKARELAWAN',
            'tanggal_masuk' => 'nullable|date',
            'pendidikan_terakhir' => 'nullable|in:SD,SMP,SMA,D1,D2,D3,S1,S2,S3',
            'jurusan_pendidikan' => 'nullable|string|max:100',
            'catatan' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Pegawai::create($validated);

        return redirect()->route('admin.pegawai.index')->with('success', 'Pegawai berhasil didaftarkan.');
    }

    public function show(Pegawai $pegawai)
    {
        $pegawai->load([
            'orang',
            'riwayatJabatan' => fn($q) => $q->orderBy('tanggal_mulai', 'desc'),
            'waliKelas.lembaga',
            'waliKelas.tahunPelajaran',
        ]);
        return view('admin.pegawai.show', compact('pegawai'));
    }

    public function edit(Pegawai $pegawai)
    {
        $pegawai->load('orang');
        return view('admin.pegawai.edit', compact('pegawai'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $validated = $request->validate([
            'nip' => 'nullable|string|max:30',
            'nuptk' => 'nullable|string|size:16|unique:pegawai,nuptk,' . $pegawai->id,
            'jenis_pegawai' => 'required|in:GURU,USTADZ,PENGASUH,STAFF_ADMIN,TENAGA_KEBERSIHAN,KEAMANAN,LAINNYA',
            'jabatan' => 'nullable|string|max:100',
            'status_kepegawaian' => 'required|in:TETAP,KONTRAK,HONORER,SUKARELAWAN',
            'tanggal_masuk' => 'nullable|date',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_masuk',
            'pendidikan_terakhir' => 'nullable|in:SD,SMP,SMA,D1,D2,D3,S1,S2,S3',
            'jurusan_pendidikan' => 'nullable|string|max:100',
            'catatan' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Auto-log perubahan jabatan ke riwayat_jabatan_pegawai
        $jabatanLama = $pegawai->jabatan;
        $jabatanBaru = $validated['jabatan'] ?? null;

        if ($jabatanLama && $jabatanBaru && $jabatanLama !== $jabatanBaru) {
            // Tutup jabatan lama
            RiwayatJabatanPegawai::where('pegawai_id', $pegawai->id)
                ->whereNull('tanggal_selesai')
                ->update(['tanggal_selesai' => now()->toDateString()]);

            // Catat jabatan baru
            RiwayatJabatanPegawai::create([
                'pegawai_id' => $pegawai->id,
                'jabatan' => $jabatanBaru,
                'jenis_pegawai' => $validated['jenis_pegawai'],
                'status_kepegawaian' => $validated['status_kepegawaian'],
                'tanggal_mulai' => now()->toDateString(),
                'keterangan' => 'Pindah jabatan dari: ' . $jabatanLama,
            ]);
        }

        $pegawai->update($validated);

        return redirect()->route('admin.pegawai.index')->with('success', 'Data Pegawai berhasil diperbarui.');
    }

    public function destroy(Pegawai $pegawai)
    {
        $pegawai->delete();
        return redirect()->route('admin.pegawai.index')->with('success', 'Data Pegawai berhasil dihapus.');
    }
}
