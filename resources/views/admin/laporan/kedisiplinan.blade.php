@extends('layouts.app')

@section('title', 'Laporan Kedisiplinan')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 print:hidden">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Laporan Kedisiplinan Santri</h1>
        <p class="text-sm text-surface-500 mt-1">Catatan pelanggaran & prestasi santri</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.laporan.kedisiplinan.export', request()->all()) }}" class="btn-secondary flex items-center gap-2">
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
            <p class="text-[10px] text-surface-500 font-semibold mt-0.5">Laporan Rekapitulasi Kedisiplinan Santri</p>
        </div>
    </div>
    <div class="mt-4 text-xs font-semibold text-surface-700">
        <span>Tanggal Cetak: {{ now()->isoFormat('D MMMM Y H:mm') }}</span>
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8 print:hidden">
    <div class="bg-rose-50 rounded-2xl border border-rose-200 p-5 flex items-center justify-between">
        <div>
            <div class="text-xs font-bold text-rose-800 uppercase tracking-wider">Total Pelanggaran</div>
            <div class="text-3xl font-extrabold text-rose-950 font-mono mt-1">{{ number_format($totalPelanggaran) }}</div>
            <div class="text-[11px] text-rose-600 mt-1 font-semibold">Catatan poin indisipliner</div>
        </div>
        <div class="p-3.5 bg-rose-500 text-white rounded-2xl"><i data-lucide="alert-triangle" class="w-6 h-6"></i></div>
    </div>
    <div class="bg-amber-50 rounded-2xl border border-amber-200 p-5 flex items-center justify-between">
        <div>
            <div class="text-xs font-bold text-amber-800 uppercase tracking-wider">Total Prestasi</div>
            <div class="text-3xl font-extrabold text-amber-950 font-mono mt-1">{{ number_format($totalPrestasi) }}</div>
            <div class="text-[11px] text-amber-600 mt-1 font-semibold">Catatan pencapaian santri</div>
        </div>
        <div class="p-3.5 bg-amber-500 text-white rounded-2xl"><i data-lucide="trophy" class="w-6 h-6"></i></div>
    </div>
    <div class="bg-primary-50 rounded-2xl border border-primary-200 p-5 flex items-center justify-between">
        <div>
            <div class="text-xs font-bold text-primary-800 uppercase tracking-wider">Rasio P:P</div>
            <div class="text-3xl font-extrabold text-primary-950 font-mono mt-1">{{ $totalPelanggaran }}:{{ $totalPrestasi }}</div>
            <div class="text-[11px] text-primary-600 mt-1 font-semibold">Pelanggaran vs Prestasi</div>
        </div>
        <div class="p-3.5 bg-primary-500 text-white rounded-2xl"><i data-lucide="scale" class="w-6 h-6"></i></div>
    </div>
</div>

