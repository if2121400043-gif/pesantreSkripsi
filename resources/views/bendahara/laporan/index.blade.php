@extends('layouts.bendahara')

@section('title', 'Laporan Keuangan')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 print:hidden">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Laporan Keuangan</h1>
        <p class="text-sm text-surface-500 mt-1">
            Periode: <span class="font-bold text-surface-900">{{ $dariTanggal->format('d M Y') }}</span> s.d. <span class="font-bold text-surface-900">{{ $sampaiTanggal->format('d M Y') }}</span>
        </p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route($routePrefix . '.laporan-keuangan.export', request()->all()) }}" class="btn-secondary flex items-center gap-2">
            <i data-lucide="download" class="w-4 h-4"></i>
            <span>Unduh CSV (Excel)</span>
        </a>
        <button onclick="window.print()" class="btn-primary flex items-center gap-2">
            <i data-lucide="printer" class="w-4 h-4"></i>
            <span class="text-white">Cetak Laporan</span>
        </button>
    </div>
</div>
@endsection

@section('content')
<style>
    @media print {
        #sidebar, #topbar, .print\:hidden, form {
            display: none !important;
        }
        body, main, #main-content {
            padding: 0 !important;
            margin: 0 !important;
            background: white !important;
        }
        .print-report-header {
            display: block !important;
        }
        .shadow-sm, .border-surface-200 {
            border: none !important;
            box-shadow: none !important;
        }
        table {
            border-collapse: collapse !important;
            width: 100% !important;
        }
        th, td {
            border: 1px solid #e2e8f0 !important;
            padding: 8px 12px !important;
        }
    }
    .print-report-header {
        display: none;
    }
</style>

{{-- Header Laporan Cetak (Hanya tampil saat di-print) --}}
<div class="print-report-header mb-8 border-b-2 border-surface-900 pb-4">
    <div class="flex items-center gap-4">
        <picture>
            <source srcset="{{ asset('images/logo-pesantren-256.webp') }}" type="image/webp">
            <img src="{{ asset('images/logo-pesantren-256.webp') }}" alt="Logo Pesantren" class="w-16 h-16 object-contain">
        </picture>
        <div>
            <h2 class="text-xs font-bold text-surface-500 uppercase tracking-widest">Yayasan Nurul Furqon</h2>
            <h1 class="text-xl font-extrabold text-surface-900 font-heading leading-tight">PONDOK PESANTREN NURUL FURQON</h1>
            <p class="text-xs text-surface-650 mt-1">Jl. Raya Pesantren No. 1, Kepuhkembeng, Jombang, Jawa Timur</p>
            <p class="text-[10px] text-surface-500 font-semibold mt-0.5">Laporan Rekapitulasi Kasir Keuangan</p>
        </div>
    </div>
    <div class="mt-4 text-xs font-semibold text-surface-700 flex justify-between">
        <span>Periode: {{ $dariTanggal->format('d F Y') }} s.d. {{ $sampaiTanggal->format('d F Y') }}</span>
        <span>Tanggal Cetak: {{ now()->isoFormat('D MMMM Y H:i') }}</span>
    </div>
</div>

