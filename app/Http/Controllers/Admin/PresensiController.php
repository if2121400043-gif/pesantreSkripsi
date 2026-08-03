<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asrama;
use App\Models\JenisPresensi;
use App\Models\PesertaDidik;
use App\Models\PesertaMukimTahun;
use App\Models\PresensiKelas;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Models\RiwayatRombelPeserta;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PresensiController extends Controller
{
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

                    $presensiHariIni = PresensiKelas::where('jenis_presensi_id', $selectedJenis->id)
                        ->where('rombel_id', $request->rombel_id)
                        ->whereDate('tanggal', $tanggal)
                        ->get()
                        ->keyBy('peserta_didik_id');
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

                    $presensiHariIni = PresensiKelas::where('jenis_presensi_id', $selectedJenis->id)
                        ->where('asrama_id', $request->asrama_id)
                        ->whereDate('tanggal', $tanggal)
                        ->get()
                        ->keyBy('peserta_didik_id');
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

                $presensiHariIni = PresensiKelas::where('jenis_presensi_id', $selectedJenis->id)
                    ->whereDate('tanggal', $tanggal)
                    ->get()
                    ->keyBy('peserta_didik_id');
            }
        }

        return view('admin.akademik.presensi.index', compact(
            'jenisPresensiList', 'selectedJenis',
            'rombels', 'selectedRombel',
            'asramas', 'selectedAsrama',
            'tanggal', 'peserta', 'presensiHariIni', 'tahunAktif'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_presensi_id' => 'required|exists:jenis_presensi,id',
            'tanggal' => 'required|date',
            'presensi' => 'required|array',
            'presensi.*.status' => 'required|in:HADIR,SAKIT,IZIN,ALPHA',
            'presensi.*.keterangan' => 'nullable|string',
            'rombel_id' => 'nullable|exists:rombel,id',
            'asrama_id' => 'nullable|exists:asrama,id',
        ]);

        $jenisPresensiId = $request->jenis_presensi_id;
        $rombelId = $request->rombel_id;
        $asramaId = $request->asrama_id;
        $tanggal = $request->tanggal;
        $dicatatOleh = auth()->id() ?? 1;

        foreach ($request->presensi as $pesertaId => $data) {
            PresensiKelas::updateOrCreate(
                [
                    'peserta_didik_id' => $pesertaId,
                    'jenis_presensi_id' => $jenisPresensiId,
                    'tanggal' => $tanggal,
                ],
                [
                    'rombel_id' => $rombelId,
                    'asrama_id' => $asramaId,
                    'status' => $data['status'],
                    'keterangan' => $data['keterangan'] ?? null,
                    'dicatat_oleh' => $dicatatOleh,
                ]
            );
        }

        return redirect()->route('admin.presensi.index', [
            'jenis_presensi_id' => $jenisPresensiId,
            'rombel_id' => $rombelId,
            'asrama_id' => $asramaId,
            'tanggal' => $tanggal,
        ])->with('success', 'Data presensi berhasil disimpan.');
    }

    public function rekap(Request $request)
    {
        $gurus = \App\Models\Pegawai::with('orang')->whereHas('jadwalPelajaran')->get();
        $rombels = Rombel::with('lembaga')->orderBy('nama')->get();
        $mapels = \App\Models\MataPelajaran::orderBy('nama')->get();

        $selectedPegawaiId = $request->get('pegawai_id');
        $selectedRombelId = $request->get('rombel_id');
        $selectedMapelId = $request->get('mata_pelajaran_id');

        $filterMode = $request->get('filter_mode', 'bulan'); // 'hari', 'minggu', 'bulan'
        $tanggalStart = $request->get('tanggal_start', date('Y-m-d'));
        $tanggalEnd = $request->get('tanggal_end', date('Y-m-d'));
        $bulan = (int) $request->get('bulan', date('n'));
        $tahun = (int) $request->get('tahun', date('Y'));

        // Query Jadwal Pelajaran based on selected filters
        $jadwalQuery = \App\Models\JadwalPelajaran::with(['mataPelajaran', 'rombel.riwayatPeserta' => function($q) {
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
        $query = PresensiKelas::whereIn('rombel_id', $rombelIds);

        $periodeLabel = '';
        $dateList = [];

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

            $dateList = PresensiKelas::whereIn('rombel_id', $rombelIds)
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->select(\Illuminate\Support\Facades\DB::raw('DISTINCT DATE(tanggal) as date_val'))
                ->orderBy('date_val')
                ->pluck('date_val')
                ->toArray();
        }

        $presensiData = $query->get()->groupBy(['peserta_didik_id', function($item) {
            return Carbon::parse($item->tanggal)->format('Y-m-d');
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

        $jadwalQuery = \App\Models\JadwalPelajaran::with(['mataPelajaran', 'rombel.riwayatPeserta' => function($q) {
            $q->where('status', 'AKTIF')->with('pesertaDidik.orang');
        }, 'guru.orang']);

        if ($selectedPegawaiId) $jadwalQuery->where('pegawai_id', $selectedPegawaiId);
        if ($selectedRombelId) $jadwalQuery->where('rombel_id', $selectedRombelId);
        if ($selectedMapelId) $jadwalQuery->where('mata_pelajaran_id', $selectedMapelId);

        $jadwals = $jadwalQuery->get();
        $selectedJadwal = $jadwals->first();

        $rombelIds = $jadwals->pluck('rombel_id')->unique()->filter();
        $query = PresensiKelas::whereIn('rombel_id', $rombelIds);

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

            $dateList = PresensiKelas::whereIn('rombel_id', $rombelIds)
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->select(\Illuminate\Support\Facades\DB::raw('DISTINCT DATE(tanggal) as date_val'))
                ->orderBy('date_val')
                ->pluck('date_val')
                ->toArray();
        }

        $presensiData = $query->get()->groupBy(['peserta_didik_id', function($item) {
            return Carbon::parse($item->tanggal)->format('Y-m-d');
        }]);

        return view('admin.akademik.presensi.cetak', compact(
            'jadwals', 'selectedJadwal', 'filterMode', 'periodeLabel',
            'presensiData', 'dateList', 'selectedPegawaiId', 'selectedRombelId', 'selectedMapelId'
        ));
    }
}
