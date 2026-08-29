@extends('layouts.app')

@section('title', 'Laporan Presensi')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 print:hidden">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Laporan Rekapitulasi Presensi</h1>
        <p class="text-sm text-surface-500 mt-1">
            Periode: <span class="font-bold text-surface-900">{{ $dariTanggal->format('d M Y') }}</span> s.d. <span class="font-bold text-surface-900">{{ $sampaiTanggal->format('d M Y') }}</span>
        </p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.laporan.presensi.export', request()->all()) }}" class="btn-secondary flex items-center gap-2">
            <i data-lucide="download" class="w-4 h-4"></i>
            <span>Unduh CSV</span>
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
        #sidebar, #topbar, .print\:hidden, form { display: none !important; }
        body, main, #main-content { padding: 0 !important; margin: 0 !important; background: white !important; }
        .print-report-header { display: block !important; }
        .shadow-sm, .border-surface-200 { border: none !important; box-shadow: none !important; }
        table { border-collapse: collapse !important; width: 100% !important; }
        th, td { border: 1px solid #e2e8f0 !important; padding: 6px 10px !important; font-size: 11px !important; }
    }
    .print-report-header { display: none; }
</style>

{{-- Print Header --}}
<div class="print-report-header mb-8 border-b-2 border-surface-900 pb-4">
    <div class="flex items-center gap-4">
        <picture>
            <source srcset="{{ asset('images/logo-pesantren-256.webp') }}" type="image/webp">
            <img src="{{ asset('images/logo-pesantren-256.webp') }}" alt="Logo" class="w-16 h-16 object-contain">
        </picture>
        <div>
            <h2 class="text-xs font-bold text-surface-500 uppercase tracking-widest">Yayasan Nurul Furqon</h2>
            <h1 class="text-xl font-extrabold text-surface-900 font-heading leading-tight">PONDOK PESANTREN NURUL FURQON</h1>
            <p class="text-[10px] text-surface-500 font-semibold mt-0.5">Laporan Rekapitulasi Presensi Santri</p>
        </div>
    </div>
    <div class="mt-4 text-xs font-semibold text-surface-700 flex justify-between">
        <span>Periode: {{ $dariTanggal->format('d F Y') }} s.d. {{ $sampaiTanggal->format('d F Y') }}</span>
        <span>Tanggal Cetak: {{ now()->isoFormat('D MMMM Y H:mm') }}</span>
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8 print:hidden">
    <div class="bg-surface-50 rounded-2xl border border-surface-200 p-4 text-center">
        <div class="text-2xl font-extrabold text-surface-900 font-mono">{{ number_format($totalRecords) }}</div>
        <div class="text-[10px] font-bold text-surface-500 uppercase tracking-wider mt-1">Total Record</div>
    </div>
    <div class="bg-primary-50 rounded-2xl border border-primary-200 p-4 text-center">
        <div class="text-2xl font-extrabold text-primary-900 font-mono">{{ $globalHadir }}%</div>
        <div class="text-[10px] font-bold text-primary-700 uppercase tracking-wider mt-1">Hadir</div>
    </div>
    <div class="bg-sky-50 rounded-2xl border border-sky-200 p-4 text-center">
        <div class="text-2xl font-extrabold text-sky-900 font-mono">{{ $globalSakit }}%</div>
        <div class="text-[10px] font-bold text-sky-700 uppercase tracking-wider mt-1">Sakit</div>
    </div>
    <div class="bg-amber-50 rounded-2xl border border-amber-200 p-4 text-center">
        <div class="text-2xl font-extrabold text-amber-900 font-mono">{{ $globalIzin }}%</div>
        <div class="text-[10px] font-bold text-amber-700 uppercase tracking-wider mt-1">Izin</div>
    </div>
    <div class="bg-rose-50 rounded-2xl border border-rose-200 p-4 text-center">
        <div class="text-2xl font-extrabold text-rose-900 font-mono">{{ $globalAlpa }}%</div>
        <div class="text-[10px] font-bold text-rose-700 uppercase tracking-wider mt-1">Alpa</div>
    </div>
</div>