{{-- Card Filter (Hidden on print) --}}
<div class="bg-white rounded-2xl shadow-sm border border-surface-200 p-6 mb-8 print:hidden">
    <form action="{{ route(request()->route()->getName()) }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div class="space-y-1.5">
            <label for="dari_tanggal" class="block text-xs font-bold text-surface-600 uppercase tracking-wider">Dari Tanggal</label>
            <input type="date" name="dari_tanggal" id="dari_tanggal" value="{{ request('dari_tanggal', $dariTanggal->format('Y-m-d')) }}" 
                   class="w-full text-sm rounded-xl border border-surface-300 px-3 py-2 bg-surface-50 focus:bg-white text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors">
        </div>
        
        <div class="space-y-1.5">
            <label for="sampai_tanggal" class="block text-xs font-bold text-surface-600 uppercase tracking-wider">Sampai Tanggal</label>
            <input type="date" name="sampai_tanggal" id="sampai_tanggal" value="{{ request('sampai_tanggal', $sampaiTanggal->format('Y-m-d')) }}" 
                   class="w-full text-sm rounded-xl border border-surface-300 px-3 py-2 bg-surface-50 focus:bg-white text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors">
        </div>

        <div class="space-y-1.5">
            <label for="metode" class="block text-xs font-bold text-surface-600 uppercase tracking-wider">Metode Bayar</label>
            <select name="metode" id="metode" 
                    class="w-full text-sm rounded-xl border border-surface-300 px-3 py-2 bg-surface-50 focus:bg-white text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors">
                <option value="SEMUA" {{ request('metode') === 'SEMUA' ? 'selected' : '' }}>Semua Metode</option>
                <option value="TUNAI" {{ request('metode') === 'TUNAI' ? 'selected' : '' }}>Tunai (Cash)</option>
                <option value="TRANSFER" {{ request('metode') === 'TRANSFER' ? 'selected' : '' }}>Transfer Bank</option>
                <option value="QRIS" {{ request('metode') === 'QRIS' ? 'selected' : '' }}>QRIS</option>
            </select>
        </div>

        <div class="space-y-1.5">
            <label for="komponen_biaya_id" class="block text-xs font-bold text-surface-600 uppercase tracking-wider">Jenis Biaya</label>
            <select name="komponen_biaya_id" id="komponen_biaya_id" 
                    class="w-full text-sm rounded-xl border border-surface-300 px-3 py-2 bg-surface-50 focus:bg-white text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors">
                <option value="SEMUA" {{ request('komponen_biaya_id') === 'SEMUA' ? 'selected' : '' }}>Semua Jenis</option>
                @foreach($komponens as $k)
                    <option value="{{ $k->id }}" {{ request('komponen_biaya_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="md:col-span-4 flex justify-end gap-3 pt-2">
            <a href="{{ route(request()->route()->getName()) }}" class="btn-secondary text-xs py-2 px-4 rounded-xl flex items-center gap-1.5">
                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Reset
            </a>
            <button type="submit" class="btn-primary text-xs py-2 px-5 rounded-xl flex items-center gap-1.5">
                <i data-lucide="search" class="w-3.5 h-3.5 text-white"></i> <span class="text-white font-bold">Filter Laporan</span>
            </button>
        </div>
    </form>
</div>

{{-- Rekap Card Dashboard --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    {{-- Card 1: Total Transaksi --}}
    <div class="bg-primary-50 rounded-2xl border border-primary-200 p-5 flex items-center justify-between transition-transform hover:scale-101">
        <div>
            <div class="text-xs font-bold text-primary-800 uppercase tracking-wider">Total Transaksi</div>
            <div class="text-3xl font-extrabold text-primary-950 font-mono mt-1.5">{{ number_format($totalTransaksi, 0, ',', '.') }}</div>
            <div class="text-[11px] text-primary-650 mt-1 font-semibold">Jumlah pembayaran diterima</div>
        </div>
        <div class="p-3.5 bg-primary-500 text-white rounded-2xl">
            <i data-lucide="files" class="w-6 h-6"></i>
        </div>
    </div>

    {{-- Card 2: Total Nominal Masuk --}}
    <div class="bg-success-50 rounded-2xl border border-success-200 p-5 flex items-center justify-between transition-transform hover:scale-101">
        <div>
            <div class="text-xs font-bold text-success-800 uppercase tracking-wider">Total Kas Masuk</div>
            <div class="text-3xl font-extrabold text-success-950 font-mono mt-1.5">Rp {{ number_format($totalNominal, 0, ',', '.') }}</div>
            <div class="text-[11px] text-success-650 mt-1 font-semibold">Uang masuk riil dari kasir</div>
        </div>
        <div class="p-3.5 bg-success-500 text-white rounded-2xl">
            <i data-lucide="wallet" class="w-6 h-6"></i>
        </div>
    </div>

    {{-- Card 3: Rata-rata --}}
    <div class="bg-info-50 rounded-2xl border border-info-200 p-5 flex items-center justify-between transition-transform hover:scale-101 font-sans">
        <div>
            <div class="text-xs font-bold text-info-800 uppercase tracking-wider">Rata-rata Transaksi</div>
            <div class="text-3xl font-extrabold text-info-950 font-mono mt-1.5">Rp {{ number_format($rataRata, 0, ',', '.') }}</div>
            <div class="text-[11px] text-info-650 mt-1 font-semibold">Rasio nominal per transaksi</div>
        </div>
        <div class="p-3.5 bg-info-500 text-white rounded-2xl">
            <i data-lucide="calculator" class="w-6 h-6"></i>
        </div>
    </div>
</div>

{{-- Detail Transaksi List --}}
<x-card title="Detail Transaksi Kas Masuk" :padding="false">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-surface-50 text-surface-600 border-b border-surface-150 uppercase tracking-wider text-[11px] font-bold">
                    <th class="px-6 py-3.5">No. Transaksi</th>
                    <th class="px-6 py-3.5">Tanggal</th>
                    <th class="px-6 py-3.5">Nama Santri</th>
                    <th class="px-6 py-3.5">NIUP</th>
                    <th class="px-6 py-3.5">Komponen</th>
                    <th class="px-6 py-3.5 text-center">Metode</th>
                    <th class="px-6 py-3.5 text-right">Nominal (Rp)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100 text-surface-750 font-sans">
                @forelse($pembayarans as $p)
                <tr class="hover:bg-surface-50/40 transition-colors">
                    <td class="px-6 py-4 font-mono font-bold text-primary-700 text-xs">
                        {{ $p->no_transaksi }}
                    </td>
                    <td class="px-6 py-4 text-xs font-mono text-surface-500">
                        {{ $p->tanggal_bayar->format('d-m-Y H:i') }}
                    </td>
                    <td class="px-6 py-4 font-bold text-surface-900">
                        {{ $p->tagihan->pesertaDidik->orang->nama_lengkap ?? '-' }}
                    </td>
                    <td class="px-6 py-4 font-mono text-xs text-surface-600">
                        {{ $p->tagihan->pesertaDidik->orang->niup ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-xs font-medium text-surface-700">
                        {{ $p->tagihan->komponenBiaya->nama ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($p->metode === 'TUNAI')
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wider bg-success-50 text-success-700 border border-success-200">TUNAI</span>
                        @elseif($p->metode === 'TRANSFER')
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wider bg-primary-50 text-primary-700 border border-primary-200">TRANSFER</span>
                        @elseif($p->metode === 'QRIS')
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wider bg-accent-50 text-accent-700 border border-accent-200">QRIS</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wider bg-surface-50 text-surface-700 border border-surface-200">{{ $p->metode }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right font-mono font-bold text-success-600">
                        Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-surface-550">
                        Tidak ada transaksi kas masuk yang ditemukan dalam kriteria filter ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($pembayarans->isNotEmpty())
            <tfoot>
                <tr class="bg-surface-50/50 border-t border-surface-150 font-bold text-surface-900">
                    <td colspan="6" class="px-6 py-4 text-right uppercase tracking-wider text-xs">Total Rekap Kas Masuk:</td>
                    <td class="px-6 py-4 text-right font-mono text-base text-success-650">
                        Rp {{ number_format($totalNominal, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</x-card>
@endsection
