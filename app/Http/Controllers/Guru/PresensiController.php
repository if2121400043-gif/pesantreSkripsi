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

        // Ambil timestamp presensi terakhir diinput/diedit per rombel
        $rombelIds = $jadwals->pluck('rombel_id')->unique();
        $latestPresensiMap = PresensiKelas::whereIn('rombel_id', $rombelIds)
            ->select('rombel_id', DB::raw('MAX(updated_at) as last_input_at'))
            ->groupBy('rombel_id')
            ->pluck('last_input_at', 'rombel_id');

        $daftarHari = ['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'AHAD'];

        return view('guru.presensi.index', compact('jadwals', 'hari', 'daftarHari', 'latestPresensiMap'));
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

        $tanggal = $request->get('tanggal', \Carbon\Carbon::now('Asia/Makassar')->format('Y-m-d'));

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
            'presensi.*.status' => 'required|in:HADIR,SAKIT,IZIN,ALPHA,ALPA',
            'presensi.*.keterangan' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $tanggal = $request->tanggal;
            $jenisPresensi = \App\Models\JenisPresensi::where('kode', 'KBM')->first()
                ?? \App\Models\JenisPresensi::first();
            $jenisPresensiId = $jenisPresensi ? $jenisPresensi->id : 1;

            foreach ($request->presensi as $peserta_didik_id => $data) {
                $statusDb = ($data['status'] === 'ALPHA') ? 'ALPA' : $data['status'];

                PresensiKelas::updateOrCreate(
                    [
                        'peserta_didik_id' => $peserta_didik_id,
                        'rombel_id' => $jadwal->rombel_id,
                        'tanggal' => $tanggal,
                    ],
                    [
                        'jenis_presensi_id' => $jenisPresensiId,
                        'status' => $statusDb,
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

    public function rekap(Request $request)
    {
        $pegawai = $this->getPegawai();
        if (!$pegawai) return redirect()->route('guru.dashboard');

        $jadwals = JadwalPelajaran::with(['mataPelajaran', 'rombel'])
            ->where('pegawai_id', $pegawai->id)
            ->get();

        $selectedJadwalId = $request->get('jadwal_id', $jadwals->first()?->id);
        $filterMode = $request->get('filter_mode', 'bulan'); // 'hari', 'minggu', 'bulan'

        $tanggalStart = $request->get('tanggal_start', date('Y-m-d'));
        $tanggalEnd = $request->get('tanggal_end', date('Y-m-d'));
        $bulan = (int) $request->get('bulan', date('n'));
        $tahun = (int) $request->get('tahun', date('Y'));

        $selectedJadwal = null;
        $presensiData = collect();
        $pesertaList = collect();
        $dateList = [];
        $periodeLabel = '';

        if ($selectedJadwalId) {
            $selectedJadwal = JadwalPelajaran::with(['mataPelajaran', 'rombel.riwayatPeserta' => function($q) {
                $q->where('status', 'AKTIF')->with('pesertaDidik.orang');
            }, 'guru.orang'])
            ->where('pegawai_id', $pegawai->id)
            ->find($selectedJadwalId);

            if ($selectedJadwal) {
                $pesertaList = $selectedJadwal->rombel->riwayatPeserta->map->pesertaDidik;

                $query = PresensiKelas::where('rombel_id', $selectedJadwal->rombel_id);

                if ($filterMode === 'hari') {
                    $query->whereDate('tanggal', $tanggalStart);
                    $periodeLabel = 'Harian: ' . Carbon::parse($tanggalStart)->locale('id')->isoFormat('D MMMM YYYY');
                    $dateList = [$tanggalStart];
                } elseif ($filterMode === 'minggu') {
                    $query->whereBetween('tanggal', [$tanggalStart, $tanggalEnd]);
                    $periodeLabel = 'Mingguan: ' . Carbon::parse($tanggalStart)->locale('id')->isoFormat('D MMM YYYY') . ' s/d ' . Carbon::parse($tanggalEnd)->locale('id')->isoFormat('D MMM YYYY');
                    
                    $dt = Carbon::parse($tanggalStart);
                    $dtEnd = Carbon::parse($tanggalEnd);
                    while ($dt->lte($dtEnd)) {
                        $dateList[] = $dt->format('Y-m-d');
                        $dt->addDay();
                    }
                } else { // bulan
                    $query->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);
                    $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->locale('id')->isoFormat('MMMM YYYY');
                    $periodeLabel = 'Bulanan: ' . $namaBulan;

                    $dateList = PresensiKelas::where('rombel_id', $selectedJadwal->rombel_id)
                        ->whereYear('tanggal', $tahun)
                        ->whereMonth('tanggal', $bulan)
                        ->select(DB::raw('DISTINCT DATE(tanggal) as date_val'))
                        ->orderBy('date_val')
                        ->pluck('date_val')
                        ->toArray();
                }

                $presensiData = $query->get()->groupBy(['peserta_didik_id', function($item) {
                    return Carbon::parse($item->tanggal)->format('Y-m-d');
                }]);
            }
        }

        return view('guru.presensi.rekap', compact(
            'jadwals', 'selectedJadwal', 'selectedJadwalId', 'filterMode',
            'tanggalStart', 'tanggalEnd', 'bulan', 'tahun', 'periodeLabel',
            'pesertaList', 'presensiData', 'dateList', 'pegawai'
        ));
    }

    public function cetak(Request $request)
    {
        $pegawai = $this->getPegawai();
        if (!$pegawai) return redirect()->route('guru.dashboard');

        $selectedJadwalId = $request->get('jadwal_id');
        $filterMode = $request->get('filter_mode', 'bulan');
        $tanggalStart = $request->get('tanggal_start', date('Y-m-d'));
        $tanggalEnd = $request->get('tanggal_end', date('Y-m-d'));
        $bulan = (int) $request->get('bulan', date('n'));
        $tahun = (int) $request->get('tahun', date('Y'));

        $selectedJadwal = JadwalPelajaran::with(['mataPelajaran', 'rombel.riwayatPeserta' => function($q) {
            $q->where('status', 'AKTIF')->with('pesertaDidik.orang');
        }, 'guru.orang'])
        ->where('pegawai_id', $pegawai->id)
        ->findOrFail($selectedJadwalId);

        $pesertaList = $selectedJadwal->rombel->riwayatPeserta->map->pesertaDidik;
        $query = PresensiKelas::where('rombel_id', $selectedJadwal->rombel_id);
        $periodeLabel = '';
        $dateList = [];

        if ($filterMode === 'hari') {
            $query->whereDate('tanggal', $tanggalStart);
            $periodeLabel = Carbon::parse($tanggalStart)->locale('id')->isoFormat('dddd, D MMMM YYYY');
            $dateList = [$tanggalStart];
        } elseif ($filterMode === 'minggu') {
            $query->whereBetween('tanggal', [$tanggalStart, $tanggalEnd]);
            $periodeLabel = Carbon::parse($tanggalStart)->locale('id')->isoFormat('D MMMM YYYY') . ' s/d ' . Carbon::parse($tanggalEnd)->locale('id')->isoFormat('D MMMM YYYY');
            
            $dt = Carbon::parse($tanggalStart);
            $dtEnd = Carbon::parse($tanggalEnd);
            while ($dt->lte($dtEnd)) {
                $dateList[] = $dt->format('Y-m-d');
                $dt->addDay();
            }
        } else {
            $query->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);
            $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->locale('id')->isoFormat('MMMM YYYY');
            $periodeLabel = 'Bulan ' . $namaBulan;

            $dateList = PresensiKelas::where('rombel_id', $selectedJadwal->rombel_id)
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->select(DB::raw('DISTINCT DATE(tanggal) as date_val'))
                ->orderBy('date_val')
                ->pluck('date_val')
                ->toArray();
        }

        $presensiData = $query->get()->groupBy(['peserta_didik_id', function($item) {
            return Carbon::parse($item->tanggal)->format('Y-m-d');
        }]);

        return view('guru.presensi.cetak', compact(
            'selectedJadwal', 'filterMode', 'periodeLabel',
            'pesertaList', 'presensiData', 'dateList', 'pegawai'
        ));
    }
}