{{-- Filter --}}
<div class="bg-white rounded-2xl shadow-sm border border-surface-200 p-6 mb-8 print:hidden">
    <form action="{{ route('admin.laporan.presensi') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-surface-600 uppercase tracking-wider">Dari Tanggal</label>
            <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal', $dariTanggal->format('Y-m-d')) }}" class="w-full text-sm rounded-xl border border-surface-300 px-3 py-2 bg-surface-50 focus:bg-white text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
        </div>
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-surface-600 uppercase tracking-wider">Sampai Tanggal</label>
            <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal', $sampaiTanggal->format('Y-m-d')) }}" class="w-full text-sm rounded-xl border border-surface-300 px-3 py-2 bg-surface-50 focus:bg-white text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
        </div>
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-surface-600 uppercase tracking-wider">Jenis Presensi</label>
            <select name="jenis_presensi_id" class="w-full text-sm rounded-xl border border-surface-300 px-3 py-2 bg-surface-50 focus:bg-white text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                <option value="SEMUA">Semua Jenis</option>
                @foreach($jenisPresensis as $jp)
                    <option value="{{ $jp->id }}" {{ request('jenis_presensi_id') == $jp->id ? 'selected' : '' }}>{{ $jp->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-surface-600 uppercase tracking-wider">Rombel / Kelas</label>
            <select name="rombel_id" class="w-full text-sm rounded-xl border border-surface-300 px-3 py-2 bg-surface-50 focus:bg-white text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                <option value="SEMUA">Semua Rombel</option>
                @foreach($rombels as $r)
                    <option value="{{ $r->id }}" {{ request('rombel_id') == $r->id ? 'selected' : '' }}>{{ $r->nama }} @if($r->lembaga)({{ $r->lembaga->singkatan ?? $r->lembaga->nama }})@endif</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.laporan.presensi') }}" class="btn-secondary text-xs py-2 px-4 rounded-xl flex items-center gap-1.5">
                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Reset
            </a>
            <button type="submit" class="btn-primary text-xs py-2 px-5 rounded-xl flex items-center gap-1.5">
                <i data-lucide="search" class="w-3.5 h-3.5 text-white"></i> <span class="text-white font-bold">Filter</span>
            </button>
        </div>
    </form>
</div>

{{-- Rekap Table --}}
<x-card title="Rekapitulasi Per Santri ({{ $rekapPerSantri->count() }} santri)" :padding="false">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-surface-50 text-surface-600 border-b border-surface-150 uppercase tracking-wider text-[11px] font-bold">
                    <th class="px-4 py-3 w-12">No</th>
                    <th class="px-4 py-3">NIUP</th>
                    <th class="px-4 py-3 min-w-[180px]">Nama Santri</th>
                    <th class="px-3 py-3 text-center w-16 bg-surface-100">Total</th>
                    <th class="px-3 py-3 text-center w-14 bg-primary-50 text-primary-800">H</th>
                    <th class="px-3 py-3 text-center w-14 bg-sky-50 text-sky-800">S</th>
                    <th class="px-3 py-3 text-center w-14 bg-amber-50 text-amber-800">I</th>
                    <th class="px-3 py-3 text-center w-14 bg-rose-50 text-rose-800">A</th>
                    <th class="px-4 py-3 text-center w-24">% Hadir</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100 text-surface-750">
                @forelse($rekapPerSantri as $i => $r)
                <tr class="hover:bg-surface-50/40 transition-colors">
                    <td class="px-4 py-3 text-xs text-surface-500 font-mono">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 font-mono text-xs text-primary-700 font-bold">{{ $r->peserta?->orang?->niup ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <div class="font-bold text-surface-900">{{ $r->peserta?->orang?->nama_lengkap ?? '-' }}</div>
                    </td>
                    <td class="px-3 py-3 text-center font-bold text-surface-900 bg-surface-50/30">{{ $r->total }}</td>
                    <td class="px-3 py-3 text-center font-black text-primary-700 bg-primary-50/40">{{ $r->hadir }}</td>
                    <td class="px-3 py-3 text-center font-black text-sky-700 bg-sky-50/40">{{ $r->sakit }}</td>
                    <td class="px-3 py-3 text-center font-black text-amber-700 bg-amber-50/40">{{ $r->izin }}</td>
                    <td class="px-3 py-3 text-center font-black text-rose-700 bg-rose-50/40">{{ $r->alpa }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold
                            {{ $r->persen >= 85 ? 'bg-primary-100 text-primary-800' : ($r->persen >= 70 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                            {{ $r->persen }}%
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-12 text-center text-surface-550">
                        <i data-lucide="info" class="w-6 h-6 mx-auto mb-2 text-surface-400"></i>
                        Tidak ada data presensi untuk periode dan filter yang dipilih.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>
@endsection