{{-- Filter --}}
<div class="bg-white rounded-2xl shadow-sm border border-surface-200 p-6 mb-8 print:hidden">
    <form action="{{ route('admin.laporan.kedisiplinan') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-surface-600 uppercase tracking-wider">Tahun Pelajaran</label>
            <select name="tahun_pelajaran_id" class="w-full text-sm rounded-xl border border-surface-300 px-3 py-2 bg-surface-50 focus:bg-white text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                <option value="SEMUA">Semua Tahun</option>
                @foreach($tahunPelajarans as $tp)
                    <option value="{{ $tp->id }}" {{ $selectedTahunId == $tp->id ? 'selected' : '' }}>{{ $tp->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-surface-600 uppercase tracking-wider">Dari Tanggal</label>
            <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}" class="w-full text-sm rounded-xl border border-surface-300 px-3 py-2 bg-surface-50 focus:bg-white text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
        </div>
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-surface-600 uppercase tracking-wider">Sampai Tanggal</label>
            <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" class="w-full text-sm rounded-xl border border-surface-300 px-3 py-2 bg-surface-50 focus:bg-white text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
        </div>
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-surface-600 uppercase tracking-wider">Kategori</label>
            <select name="jenis" class="w-full text-sm rounded-xl border border-surface-300 px-3 py-2 bg-surface-50 focus:bg-white text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                <option value="SEMUA" {{ $jenis === 'SEMUA' ? 'selected' : '' }}>Semua</option>
                <option value="PELANGGARAN" {{ $jenis === 'PELANGGARAN' ? 'selected' : '' }}>Pelanggaran</option>
                <option value="PRESTASI" {{ $jenis === 'PRESTASI' ? 'selected' : '' }}>Prestasi</option>
            </select>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.laporan.kedisiplinan') }}" class="btn-secondary text-xs py-2 px-4 rounded-xl flex items-center gap-1.5">
                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Reset
            </a>
            <button type="submit" class="btn-primary text-xs py-2 px-5 rounded-xl flex items-center gap-1.5">
                <i data-lucide="search" class="w-3.5 h-3.5 text-white"></i> <span class="text-white font-bold">Filter</span>
            </button>
        </div>
    </form>
</div>

{{-- Pelanggaran Table --}}
@if($jenis === 'SEMUA' || $jenis === 'PELANGGARAN')
<x-card title="Catatan Pelanggaran ({{ $totalPelanggaran }})" :padding="false" class="mb-8">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-rose-50/50 text-surface-600 border-b border-surface-150 uppercase tracking-wider text-[11px] font-bold">
                    <th class="px-4 py-3 w-12">No</th>
                    <th class="px-4 py-3">Nama Santri</th>
                    <th class="px-4 py-3">NIUP</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Jenis Pelanggaran</th>
                    <th class="px-4 py-3">Keterangan</th>
                    <th class="px-4 py-3">Tindakan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100 text-surface-750">
                @forelse($pelanggarans as $i => $p)
                <tr class="hover:bg-surface-50/40 transition-colors">
                    <td class="px-4 py-3 text-xs text-surface-500 font-mono">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 font-bold text-surface-900">{{ $p->pesertaDidik?->orang?->nama_lengkap ?? '-' }}</td>
                    <td class="px-4 py-3 font-mono text-xs text-primary-700">{{ $p->pesertaDidik?->orang?->niup ?? '-' }}</td>
                    <td class="px-4 py-3 text-xs font-mono text-surface-600">{{ $p->tanggal?->format('d-m-Y') ?? '-' }}</td>
                    <td class="px-4 py-3 text-xs">
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                            {{ $p->jenisPelanggaran?->nama ?? '-' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-surface-600 max-w-[200px] truncate">{{ $p->keterangan ?? '-' }}</td>
                    <td class="px-4 py-3 text-xs text-surface-600">{{ $p->tindakan ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-8 text-center text-surface-550">Tidak ada catatan pelanggaran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>
@endif

{{-- Prestasi Table --}}
@if($jenis === 'SEMUA' || $jenis === 'PRESTASI')
<x-card title="Catatan Prestasi ({{ $totalPrestasi }})" :padding="false">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-amber-50/50 text-surface-600 border-b border-surface-150 uppercase tracking-wider text-[11px] font-bold">
                    <th class="px-4 py-3 w-12">No</th>
                    <th class="px-4 py-3">Nama Santri</th>
                    <th class="px-4 py-3">NIUP</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Judul Prestasi</th>
                    <th class="px-4 py-3 text-center">Tingkat</th>
                    <th class="px-4 py-3">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100 text-surface-750">
                @forelse($prestasis as $i => $p)
                <tr class="hover:bg-surface-50/40 transition-colors">
                    <td class="px-4 py-3 text-xs text-surface-500 font-mono">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 font-bold text-surface-900">{{ $p->pesertaDidik?->orang?->nama_lengkap ?? '-' }}</td>
                    <td class="px-4 py-3 font-mono text-xs text-primary-700">{{ $p->pesertaDidik?->orang?->niup ?? '-' }}</td>
                    <td class="px-4 py-3 text-xs font-mono text-surface-600">{{ $p->tanggal?->format('d-m-Y') ?? '-' }}</td>
                    <td class="px-4 py-3 text-xs font-semibold text-surface-900">{{ $p->judul ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200">
                            {{ $p->tingkat ?? '-' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-surface-600 max-w-[200px] truncate">{{ $p->keterangan ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-8 text-center text-surface-550">Tidak ada catatan prestasi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>
@endif
@endsection
