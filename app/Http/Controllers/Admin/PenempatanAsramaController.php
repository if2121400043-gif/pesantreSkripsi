<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asrama;
use App\Models\Kamar;
use App\Models\TahunPelajaran;
use App\Models\PesertaDidik;
use App\Models\PesertaMukimTahun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class PenempatanAsramaController extends Controller
{
    public function index(Request $request)
    {
        $asramaId = $request->get('asrama_id');
        $tahunId = $request->get('tahun_pelajaran_id');

        $asramas = Asrama::where('is_active', true)->orderBy('nama')->get();
        $tahuns = TahunPelajaran::orderBy('tanggal_mulai', 'desc')->get();
        
        $kamars = [];
        $pesertaBelumDitempatkan = [];
        $selectedAsrama = null;

        if ($asramaId && $tahunId) {
            $selectedAsrama = Asrama::find($asramaId);
            
            // Get Kamar for this Asrama, and count how many santri are already placed inside it for the selected year
            $kamars = Kamar::where('asrama_id', $asramaId)
                ->where('is_active', true)
                ->withCount(['penghuni as terisi_count' => function ($q) use ($tahunId) {
                    $q->where('tahun_pelajaran_id', $tahunId)
                      ->where('status_mukim', 'MUKIM');
                }])
                ->orderBy('nama')
                ->get();

            // Get active Santri who are NOT yet placed in ANY Kamar for this year
            $query = PesertaDidik::with('orang')
                ->where('status', 'AKTIF')
                ->whereDoesntHave('riwayatMukim', function ($q) use ($tahunId) {
                    $q->where('tahun_pelajaran_id', $tahunId)
                      ->where('status_mukim', 'MUKIM');
                });

            // IMPORTANT: Filter gender to match Asrama
            if ($selectedAsrama && $selectedAsrama->jenis_kelamin !== 'CAMPURAN') {
                $query->whereHas('orang', function ($q) use ($selectedAsrama) {
                    $q->where('jenis_kelamin', $selectedAsrama->jenis_kelamin);
                });
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('orang', function($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('niup', 'like', "%{$search}%");
                });
            }

            $pesertaBelumDitempatkan = $query->get();
        }

        return view('admin.asrama.penempatan.index', compact(
            'asramas', 'tahuns', 'kamars', 'pesertaBelumDitempatkan', 
            'asramaId', 'tahunId', 'selectedAsrama'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kamar_id' => 'required|exists:kamar,id',
            'tahun_pelajaran_id' => 'required|exists:tahun_pelajaran,id',
            'peserta_ids' => 'required|array|min:1',
            'peserta_ids.*' => 'exists:peserta_didik,id'
        ]);

        $kamarId = $request->kamar_id;
        $tahunId = $request->tahun_pelajaran_id;
        $pesertaIds = $request->peserta_ids;

        // Retrieve Kamar and its parent Asrama
        $kamar = Kamar::with('asrama')->findOrFail($kamarId);
        $asrama = $kamar->asrama;

        // 1. Backend Gender Validation
        if ($asrama->jenis_kelamin !== 'CAMPURAN') {
            $invalidGenderCount = PesertaDidik::whereIn('id', $pesertaIds)
                ->whereHas('orang', function($q) use ($asrama) {
                    $q->where('jenis_kelamin', '!=', $asrama->jenis_kelamin);
                })->count();

            if ($invalidGenderCount > 0) {
                return back()->with('error', 'Validasi Gagal: Terdapat santri dengan jenis kelamin yang tidak sesuai dengan peruntukan asrama (' . $asrama->jenis_kelamin . ').');
            }
        }

        DB::beginTransaction();
        try {
            // Lock the Kamar row to prevent race conditions during capacity calculation
            // We lock the Kamar so no other transaction can process assignments for this Kamar concurrently
            $lockedKamar = Kamar::where('id', $kamarId)->lockForUpdate()->first();

            // Check current filled capacity
            $terisi = PesertaMukimTahun::where('kamar_id', $kamarId)
                ->where('tahun_pelajaran_id', $tahunId)
                ->where('status_mukim', 'MUKIM')
                ->count();

            // 2. Exact Capacity Validation (Prevent Overcapacity)
            $sisaKapasitas = max(0, $lockedKamar->kapasitas - $terisi);
            $jumlahAkanMasuk = count($pesertaIds);

            if ($jumlahAkanMasuk > $sisaKapasitas) {
                DB::rollBack();
                return back()->with('error', "Kapasitas penuh! Kamar ini hanya tersisa {$sisaKapasitas} kasur, tetapi Anda mencoba memasukkan {$jumlahAkanMasuk} santri.");
            }

            // Perform Bulk Insert/Update
            $count = 0;
            foreach ($pesertaIds as $pesertaId) {
                // Ensure the santri isn't somehow already placed in another Kamar (race condition safety)
                $existing = PesertaMukimTahun::where('peserta_didik_id', $pesertaId)
                    ->where('tahun_pelajaran_id', $tahunId)
                    ->where('status_mukim', 'MUKIM')
                    ->first();

                if ($existing) {
                    // Skip or we could abort. For safety, we skip and let them know.
                    continue; 
                }

                PesertaMukimTahun::create([
                    'peserta_didik_id' => $pesertaId,
                    'tahun_pelajaran_id' => $tahunId,
                    'kamar_id' => $kamarId,
                    'status_mukim' => 'MUKIM',
                    'tanggal_masuk' => now()->format('Y-m-d')
                ]);
                $count++;
            }

            DB::commit();

            if ($count === 0) {
                return back()->with('warning', 'Tidak ada santri yang berhasil ditempatkan (mungkin sudah mendapat kamar).');
            }

            return back()->with('success', "Berhasil menempatkan {$count} santri ke Kamar {$lockedKamar->nama}.");

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'peserta_mukim_id' => 'required|exists:peserta_mukim_tahun,id'
        ]);

        $riwayat = PesertaMukimTahun::findOrFail($request->peserta_mukim_id);
        
        // Logically we can just delete the record if it's an erroneous placement,
        // or set status_mukim to TIDAK_MUKIM if they move out.
        // For simplicity (as per user approval "Keluarkan -> Masukkan baru"), we delete.
        $riwayat->delete();

        return back()->with('success', 'Santri berhasil dikeluarkan dari kamar.');
    }
}
