<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\JadwalPelajaran;
use App\Models\NilaiRapor;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenilaianController extends Controller
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

        // Menampilkan daftar kelas yang diampu (distinct berdasarkan rombel dan mata pelajaran)
        // Kita bisa ambil dari jadwal_pelajaran
        $jadwals = JadwalPelajaran::with(['mataPelajaran', 'rombel.tahunPelajaran'])
            ->where('pegawai_id', $pegawai->id)
            ->get()
            ->unique(function ($item) {
                return $item->rombel_id . '-' . $item->mata_pelajaran_id;
            });

        return view('guru.penilaian.index', compact('jadwals'));
    }

    public function create(Request $request, $jadwal_id)
    {
        $pegawai = $this->getPegawai();
        if (!$pegawai) return redirect()->route('guru.dashboard');

        $jadwal = JadwalPelajaran::with(['mataPelajaran', 'rombel.riwayatPeserta' => function($q) {
                $q->where('status', 'AKTIF')->with('pesertaDidik.orang');
            }])
            ->where('pegawai_id', $pegawai->id)
            ->findOrFail($jadwal_id);

        $semester = $request->get('semester', 'Ganjil'); // Default Ganjil

        // Get existing nilai for this rombel, mapel, and semester
        $existingNilai = NilaiRapor::where('rombel_id', $jadwal->rombel_id)
            ->where('mata_pelajaran_id', $jadwal->mata_pelajaran_id)
            ->where('tahun_pelajaran_id', $jadwal->tahun_pelajaran_id)
            ->where('semester', strtoupper($semester))
            ->get()
            ->keyBy('peserta_didik_id');

        return view('guru.penilaian.create', compact('jadwal', 'semester', 'existingNilai'));
    }

    public function store(Request $request, $jadwal_id)
    {
        $pegawai = $this->getPegawai();
        if (!$pegawai) return redirect()->route('guru.dashboard');

        $jadwal = JadwalPelajaran::where('pegawai_id', $pegawai->id)->findOrFail($jadwal_id);
        
        $request->validate([
            'semester' => 'required|string|in:GANJIL,GENAP',
            'nilai' => 'required|array',
            'nilai.*.tugas' => 'nullable|numeric|min:0|max:100',
            'nilai.*.uts' => 'nullable|numeric|min:0|max:100',
            'nilai.*.uas' => 'nullable|numeric|min:0|max:100',
            'nilai.*.catatan' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->nilai as $peserta_didik_id => $data) {
                $tugas = $data['tugas'] ?? 0;
                $uts = $data['uts'] ?? 0;
                $uas = $data['uas'] ?? 0;
                
                // Kalkulasi nilai akhir sederhana (bisa disesuaikan dengan aturan sekolah)
                $nilai_akhir = ($tugas * 0.3) + ($uts * 0.3) + ($uas * 0.4);
                
                // Kalkulasi predikat
                $predikat = 'D';
                if ($nilai_akhir >= 90) $predikat = 'A';
                elseif ($nilai_akhir >= 80) $predikat = 'B';
                elseif ($nilai_akhir >= 70) $predikat = 'C';

                NilaiRapor::updateOrCreate(
                    [
                        'peserta_didik_id' => $peserta_didik_id,
                        'rombel_id' => $jadwal->rombel_id,
                        'mata_pelajaran_id' => $jadwal->mata_pelajaran_id,
                        'tahun_pelajaran_id' => $jadwal->tahun_pelajaran_id,
                        'semester' => strtoupper($request->semester),
                    ],
                    [
                        'nilai_tugas' => $tugas,
                        'nilai_uts' => $uts,
                        'nilai_uas' => $uas,
                        'nilai_akhir' => round($nilai_akhir, 2),
                        'predikat' => $predikat,
                        'catatan_guru' => $data['catatan'] ?? null,
                    ]
                );
            }

            DB::commit();
            return redirect()->route('guru.penilaian.index')->with('success', 'Nilai rapor berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
}
