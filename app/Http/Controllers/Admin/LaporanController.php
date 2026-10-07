<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PesertaDidik;
use App\Models\Attendance;
use App\Models\CatatanPelanggaran;
use App\Models\CatatanPrestasi;
use App\Models\CalonSantri;

use App\Models\TahunPelajaran;
use App\Models\Lembaga;
use App\Models\Rombel;
use App\Models\Asrama;
use App\Models\JenisPresensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanController extends Controller
{
    // =========================================================================
    // Hub — Pusat Laporan
    // =========================================================================
    public function index()
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        $stats = [
            'totalSantriAktif'  => PesertaDidik::where('status', 'AKTIF')->count(),
            'totalPresensi'     => Attendance::count(),
            'totalPelanggaran'  => CatatanPelanggaran::count(),
            'totalPrestasi'     => CatatanPrestasi::count(),
            'totalPendaftar'    => CalonSantri::count(),
        ];

        return view('admin.laporan.index', compact('tahunAktif', 'stats'));
    }

    // =========================================================================
    // Laporan Santri
    // =========================================================================
    public function santri(Request $request)
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $lembagas   = Lembaga::orderBy('urutan')->get();

        $query = PesertaDidik::with(['orang', 'riwayatRombel' => function ($q) use ($tahunAktif) {
            if ($tahunAktif) {
                $q->where('tahun_pelajaran_id', $tahunAktif->id)->where('status', 'AKTIF');
            }
            $q->with('rombel.lembaga');
        }]);

        // Filters
        if ($request->filled('status') && $request->status !== 'SEMUA') {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'AKTIF');
        }

        if ($request->filled('jenis_kelamin') && $request->jenis_kelamin !== 'SEMUA') {
            $query->whereHas('orang', fn($q) => $q->where('jenis_kelamin', $request->jenis_kelamin));
        }

        if ($request->filled('lembaga_id') && $request->lembaga_id !== 'SEMUA' && $tahunAktif) {
            $lembagaId = $request->lembaga_id;
            $query->whereHas('riwayatRombel', function ($q) use ($tahunAktif, $lembagaId) {
                $q->where('tahun_pelajaran_id', $tahunAktif->id)
                  ->where('status', 'AKTIF')
                  ->whereHas('rombel', fn($r) => $r->where('lembaga_id', $lembagaId));
            });
        }

        $santris = $query->get();

        // Statistics
        $totalSantri = $santris->count();
        $putraCount  = $santris->filter(fn($s) => $s->orang?->jenis_kelamin === 'L')->count();
        $putriCount  = $santris->filter(fn($s) => $s->orang?->jenis_kelamin === 'P')->count();

        // Per lembaga breakdown
        $perLembaga = [];
        if ($tahunAktif) {
            foreach ($lembagas as $lembaga) {
                $count = DB::table('riwayat_rombel_peserta')
                    ->join('rombel', 'riwayat_rombel_peserta.rombel_id', '=', 'rombel.id')
                    ->join('peserta_didik', 'riwayat_rombel_peserta.peserta_didik_id', '=', 'peserta_didik.id')
                    ->where('rombel.lembaga_id', $lembaga->id)
                    ->where('riwayat_rombel_peserta.tahun_pelajaran_id', $tahunAktif->id)
                    ->where('riwayat_rombel_peserta.status', 'AKTIF')
                    ->where('peserta_didik.status', 'AKTIF')
                    ->whereNull('peserta_didik.deleted_at')
                    ->count();
                $perLembaga[] = ['nama' => $lembaga->singkatan ?? $lembaga->nama, 'jumlah' => $count];
            }
        }

        return view('admin.laporan.santri', compact(
            'santris', 'lembagas', 'tahunAktif',
            'totalSantri', 'putraCount', 'putriCount', 'perLembaga'
        ));
    }

    public function exportSantri(Request $request)
    {
        // Re-use same query logic
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        $query = PesertaDidik::with(['orang', 'riwayatRombel' => function ($q) use ($tahunAktif) {
            if ($tahunAktif) {
                $q->where('tahun_pelajaran_id', $tahunAktif->id)->where('status', 'AKTIF');
            }
            $q->with('rombel.lembaga');
        }]);

        if ($request->filled('status') && $request->status !== 'SEMUA') {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'AKTIF');
        }

        if ($request->filled('jenis_kelamin') && $request->jenis_kelamin !== 'SEMUA') {
            $query->whereHas('orang', fn($q) => $q->where('jenis_kelamin', $request->jenis_kelamin));
        }

        if ($request->filled('lembaga_id') && $request->lembaga_id !== 'SEMUA' && $tahunAktif) {
            $lembagaId = $request->lembaga_id;
            $query->whereHas('riwayatRombel', function ($q) use ($tahunAktif, $lembagaId) {
                $q->where('tahun_pelajaran_id', $tahunAktif->id)
                  ->where('status', 'AKTIF')
                  ->whereHas('rombel', fn($r) => $r->where('lembaga_id', $lembagaId));
            });
        }

        $santris = $query->get();

        $filename = 'Laporan_Santri_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=$filename",
        ];

        $callback = function () use ($santris, $tahunAktif) {
            $file = fopen('php://output', 'w');
            fputs($file, chr(239) . chr(187) . chr(191)); // BOM
            fputcsv($file, ['No', 'NIUP', 'Nama Lengkap', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'NIS', 'NISN', 'Lembaga', 'Rombel', 'Status'], ';');

            foreach ($santris as $i => $s) {
                $riwayat = $tahunAktif ? $s->riwayatRombel->first() : null;
                fputcsv($file, [
                    $i + 1,
                    $s->orang?->niup ?? '-',
                    $s->orang?->nama_lengkap ?? '-',
                    $s->orang?->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
                    $s->orang?->tempat_lahir ?? '-',
                    $s->orang?->tanggal_lahir?->format('d-m-Y') ?? '-',
                    $s->nis ?? '-',
                    $s->nisn ?? '-',
                    $riwayat?->rombel?->lembaga?->nama ?? '-',
                    $riwayat?->rombel?->nama ?? '-',
                    $s->status,
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // =========================================================================
    // Laporan Presensi
    // =========================================================================
    public function presensi(Request $request)
    {
        $tahunAktif     = TahunPelajaran::where('is_active', true)->first();
        $jenisPresensis = JenisPresensi::where('is_active', true)->orderBy('urutan')->get();
        $rombels        = Rombel::with('lembaga')->when($tahunAktif, fn($q) => $q->where('tahun_pelajaran_id', $tahunAktif->id))->get();
        $asramas        = Asrama::where('is_active', true)->get();

        $dariTanggal   = $request->filled('dari_tanggal') ? Carbon::parse($request->dari_tanggal) : now()->startOfMonth();
        $sampaiTanggal = $request->filled('sampai_tanggal') ? Carbon::parse($request->sampai_tanggal) : now();

        $query = Attendance::with(['pesertaDidik.orang', 'jenisPresensi', 'rombel.lembaga'])
            ->whereBetween('attendance_date', [$dariTanggal->startOfDay(), $sampaiTanggal->endOfDay()]);

        if ($request->filled('jenis_presensi_id') && $request->jenis_presensi_id !== 'SEMUA') {
            $query->where('jenis_presensi_id', $request->jenis_presensi_id);
        }

        if ($request->filled('rombel_id') && $request->rombel_id !== 'SEMUA') {
            $query->where('rombel_id', $request->rombel_id);
        }

        if ($request->filled('asrama_id') && $request->asrama_id !== 'SEMUA') {
            $query->where('asrama_id', $request->asrama_id);
        }

        $records = $query->get();

        // Group by student for recap
        $rekapPerSantri = $records->groupBy('student_id')->map(function ($group) {
            $peserta = $group->first()->pesertaDidik;
            $total   = $group->count();
            $hadir   = $group->where('status', 'HADIR')->count();
            $sakit   = $group->where('status', 'SAKIT')->count();
            $izin    = $group->where('status', 'IZIN')->count();
            $alpa    = $group->where('status', 'ALPA')->count();
            $persen  = $total > 0 ? round(($hadir / $total) * 100, 1) : 0;

            return (object) [
                'peserta'    => $peserta,
                'total'      => $total,
                'hadir'      => $hadir,
                'sakit'      => $sakit,
                'izin'       => $izin,
                'alpa'       => $alpa,
                'persen'     => $persen,
            ];
        })->sortByDesc('persen')->values();

        // Global stats
        $totalRecords = $records->count();
        $globalHadir  = $totalRecords > 0 ? round($records->where('status', 'HADIR')->count() / $totalRecords * 100, 1) : 0;
        $globalSakit  = $totalRecords > 0 ? round($records->where('status', 'SAKIT')->count() / $totalRecords * 100, 1) : 0;
        $globalIzin   = $totalRecords > 0 ? round($records->where('status', 'IZIN')->count() / $totalRecords * 100, 1) : 0;
        $globalAlpa   = $totalRecords > 0 ? round($records->where('status', 'ALPA')->count() / $totalRecords * 100, 1) : 0;

        return view('admin.laporan.presensi', compact(
            'rekapPerSantri', 'jenisPresensis', 'rombels', 'asramas',
            'dariTanggal', 'sampaiTanggal',
            'totalRecords', 'globalHadir', 'globalSakit', 'globalIzin', 'globalAlpa'
        ));
    }

    public function exportPresensi(Request $request)
    {
        $dariTanggal   = $request->filled('dari_tanggal') ? Carbon::parse($request->dari_tanggal) : now()->startOfMonth();
        $sampaiTanggal = $request->filled('sampai_tanggal') ? Carbon::parse($request->sampai_tanggal) : now();

        $query = Attendance::with(['pesertaDidik.orang', 'jenisPresensi', 'rombel.lembaga'])
            ->whereBetween('attendance_date', [$dariTanggal->startOfDay(), $sampaiTanggal->endOfDay()]);

        if ($request->filled('jenis_presensi_id') && $request->jenis_presensi_id !== 'SEMUA') {
            $query->where('jenis_presensi_id', $request->jenis_presensi_id);
        }
        if ($request->filled('rombel_id') && $request->rombel_id !== 'SEMUA') {
            $query->where('rombel_id', $request->rombel_id);
        }
        if ($request->filled('asrama_id') && $request->asrama_id !== 'SEMUA') {
            $query->where('asrama_id', $request->asrama_id);
        }

        $rekapPerSantri = $query->get()->groupBy('student_id')->map(function ($group) {
            $peserta = $group->first()->pesertaDidik;
            return (object) [
                'peserta' => $peserta,
                'total'   => $group->count(),
                'hadir'   => $group->where('status', 'HADIR')->count(),
                'sakit'   => $group->where('status', 'SAKIT')->count(),
                'izin'    => $group->where('status', 'IZIN')->count(),
                'alpa'    => $group->where('status', 'ALPA')->count(),
                'persen'  => $group->count() > 0 ? round($group->where('status', 'HADIR')->count() / $group->count() * 100, 1) : 0,
            ];
        })->sortByDesc('persen')->values();

        $filename = 'Laporan_Presensi_' . $dariTanggal->format('Ymd') . '_sd_' . $sampaiTanggal->format('Ymd') . '.csv';
        $headers = [
            'Content-type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=$filename",
        ];

        $callback = function () use ($rekapPerSantri) {
            $file = fopen('php://output', 'w');
            fputs($file, chr(239) . chr(187) . chr(191));
            fputcsv($file, ['No', 'NIUP', 'Nama Santri', 'Total Sesi', 'Hadir', 'Sakit', 'Izin', 'Alpa', '% Kehadiran'], ';');

            foreach ($rekapPerSantri as $i => $r) {
                fputcsv($file, [
                    $i + 1,
                    $r->peserta?->orang?->niup ?? '-',
                    $r->peserta?->orang?->nama_lengkap ?? '-',
                    $r->total, $r->hadir, $r->sakit, $r->izin, $r->alpa,
                    $r->persen . '%',
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // =========================================================================
    // Laporan Kedisiplinan
    // =========================================================================
    public function kedisiplinan(Request $request)
    {
        $tahunPelajarans = TahunPelajaran::orderBy('nama', 'desc')->get();
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();
        $selectedTahunId = $request->filled('tahun_pelajaran_id') ? $request->tahun_pelajaran_id : ($tahunAktif?->id ?? null);

        // Pelanggaran query
        $pelanggaranQuery = CatatanPelanggaran::with(['pesertaDidik.orang', 'jenisPelanggaran', 'tahunPelajaran']);
        if ($selectedTahunId && $selectedTahunId !== 'SEMUA') {
            $pelanggaranQuery->where('tahun_pelajaran_id', $selectedTahunId);
        }
        if ($request->filled('dari_tanggal')) {
            $pelanggaranQuery->whereDate('tanggal', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $pelanggaranQuery->whereDate('tanggal', '<=', $request->sampai_tanggal);
        }
        $pelanggarans = $pelanggaranQuery->orderBy('tanggal', 'desc')->get();

        // Prestasi query
        $prestasiQuery = CatatanPrestasi::with(['pesertaDidik.orang', 'tahunPelajaran']);
        if ($selectedTahunId && $selectedTahunId !== 'SEMUA') {
            $prestasiQuery->where('tahun_pelajaran_id', $selectedTahunId);
        }
        if ($request->filled('dari_tanggal')) {
            $prestasiQuery->whereDate('tanggal', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $prestasiQuery->whereDate('tanggal', '<=', $request->sampai_tanggal);
        }
        $prestasis = $prestasiQuery->orderBy('tanggal', 'desc')->get();

        $totalPelanggaran = $pelanggarans->count();
        $totalPrestasi    = $prestasis->count();

        $jenis = $request->input('jenis', 'SEMUA');

        return view('admin.laporan.kedisiplinan', compact(
            'pelanggarans', 'prestasis', 'tahunPelajarans', 'selectedTahunId',
            'totalPelanggaran', 'totalPrestasi', 'jenis'
        ));
    }

    public function exportKedisiplinan(Request $request)
    {
        $jenis = $request->input('jenis', 'SEMUA');
        $selectedTahunId = $request->input('tahun_pelajaran_id');

        $pelanggaranQuery = CatatanPelanggaran::with(['pesertaDidik.orang', 'jenisPelanggaran']);
        $prestasiQuery    = CatatanPrestasi::with(['pesertaDidik.orang']);

        if ($selectedTahunId && $selectedTahunId !== 'SEMUA') {
            $pelanggaranQuery->where('tahun_pelajaran_id', $selectedTahunId);
            $prestasiQuery->where('tahun_pelajaran_id', $selectedTahunId);
        }
        if ($request->filled('dari_tanggal')) {
            $pelanggaranQuery->whereDate('tanggal', '>=', $request->dari_tanggal);
            $prestasiQuery->whereDate('tanggal', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $pelanggaranQuery->whereDate('tanggal', '<=', $request->sampai_tanggal);
            $prestasiQuery->whereDate('tanggal', '<=', $request->sampai_tanggal);
        }

        $pelanggarans = $pelanggaranQuery->orderBy('tanggal', 'desc')->get();
        $prestasis    = $prestasiQuery->orderBy('tanggal', 'desc')->get();

        $filename = 'Laporan_Kedisiplinan_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=$filename",
        ];

        $callback = function () use ($pelanggarans, $prestasis, $jenis) {
            $file = fopen('php://output', 'w');
            fputs($file, chr(239) . chr(187) . chr(191));
            fputcsv($file, ['No', 'Kategori', 'Nama Santri', 'NIUP', 'Tanggal', 'Jenis/Judul', 'Keterangan'], ';');

            $no = 1;
            if ($jenis === 'SEMUA' || $jenis === 'PELANGGARAN') {
                foreach ($pelanggarans as $p) {
                    fputcsv($file, [
                        $no++, 'Pelanggaran',
                        $p->pesertaDidik?->orang?->nama_lengkap ?? '-',
                        $p->pesertaDidik?->orang?->niup ?? '-',
                        $p->tanggal?->format('d-m-Y') ?? '-',
                        $p->jenisPelanggaran?->nama ?? '-',
                        $p->keterangan ?? '-',
                    ], ';');
                }
            }
            if ($jenis === 'SEMUA' || $jenis === 'PRESTASI') {
                foreach ($prestasis as $p) {
                    fputcsv($file, [
                        $no++, 'Prestasi',
                        $p->pesertaDidik?->orang?->nama_lengkap ?? '-',
                        $p->pesertaDidik?->orang?->niup ?? '-',
                        $p->tanggal?->format('d-m-Y') ?? '-',
                        $p->judul ?? '-',
                        ($p->tingkat ? "Tingkat: {$p->tingkat}" : '') . ($p->keterangan ? " — {$p->keterangan}" : ''),
                    ], ';');
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // =========================================================================
    // Laporan PSB
    // =========================================================================
    public function psb(Request $request)
    {
        $lembagas   = Lembaga::orderBy('urutan')->get();

        $query = CalonSantri::with(['lembagaTujuan']);

        if ($request->filled('status') && $request->status !== 'SEMUA') {
            $query->where('status', $request->status);
        }
        if ($request->filled('jenis_kelamin') && $request->jenis_kelamin !== 'SEMUA') {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }
        if ($request->filled('lembaga_tujuan_id') && $request->lembaga_tujuan_id !== 'SEMUA') {
            $query->where('lembaga_tujuan_id', $request->lembaga_tujuan_id);
        }

        $calonSantris = $query->orderBy('created_at', 'desc')->get();

        $totalPendaftar = $calonSantris->count();
        $totalDiterima  = $calonSantris->where('status', 'DITERIMA')->count();
        $totalDitolak   = $calonSantris->where('status', 'DITOLAK')->count();
        $totalMenunggu  = $calonSantris->whereIn('status', ['BARU_MASUK', 'VERIFIKASI'])->count();
        $putraCount     = $calonSantris->where('jenis_kelamin', 'L')->count();
        $putriCount     = $calonSantris->where('jenis_kelamin', 'P')->count();

        return view('admin.laporan.psb', compact(
            'calonSantris', 'lembagas',
            'totalPendaftar', 'totalDiterima', 'totalDitolak', 'totalMenunggu',
            'putraCount', 'putriCount'
        ));
    }

    public function exportPsb(Request $request)
    {
        $query = CalonSantri::with(['lembagaTujuan']);
        if ($request->filled('status') && $request->status !== 'SEMUA') {
            $query->where('status', $request->status);
        }
        if ($request->filled('jenis_kelamin') && $request->jenis_kelamin !== 'SEMUA') {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }
        if ($request->filled('lembaga_tujuan_id') && $request->lembaga_tujuan_id !== 'SEMUA') {
            $query->where('lembaga_tujuan_id', $request->lembaga_tujuan_id);
        }

        $calonSantris = $query->orderBy('created_at', 'desc')->get();

        $filename = 'Laporan_PSB_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=$filename",
        ];

        $callback = function () use ($calonSantris) {
            $file = fopen('php://output', 'w');
            fputs($file, chr(239) . chr(187) . chr(191));
            fputcsv($file, ['No', 'No Pendaftaran', 'Nama Lengkap', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'Asal Sekolah', 'Gelombang', 'Lembaga Tujuan', 'Status'], ';');

            foreach ($calonSantris as $i => $c) {
                fputcsv($file, [
                    $i + 1,
                    $c->no_pendaftaran ?? '-',
                    $c->nama_lengkap ?? '-',
                    $c->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
                    $c->tempat_lahir ?? '-',
                    $c->tanggal_lahir?->format('d-m-Y') ?? '-',
                    $c->asal_sekolah ?? '-',
                    $c->gelombang?->nama ?? '-',
                    $c->lembagaTujuan?->nama ?? '-',
                    $c->status ?? '-',
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
