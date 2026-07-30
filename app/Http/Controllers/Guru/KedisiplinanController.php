<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\PesertaDidik;
use App\Models\JenisPelanggaran;
use App\Models\CatatanPelanggaran;
use App\Models\CatatanPrestasi;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;

class KedisiplinanController extends Controller
{
    private function getPegawai()
    {
        $user = auth()->user();
        return $user && $user->orang_id ? Pegawai::where('orang_id', $user->orang_id)->first() : null;
    }

    public function index()
    {
        $pegawai = $this->getPegawai();
        if (!$pegawai) return redirect()->route('guru.dashboard');

        // Untuk form pencarian/pilihan santri, lebih baik menggunakan Select2 via ajax di view
        // Tapi untuk simplifikasi form, kita sediakan data awalnya.
        $jenisPelanggaran = JenisPelanggaran::all();
        
        // Ambil tahun pelajaran aktif
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        // Riwayat pencatatan oleh guru ini
        $riwayatPelanggaran = CatatanPelanggaran::with(['pesertaDidik.orang', 'jenisPelanggaran'])
            ->where('dicatat_oleh', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        $riwayatPrestasi = CatatanPrestasi::with('pesertaDidik.orang')
            ->where('dicatat_oleh', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('guru.kedisiplinan.index', compact('jenisPelanggaran', 'tahunAktif', 'riwayatPelanggaran', 'riwayatPrestasi'));
    }

    public function storePelanggaran(Request $request)
    {
        $request->validate([
            'peserta_didik_id' => 'required|exists:peserta_didik,id',
            'jenis_pelanggaran_id' => 'required|exists:jenis_pelanggaran,id',
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
            'tindakan' => 'nullable|string',
        ]);

        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        CatatanPelanggaran::create([
            'peserta_didik_id' => $request->peserta_didik_id,
            'jenis_pelanggaran_id' => $request->jenis_pelanggaran_id,
            'tahun_pelajaran_id' => $tahunAktif ? $tahunAktif->id : null,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'tindakan' => $request->tindakan,
            'dicatat_oleh' => auth()->id(),
        ]);

        return redirect()->route('guru.kedisiplinan.index')->with('success', 'Catatan pelanggaran berhasil disimpan.');
    }

    public function storePrestasi(Request $request)
    {
        $request->validate([
            'peserta_didik_id' => 'required|exists:peserta_didik,id',
            'tanggal' => 'required|date',
            'nama_prestasi' => 'required|string|max:255',
            'tingkat' => 'required|string|max:100',
            'penyelenggara' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        CatatanPrestasi::create([
            'peserta_didik_id' => $request->peserta_didik_id,
            'tahun_pelajaran_id' => $tahunAktif ? $tahunAktif->id : null,
            'tanggal' => $request->tanggal,
            'nama_prestasi' => $request->nama_prestasi,
            'tingkat' => $request->tingkat,
            'penyelenggara' => $request->penyelenggara,
            'keterangan' => $request->keterangan,
            'dicatat_oleh' => auth()->id(),
        ]);

        return redirect()->route('guru.kedisiplinan.index')->with('success', 'Catatan prestasi berhasil disimpan.');
    }

    public function searchSantri(Request $request)
    {
        $search = $request->get('q');
        
        $santri = PesertaDidik::with('orang')
            ->where('status', 'AKTIF')
            ->whereHas('orang', function($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                  ->orWhere('nisn', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'text' => ($item->orang->nama ?? '-') . ' (NISN: ' . ($item->nisn ?? '-') . ')'
                ];
            });

        return response()->json($santri);
    }
}
