<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\KomponenBiaya;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $dariTanggal = $request->filled('dari_tanggal') 
            ? Carbon::parse($request->dari_tanggal)->startOfDay() 
            : now()->startOfMonth()->startOfDay();

        $sampaiTanggal = $request->filled('sampai_tanggal') 
            ? Carbon::parse($request->sampai_tanggal)->endOfDay() 
            : now()->endOfDay();

        $query = Pembayaran::with(['tagihan.pesertaDidik.orang', 'tagihan.komponenBiaya'])
            ->whereBetween('tanggal_bayar', [$dariTanggal, $sampaiTanggal]);

        if ($request->filled('metode') && $request->metode !== 'SEMUA') {
            $query->where('metode', $request->metode);
        }

        if ($request->filled('komponen_biaya_id') && $request->komponen_biaya_id !== 'SEMUA') {
            $query->whereHas('tagihan', function($q) use ($request) {
                $q->where('komponen_biaya_id', $request->komponen_biaya_id);
            });
        }

        $pembayarans = $query->orderBy('tanggal_bayar', 'desc')->get();

        $totalNominal = $pembayarans->sum('jumlah');
        $totalTransaksi = $pembayarans->count();
        $rataRata = $totalTransaksi > 0 ? $totalNominal / $totalTransaksi : 0;

        $komponens = KomponenBiaya::where('is_active', true)->get();

        // Detect current panel prefix from current route name
        $routePrefix = str_contains($request->route()->getName(), 'admin') ? 'admin' : 'bendahara';

        return view('bendahara.laporan.index', compact(
            'pembayarans', 'dariTanggal', 'sampaiTanggal', 'totalNominal', 
            'totalTransaksi', 'rataRata', 'komponens', 'routePrefix'
        ));
    }

    public function export(Request $request)
    {
        $dariTanggal = $request->filled('dari_tanggal') 
            ? Carbon::parse($request->dari_tanggal)->startOfDay() 
            : now()->startOfMonth()->startOfDay();

        $sampaiTanggal = $request->filled('sampai_tanggal') 
            ? Carbon::parse($request->sampai_tanggal)->endOfDay() 
            : now()->endOfDay();

        $query = Pembayaran::with(['tagihan.pesertaDidik.orang', 'tagihan.komponenBiaya'])
            ->whereBetween('tanggal_bayar', [$dariTanggal, $sampaiTanggal]);

        if ($request->filled('metode') && $request->metode !== 'SEMUA') {
            $query->where('metode', $request->metode);
        }

        if ($request->filled('komponen_biaya_id') && $request->komponen_biaya_id !== 'SEMUA') {
            $query->whereHas('tagihan', function($q) use ($request) {
                $q->where('komponen_biaya_id', $request->komponen_biaya_id);
            });
        }

        $pembayarans = $query->orderBy('tanggal_bayar', 'asc')->get();

        $filename = "Laporan_Keuangan_Nurul_Furqon_" . $dariTanggal->format('Ymd') . "_to_" . $sampaiTanggal->format('Ymd') . ".csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No. Transaksi', 'Tanggal Bayar', 'Nama Santri', 'NIUP', 'Komponen Biaya', 'Metode', 'Nominal (Rp)', 'Keterangan'];

        $callback = function() use($pembayarans, $columns) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 reading
            fputs($file, chr(239) . chr(187) . chr(191));
            
            fputcsv($file, $columns, ';');

            foreach ($pembayarans as $p) {
                fputcsv($file, [
                    $p->no_transaksi,
                    $p->tanggal_bayar->format('d-m-Y H:i'),
                    $p->tagihan->pesertaDidik->orang->nama_lengkap ?? '-',
                    $p->tagihan->pesertaDidik->orang->niup ?? '-',
                    $p->tagihan->komponenBiaya->nama ?? '-',
                    $p->metode,
                    $p->jumlah,
                    $p->keterangan ?? '-'
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
