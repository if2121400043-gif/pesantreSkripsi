<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PesertaDidik;
use App\Models\TahunPelajaran;
use App\Models\Kamar;
use App\Models\Rombel;
use App\Models\RiwayatMutasi;
use App\Models\PesertaMukimTahun;
use App\Models\RiwayatRombelPeserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MutasiSantriController extends Controller
{
    public function index(Request $request)
    {
        $query = RiwayatMutasi::with(['pesertaDidik.orang', 'tahunPelajaran', 'operator']);

        if ($request->filled('jenis_mutasi')) {
            $query->where('jenis_mutasi', $request->jenis_mutasi);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pesertaDidik.orang', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $mutasis = $query->orderBy('tanggal_mutasi', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->paginate(15)
                         ->withQueryString();

        return view('admin.mutasi.index', compact('mutasis'));
    }

    public function create()
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        if (!$tahunAktif) {
            return redirect()->route('admin.dashboard')->with('error', 'Silakan aktifkan Tahun Pelajaran terlebih dahulu.');
        }

        // Fetch active students with their current rooms and classes loaded
        $santris = PesertaDidik::with(['orang', 'riwayatMukim' => function ($q) use ($tahunAktif) {
            $q->where('tahun_pelajaran_id', $tahunAktif->id)->where('status_mukim', 'MUKIM')->with('kamar.asrama');
        }, 'riwayatRombel' => function ($q) use ($tahunAktif) {
            $q->where('tahun_pelajaran_id', $tahunAktif->id)->where('status', 'AKTIF')->with('rombel.lembaga');
        }])->where('status', 'AKTIF')->get();

        // Fetch rooms with currently calculated occupant counts
        $kamars = Kamar::where('is_active', true)->with('asrama')->get()->map(function ($kamar) use ($tahunAktif) {
            $occupantCount = PesertaMukimTahun::where('kamar_id', $kamar->id)
                ->where('status_mukim', 'MUKIM')
                ->where('tahun_pelajaran_id', $tahunAktif->id)
                ->count();
            
            $kamar->occupants_count = $occupantCount;
            return $kamar;
        });

        // Fetch classes with currently calculated student counts
        $rombels = Rombel::where('tahun_pelajaran_id', $tahunAktif->id)->with('lembaga')->get()->map(function ($rombel) use ($tahunAktif) {
            $studentCount = RiwayatRombelPeserta::where('rombel_id', $rombel->id)
                ->where('status', 'AKTIF')
                ->where('tahun_pelajaran_id', $tahunAktif->id)
                ->count();

            $rombel->students_count = $studentCount;
            return $rombel;
        });

        return view('admin.mutasi.create', compact('tahunAktif', 'santris', 'kamars', 'rombels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'peserta_didik_id' => 'required|exists:peserta_didik,id',
            'jenis_mutasi' => 'required|in:ASRAMA,ROMBEL',
            'kamar_id' => 'required_if:jenis_mutasi,ASRAMA|nullable|exists:kamar,id',
            'rombel_id' => 'required_if:jenis_mutasi,ROMBEL|nullable|exists:rombel,id',
            'tanggal_mutasi' => 'required|date',
            'keterangan' => 'nullable|string',
        ], [
            'kamar_id.required_if' => 'Kamar tujuan wajib diisi jika jenis mutasi adalah Asrama.',
            'rombel_id.required_if' => 'Kelas tujuan wajib diisi jika jenis mutasi adalah Rombongan Belajar.',
        ]);

        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        if (!$tahunAktif) {
            return back()->with('error', 'Tahun pelajaran aktif tidak ditemukan.');
        }

        try {
            DB::beginTransaction();

            $student = PesertaDidik::findOrFail($request->peserta_didik_id);
            $dariPosisi = '';
            $kePosisi = '';

            if ($request->jenis_mutasi === 'ASRAMA') {
                $kamar = Kamar::with('asrama')->findOrFail($request->kamar_id);
                
                // 1. Verify room capacity
                $occupantCount = PesertaMukimTahun::where('kamar_id', $kamar->id)
                    ->where('status_mukim', 'MUKIM')
                    ->where('tahun_pelajaran_id', $tahunAktif->id)
                    ->count();

                if ($occupantCount >= $kamar->kapasitas) {
                    return back()->withInput()->with('error', "Kamar {$kamar->nama} sudah penuh (Kapasitas: {$kamar->kapasitas} orang).");
                }

                // 2. Fetch current active placement
                $currentPlacement = PesertaMukimTahun::with('kamar.asrama')
                    ->where('peserta_didik_id', $student->id)
                    ->where('tahun_pelajaran_id', $tahunAktif->id)
                    ->first();

                if ($currentPlacement && $currentPlacement->kamar) {
                    $dariPosisi = "Asrama " . $currentPlacement->kamar->asrama->nama . " - Kamar " . $currentPlacement->kamar->nama;
                    
                    // Close the old placement / change room
                    $currentPlacement->update([
                        'kamar_id' => $kamar->id,
                        'status_mukim' => 'MUKIM',
                        'tanggal_masuk' => $request->tanggal_mutasi,
                        'keterangan' => 'Mutasi dari ' . $dariPosisi . '. ' . $request->keterangan
                    ]);
                } else {
                    $dariPosisi = "Belum Bermukim";
                    
                    PesertaMukimTahun::create([
                        'peserta_didik_id' => $student->id,
                        'tahun_pelajaran_id' => $tahunAktif->id,
                        'kamar_id' => $kamar->id,
                        'status_mukim' => 'MUKIM',
                        'tanggal_masuk' => $request->tanggal_mutasi,
                        'keterangan' => 'Penempatan pertama lewat mutasi. ' . $request->keterangan
                    ]);
                }

                $kePosisi = "Asrama " . $kamar->asrama->nama . " - Kamar " . $kamar->nama;

            } elseif ($request->jenis_mutasi === 'ROMBEL') {
                $rombel = Rombel::with('lembaga')->findOrFail($request->rombel_id);

                // 1. Verify class capacity
                $studentCount = RiwayatRombelPeserta::where('rombel_id', $rombel->id)
                    ->where('status', 'AKTIF')
                    ->where('tahun_pelajaran_id', $tahunAktif->id)
                    ->count();

                if ($studentCount >= $rombel->kapasitas) {
                    return back()->withInput()->with('error', "Kelas {$rombel->nama} sudah penuh (Kapasitas: {$rombel->kapasitas} siswa).");
                }

                // 2. Fetch current class placement
                $currentRombel = RiwayatRombelPeserta::with('rombel.lembaga')
                    ->where('peserta_didik_id', $student->id)
                    ->where('tahun_pelajaran_id', $tahunAktif->id)
                    ->where('status', 'AKTIF')
                    ->first();

                if ($currentRombel) {
                    $dariPosisi = $currentRombel->rombel->lembaga->singkatan . " - Kelas " . $currentRombel->rombel->nama;
                    
                    // Mark old placement as PINDAH
                    $currentRombel->update([
                        'status' => 'PINDAH',
                        'tanggal_keluar' => $request->tanggal_mutasi
                    ]);
                } else {
                    $dariPosisi = "Belum Ada Kelas";
                }

                // 3. Create new class placement
                RiwayatRombelPeserta::create([
                    'peserta_didik_id' => $student->id,
                    'rombel_id' => $rombel->id,
                    'tahun_pelajaran_id' => $tahunAktif->id,
                    'tanggal_masuk' => $request->tanggal_mutasi,
                    'status' => 'AKTIF'
                ]);

                $kePosisi = $rombel->lembaga->singkatan . " - Kelas " . $rombel->nama;
            }

            // 3. Insert mutation log
            RiwayatMutasi::create([
                'peserta_didik_id' => $student->id,
                'tahun_pelajaran_id' => $tahunAktif->id,
                'jenis_mutasi' => $request->jenis_mutasi,
                'dari_posisi' => $dariPosisi,
                'ke_posisi' => $kePosisi,
                'tanggal_mutasi' => $request->tanggal_mutasi,
                'keterangan' => $request->keterangan,
                'diinput_oleh' => auth()->id()
            ]);

            DB::commit();

            return redirect()->route('admin.mutasi.index')->with('success', 'Mutasi santri berhasil dilakukan dan tercatat di log historis.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(RiwayatMutasi $mutasi)
    {
        // For security and Skripsi auditing, deleting history is not generally allowed, but we can support it.
        $mutasi->delete();
        return redirect()->route('admin.mutasi.index')->with('success', 'Log riwayat mutasi berhasil dihapus dari database.');
    }
}
