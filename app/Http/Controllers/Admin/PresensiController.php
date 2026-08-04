<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminAttendanceRequest;
use App\Models\Asrama;
use App\Models\Attendance;
use App\Models\JadwalPelajaran;
use App\Models\JenisPresensi;
use App\Models\MataPelajaran;
use App\Models\Pegawai;
use App\Models\PesertaDidik;
use App\Models\PesertaMukimTahun;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Models\RiwayatRombelPeserta;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PresensiController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendanceService
    ) {}

    public function index(Request $request)
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        // Load all active jenis presensi
        $jenisPresensiList = JenisPresensi::active()->orderBy('urutan')->get();
        $selectedJenis = null;
        $selectedRombel = null;
        $selectedAsrama = null;
        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $peserta = collect();
        $presensiHariIni = collect();
        $rombels = collect();
        $asramas = collect();

        if ($request->filled('jenis_presensi_id')) {
            $selectedJenis = JenisPresensi::find($request->jenis_presensi_id);
        }

        if ($selectedJenis) {
            if ($selectedJenis->tipe_target === 'PER_ROMBEL') {
                // Load rombels for the active year
                $rombels = Rombel::with('lembaga')
                    ->where('tahun_pelajaran_id', $tahunAktif?->id)
                    ->orderBy('nama')
                    ->get();

                if ($request->filled('rombel_id')) {
                    $selectedRombel = Rombel::find($request->rombel_id);

                    $riwayat = RiwayatRombelPeserta::with(['pesertaDidik.orang'])
                        ->where('rombel_id', $request->rombel_id)
                        ->where('status', 'AKTIF')
                        ->get();

                    $peserta = $riwayat->pluck('pesertaDidik')
                        ->filter()
                        ->when($selectedJenis->target_gender !== 'SEMUA', function ($collection) use ($selectedJenis) {
                            $gender = $selectedJenis->target_gender === 'PUTRA' ? 'L' : 'P';
                            return $collection->filter(fn($p) => $p->orang?->jenis_kelamin === $gender);
                        })
                        ->sortBy(fn($p) => $p->orang?->nama_lengkap);

                    // Query dari tabel attendance (Layer 2) — bukan lagi presensi_kelas
                    $presensiHariIni = Attendance::where('jenis_presensi_id', $selectedJenis->id)
                        ->where('rombel_id', $request->rombel_id)
                        ->whereDate('attendance_date', $tanggal)
                        ->get()
                        ->keyBy('student_id');
                }

            } elseif ($selectedJenis->tipe_target === 'PER_ASRAMA') {
                // Load asramas, filtered by gender if needed
                $asramas = Asrama::where('is_active', true)
                    ->when($selectedJenis->target_gender !== 'SEMUA', function ($q) use ($selectedJenis) {
                        $gender = $selectedJenis->target_gender === 'PUTRA' ? 'L' : 'P';
                        $q->where('jenis_kelamin', $gender);
                    })
                    ->orderBy('nama')
                    ->get();

                if ($request->filled('asrama_id')) {
                    $selectedAsrama = Asrama::find($request->asrama_id);

                    // Get santri who are mukim in this asrama's kamar
                    $kamarIds = $selectedAsrama->kamar()->pluck('id');
                    $mukimPeserta = PesertaMukimTahun::with(['pesertaDidik.orang'])
                        ->whereIn('kamar_id', $kamarIds)
                        ->where('tahun_pelajaran_id', $tahunAktif?->id)
                        ->where('status_mukim', 'MUKIM')
                        ->get();

                    $peserta = $mukimPeserta->pluck('pesertaDidik')
                        ->filter()
                        ->when($selectedJenis->target_gender !== 'SEMUA', function ($collection) use ($selectedJenis) {
                            $gender = $selectedJenis->target_gender === 'PUTRA' ? 'L' : 'P';
                            return $collection->filter(fn($p) => $p->orang?->jenis_kelamin === $gender);
                        })
                        ->sortBy(fn($p) => $p->orang?->nama_lengkap);

                    // Query dari tabel attendance (Layer 2)
                    $presensiHariIni = Attendance::where('jenis_presensi_id', $selectedJenis->id)
                        ->where('asrama_id', $request->asrama_id)
                        ->whereDate('attendance_date', $tanggal)
                        ->get()
                        ->keyBy('student_id');
                }

            } elseif ($selectedJenis->tipe_target === 'SEMUA_SANTRI') {
                // Load all active santri, filtered by gender
                $peserta = PesertaDidik::with('orang')
                    ->where('status', 'AKTIF')
                    ->get()
                    ->when($selectedJenis->target_gender !== 'SEMUA', function ($collection) use ($selectedJenis) {
                        $gender = $selectedJenis->target_gender === 'PUTRA' ? 'L' : 'P';
                        return $collection->filter(fn($p) => $p->orang?->jenis_kelamin === $gender);
                    })
                    ->sortBy(fn($p) => $p->orang?->nama_lengkap);

                // Query dari tabel attendance (Layer 2)
                $presensiHariIni = Attendance::where('jenis_presensi_id', $selectedJenis->id)
                    ->whereDate('attendance_date', $tanggal)
                    ->get()
                    ->keyBy('student_id');
            }
        }

        return view('admin.akademik.presensi.index', compact(
            'jenisPresensiList', 'selectedJenis',
            'rombels', 'selectedRombel',
            'asramas', 'selectedAsrama',
            'tanggal', 'peserta', 'presensiHariIni', 'tahunAktif'
        ));
    }

    /**
     * Store attendance — delegate to AttendanceService (thin controller).
     */
    public function store(StoreAdminAttendanceRequest $request)
    {
        $jenisPresensiId = $request->jenis_presensi_id;
        $rombelId = $request->rombel_id;
        $asramaId = $request->asrama_id;

        // Gunakan server date (WITA), bukan tanggal dari browser
        $tanggal = Carbon::now('Asia/Makassar')->format('Y-m-d');

        try {
            $this->attendanceService->bulkRecordAttendance(
                studentsData: $request->normalizedPresensi(),
                jenisPresensiId: (int) $jenisPresensiId,
                date: $tanggal,
                sharedMetadata: [
                    'rombel_id' => $rombelId,
                    'asrama_id' => $asramaId,
                    'device' => 'WEB',
                    'ip_address' => $request->ip(),
                    'user_agent' => substr($request->userAgent() ?? '', 0, 500),
                ],
                userId: auth()->id()
            );

            return redirect()->route('admin.presensi.index', [
                'jenis_presensi_id' => $jenisPresensiId,
                'rombel_id' => $rombelId,
                'asrama_id' => $asramaId,
                'tanggal' => $tanggal,
            ])->with('success', 'Data presensi berhasil disimpan.');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function rekap(Request $request)
    {
        $gurus = Pegawai::with('orang')->whereHas('jadwalMengajar')->get();
        $rombels = Rombel::with('lembaga')->orderBy('nama')->get();
        $mapels = MataPelajaran::orderBy('nama')->get();

        $selectedPegawaiId = $request->get('pegawai_id');
        $selectedRombelId = $request->get('rombel_id');
        $selectedMapelId = $request->get('mata_pelajaran_id');

        $filterMode = $request->get('filter_mode', 'bulan');
        $tanggalStart = $request->get('tanggal_start', date('Y-m-d'));
        $tanggalEnd = $request->get('tanggal_end', date('Y-m-d'));
        $bulan = (int) $request->get('bulan', date('n'));
        $tahun = (int) $request->get('tahun', date('Y'));

        // Query Jadwal Pelajaran based on selected filters
        $jadwalQuery = JadwalPelajaran::with(['mataPelajaran', 'rombel.riwayatPeserta' => function($q) {
            $q->where('status', 'AKTIF')->with('pesertaDidik.orang');
        }, 'guru.orang']);

        if ($selectedPegawaiId) {
            $jadwalQuery->where('pegawai_id', $selectedPegawaiId);
        }
        if ($selectedRombelId) {
            $jadwalQuery->where('rombel_id', $selectedRombelId);
        }
        if ($selectedMapelId) {
            $jadwalQuery->where('mata_pelajaran_id', $selectedMapelId);
        }

        $jadwals = $jadwalQuery->get();
        $rombelIds = $jadwals->pluck('rombel_id')->unique()->filter();

        // Query dari tabel attendance (Layer 2) — bukan lagi presensi_kelas
        $query = Attendance::whereIn('rombel_id', $rombelIds);

        $periodeLabel = '';
        $dateList = [];

        if ($filterMode === 'hari') {
            $query->whereDate('attendance_date', $tanggalStart);
            $periodeLabel = 'Harian: ' . Carbon::parse($tanggalStart)->locale('id')->isoFormat('D MMMM YYYY');
            $dateList = [$tanggalStart];
        } elseif ($filterMode === 'minggu') {
            $query->whereBetween('attendance_date', [$tanggalStart, $tanggalEnd]);
            $periodeLabel = 'Mingguan: ' . Carbon::parse($tanggalStart)->locale('id')->isoFormat('D MMM YYYY') . ' s/d ' . Carbon::parse($tanggalEnd)->locale('id')->isoFormat('D MMM YYYY');
            
            $dt = Carbon::parse($tanggalStart);
            $dtEnd = Carbon::parse($tanggalEnd);
            while ($dt->lte($dtEnd)) {
                $dateList[] = $dt->format('Y-m-d');
                $dt->addDay();
            }
        } else { // bulan
            $query->whereYear('attendance_date', $tahun)->whereMonth('attendance_date', $bulan);
            $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->locale('id')->isoFormat('MMMM YYYY');
            $periodeLabel = 'Bulanan: ' . $namaBulan;

            $dateList = Attendance::whereIn('rombel_id', $rombelIds)
                ->whereYear('attendance_date', $tahun)
                ->whereMonth('attendance_date', $bulan)
                ->select(DB::raw('DISTINCT DATE(attendance_date) as date_val'))
                ->orderBy('date_val')
                ->pluck('date_val')
                ->toArray();
        }

        $presensiData = $query->get()->groupBy(['student_id', function($item) {
            return Carbon::parse($item->attendance_date)->format('Y-m-d');
        }]);

        return view('admin.akademik.presensi.rekap', compact(
            'gurus', 'rombels', 'mapels', 'jadwals',
            'selectedPegawaiId', 'selectedRombelId', 'selectedMapelId',
            'filterMode', 'tanggalStart', 'tanggalEnd', 'bulan', 'tahun',
            'periodeLabel', 'presensiData', 'dateList'
        ));
    }

    public function cetak(Request $request)
    {
        $selectedPegawaiId = $request->get('pegawai_id');
        $selectedRombelId = $request->get('rombel_id');
        $selectedMapelId = $request->get('mata_pelajaran_id');

        $filterMode = $request->get('filter_mode', 'bulan');
        $tanggalStart = $request->get('tanggal_start', date('Y-m-d'));
        $tanggalEnd = $request->get('tanggal_end', date('Y-m-d'));
        $bulan = (int) $request->get('bulan', date('n'));
        $tahun = (int) $request->get('tahun', date('Y'));

        $jadwalQuery = JadwalPelajaran::with(['mataPelajaran', 'rombel.riwayatPeserta' => function($q) {
            $q->where('status', 'AKTIF')->with('pesertaDidik.orang');
        }, 'guru.orang']);

        if ($selectedPegawaiId) $jadwalQuery->where('pegawai_id', $selectedPegawaiId);
        if ($selectedRombelId) $jadwalQuery->where('rombel_id', $selectedRombelId);
        if ($selectedMapelId) $jadwalQuery->where('mata_pelajaran_id', $selectedMapelId);

        $jadwals = $jadwalQuery->get();
        $selectedJadwal = $jadwals->first();

        $rombelIds = $jadwals->pluck('rombel_id')->unique()->filter();

        // Query dari tabel attendance (Layer 2) — bukan lagi presensi_kelas
        $query = Attendance::whereIn('rombel_id', $rombelIds);

        $periodeLabel = '';
        $dateList = [];

        if ($filterMode === 'hari') {
            $query->whereDate('attendance_date', $tanggalStart);
            $periodeLabel = Carbon::parse($tanggalStart)->locale('id')->isoFormat('dddd, D MMMM YYYY');
            $dateList = [$tanggalStart];
        } elseif ($filterMode === 'minggu') {
            $query->whereBetween('attendance_date', [$tanggalStart, $tanggalEnd]);
            $periodeLabel = Carbon::parse($tanggalStart)->locale('id')->isoFormat('D MMMM YYYY') . ' s/d ' . Carbon::parse($tanggalEnd)->locale('id')->isoFormat('D MMMM YYYY');
            
            $dt = Carbon::parse($tanggalStart);
            $dtEnd = Carbon::parse($tanggalEnd);
            while ($dt->lte($dtEnd)) {
                $dateList[] = $dt->format('Y-m-d');
                $dt->addDay();
            }
        } else {
            $query->whereYear('attendance_date', $tahun)->whereMonth('attendance_date', $bulan);
            $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->locale('id')->isoFormat('MMMM YYYY');
            $periodeLabel = 'Bulan ' . $namaBulan;

            $dateList = Attendance::whereIn('rombel_id', $rombelIds)
                ->whereYear('attendance_date', $tahun)
                ->whereMonth('attendance_date', $bulan)
                ->select(DB::raw('DISTINCT DATE(attendance_date) as date_val'))
                ->orderBy('date_val')
                ->pluck('date_val')
                ->toArray();
        }

        $presensiData = $query->get()->groupBy(['student_id', function($item) {
            return Carbon::parse($item->attendance_date)->format('Y-m-d');
        }]);

        return view('admin.akademik.presensi.cetak', compact(
            'jadwals', 'selectedJadwal', 'filterMode', 'periodeLabel',
            'presensiData', 'dateList', 'selectedPegawaiId', 'selectedRombelId', 'selectedMapelId'
        ));
    }
}
