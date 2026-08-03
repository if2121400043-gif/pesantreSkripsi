<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\JadwalPelajaran;
use App\Models\PresensiKelas;
use App\Models\Rombel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PresensiController extends Controller
{
    private function getPegawai()
    {
        $user = auth()->user();
        return $user && $user->orang_id ? Pegawai::where('orang_id', $user->orang_id)->first() : null;
    }

    /**
     * Konversi nama hari Indonesia (Carbon) ke enum database.
     * Carbon menggunakan 'Minggu' sedangkan database menggunakan 'AHAD'.
     */
    private function hariToEnum(string $hariCarbon): string
    {
        $map = [
            'Senin'     => 'SENIN',
            'Selasa'    => 'SELASA',
            'Rabu'      => 'RABU',
            'Kamis'     => 'KAMIS',
            'Jumat'     => 'JUMAT',
            'Sabtu'     => 'SABTU',
            'Minggu'    => 'AHAD', // ⚠️ Pesantren memakai istilah 'AHAD', bukan 'MINGGU'
            // English fallbacks in case system locale is not 'id'
            'Monday'    => 'SENIN',
            'Tuesday'   => 'SELASA',
            'Wednesday' => 'RABU',
            'Thursday'  => 'KAMIS',
            'Friday'    => 'JUMAT',
            'Saturday'  => 'SABTU',
            'Sunday'    => 'AHAD',
        ];

        return $map[$hariCarbon] ?? strtoupper($hariCarbon);
    }

    public function index(Request $request)
    {
        $pegawai = $this->getPegawai();
        if (!$pegawai) return redirect()->route('guru.dashboard')->with('error', 'Data pegawai tidak ditemukan.');

        $hariCarbon = Carbon::now()->locale('id')->dayName; // e.g., 'Senin', 'Minggu'
        $hariDefault = $this->hariToEnum($hariCarbon);      // e.g., 'SENIN', 'AHAD'
        $hari = $request->get('hari', $hariDefault);
        
        $jadwals = JadwalPelajaran::with(['mataPelajaran', 'rombel'])
            ->where('pegawai_id', $pegawai->id)
            ->where('hari', $hari)
            ->orderBy('jam_mulai')
            ->get();

        $daftarHari = ['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'AHAD'];

        return view('guru.presensi.index', compact('jadwals', 'hari', 'daftarHari'));
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

        $tanggal = $request->get('tanggal', date('Y-m-d'));

        // Get existing presensi for this rombel on this date
        $existingPresensi = PresensiKelas::where('rombel_id', $jadwal->rombel_id)
            ->whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy('peserta_didik_id');

        return view('guru.presensi.create', compact('jadwal', 'tanggal', 'existingPresensi'));
    }

    public function store(Request $request, $jadwal_id)
    {
        $pegawai = $this->getPegawai();
        if (!$pegawai) return redirect()->route('guru.dashboard');

        $jadwal = JadwalPelajaran::where('pegawai_id', $pegawai->id)->findOrFail($jadwal_id);
        
        $request->validate([
            'tanggal' => 'required|date',
            'presensi' => 'required|array',
            'presensi.*.status' => 'required|in:HADIR,SAKIT,IZIN,ALPHA',
            'presensi.*.keterangan' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $tanggal = $request->tanggal;
            $jenisPresensi = \App\Models\JenisPresensi::where('kode', 'KBM')->first()
                ?? \App\Models\JenisPresensi::first();
            $jenisPresensiId = $jenisPresensi ? $jenisPresensi->id : 1;

            foreach ($request->presensi as $peserta_didik_id => $data) {
                PresensiKelas::updateOrCreate(
                    [
                        'peserta_didik_id' => $peserta_didik_id,
                        'rombel_id' => $jadwal->rombel_id,
                        'tanggal' => $tanggal,
                    ],
                    [
                        'jenis_presensi_id' => $jenisPresensiId,
                        'status' => $data['status'],
                        'keterangan' => $data['keterangan'] ?? null,
                        'dicatat_oleh' => auth()->id(),
                    ]
                );
            }

            DB::commit();
            return redirect()->route('guru.presensi.index')->with('success', 'Presensi kelas ' . $jadwal->rombel->nama . ' berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
}
