@extends('layouts.app')

@section('title', 'Laporan PSB')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 print:hidden">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Laporan Penerimaan Santri Baru</h1>
        <p class="text-sm text-surface-500 mt-1">Statistik dan rekapitulasi data pendaftaran PSB</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.laporan.psb.export', request()->all()) }}" class="btn-secondary flex items-center gap-2">
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
            <p class="text-[10px] text-surface-500 font-semibold mt-0.5">Laporan Penerimaan Santri Baru (PSB)</p>
        </div>
    </div>
    <div class="mt-4 text-xs font-semibold text-surface-700">
        <span>Tanggal Cetak: {{ now()->isoFormat('D MMMM Y H:mm') }}</span>
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8 print:hidden">
    <div class="bg-primary-50 rounded-2xl border border-primary-200 p-4 text-center">
        <div class="text-2xl font-extrabold text-primary-900 font-mono">{{ number_format($totalPendaftar) }}</div>
        <div class="text-[10px] font-bold text-primary-700 uppercase tracking-wider mt-1">Total Pendaftar</div>
    </div>
    <div class="bg-primary-50 rounded-2xl border border-primary-200 p-4 text-center">
        <div class="text-2xl font-extrabold text-primary-900 font-mono">{{ number_format($totalDiterima) }}</div>
        <div class="text-[10px] font-bold text-primary-700 uppercase tracking-wider mt-1">Diterima</div>
    </div>
    <div class="bg-rose-50 rounded-2xl border border-rose-200 p-4 text-center">
        <div class="text-2xl font-extrabold text-rose-900 font-mono">{{ number_format($totalDitolak) }}</div>
        <div class="text-[10px] font-bold text-rose-700 uppercase tracking-wider mt-1">Ditolak</div>
    </div>
    <div class="bg-amber-50 rounded-2xl border border-amber-200 p-4 text-center">
        <div class="text-2xl font-extrabold text-amber-900 font-mono">{{ number_format($totalMenunggu) }}</div>
        <div class="text-[10px] font-bold text-amber-700 uppercase tracking-wider mt-1">Menunggu</div>
    </div>
    <div class="bg-sky-50 rounded-2xl border border-sky-200 p-4 text-center">
        <div class="text-2xl font-extrabold text-sky-900 font-mono">{{ number_format($putraCount) }}</div>
        <div class="text-[10px] font-bold text-sky-700 uppercase tracking-wider mt-1">Putra</div>
    </div>
    <div class="bg-pink-50 rounded-2xl border border-pink-200 p-4 text-center">
        <div class="text-2xl font-extrabold text-pink-900 font-mono">{{ number_format($putriCount) }}</div>
        <div class="text-[10px] font-bold text-pink-700 uppercase tracking-wider mt-1">Putri</div>
    </div>
</div>

{{-- Filter --}}
<div class="bg-white rounded-2xl shadow-sm border border-surface-200 p-6 mb-8 print:hidden">
    <form action="{{ route('admin.laporan.psb') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-surface-600 uppercase tracking-wider">Gelombang</label>
            <select name="gelombang_id" class="w-full text-sm rounded-xl border border-surface-300 px-3 py-2 bg-surface-50 focus:bg-white text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                <option value="SEMUA">Semua Gelombang</option>
                @foreach($gelombangs as $g)
                    <option value="{{ $g->id }}" {{ request('gelombang_id') == $g->id ? 'selected' : '' }}>{{ $g->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-surface-600 uppercase tracking-wider">Status</label>
            <select name="status" class="w-full text-sm rounded-xl border border-surface-300 px-3 py-2 bg-surface-50 focus:bg-white text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                <option value="SEMUA" {{ request('status') === 'SEMUA' ? 'selected' : '' }}>Semua Status</option>
                <option value="BARU_MASUK" {{ request('status') === 'BARU_MASUK' ? 'selected' : '' }}>Baru Masuk</option>
                <option value="VERIFIKASI" {{ request('status') === 'VERIFIKASI' ? 'selected' : '' }}>Verifikasi</option>
                <option value="DITERIMA" {{ request('status') === 'DITERIMA' ? 'selected' : '' }}>Diterima</option>
                <option value="DITOLAK" {{ request('status') === 'DITOLAK' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-surface-600 uppercase tracking-wider">Jenis Kelamin</label>
            <select name="jenis_kelamin" class="w-full text-sm rounded-xl border border-surface-300 px-3 py-2 bg-surface-50 focus:bg-white text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                <option value="SEMUA" {{ request('jenis_kelamin') === 'SEMUA' ? 'selected' : '' }}>Semua</option>
                <option value="L" {{ request('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ request('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
        </div>
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-surface-600 uppercase tracking-wider">Lembaga Tujuan</label>
            <select name="lembaga_tujuan_id" class="w-full text-sm rounded-xl border border-surface-300 px-3 py-2 bg-surface-50 focus:bg-white text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                <option value="SEMUA">Semua Lembaga</option>
                @foreach($lembagas as $l)
                    <option value="{{ $l->id }}" {{ request('lembaga_tujuan_id') == $l->id ? 'selected' : '' }}>{{ $l->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.laporan.psb') }}" class="btn-secondary text-xs py-2 px-4 rounded-xl flex items-center gap-1.5">
                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Reset
            </a>
            <button type="submit" class="btn-primary text-xs py-2 px-5 rounded-xl flex items-center gap-1.5">
                <i data-lucide="search" class="w-3.5 h-3.5 text-white"></i> <span class="text-white font-bold">Filter</span>
            </button>
        </div>
    </form>
</div>

{{-- Data Table --}}
<x-card title="Daftar Calon Santri ({{ number_format($totalPendaftar) }} data)" :padding="false">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-surface-50 text-surface-600 border-b border-surface-150 uppercase tracking-wider text-[11px] font-bold">
                    <th class="px-4 py-3 w-12">No</th>
                    <th class="px-4 py-3">No Pendaftaran</th>
                    <th class="px-4 py-3">Nama Lengkap</th>
                    <th class="px-4 py-3 text-center">JK</th>
                    <th class="px-4 py-3">Asal Sekolah</th>
                    <th class="px-4 py-3">Gelombang</th>
                    <th class="px-4 py-3">Lembaga Tujuan</th>
                    <th class="px-4 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100 text-surface-750">
                @forelse($calonSantris as $i => $c)
                <tr class="hover:bg-surface-50/40 transition-colors">
                    <td class="px-4 py-3 text-xs text-surface-500 font-mono">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 font-mono text-xs text-primary-700 font-bold">{{ $c->no_pendaftaran ?? '-' }}</td>
                    <td class="px-4 py-3 font-bold text-surface-900">{{ $c->nama_lengkap ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($c->jenis_kelamin === 'L')
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-sky-50 text-sky-700 border border-sky-200">L</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-pink-50 text-pink-700 border border-pink-200">P</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-surface-600">{{ $c->asal_sekolah ?? '-' }}</td>
                    <td class="px-4 py-3 text-xs text-surface-700">{{ $c->gelombang?->nama ?? '-' }}</td>
                    <td class="px-4 py-3 text-xs text-surface-700">{{ $c->lembagaTujuan?->nama ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        @php
                            $statusColors = [
                                'BARU_MASUK' => 'bg-surface-100 text-surface-600 border-surface-200',
                                'VERIFIKASI' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'DITERIMA' => 'bg-success-50 text-success-700 border-success-200',
                                'DITOLAK' => 'bg-rose-50 text-rose-700 border-rose-200',
                            ];
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase border
                            {{ $statusColors[$c->status] ?? 'bg-surface-100 text-surface-600 border-surface-200' }}">
                            {{ str_replace('_', ' ', $c->status ?? '-') }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-surface-550">
                        <i data-lucide="info" class="w-6 h-6 mx-auto mb-2 text-surface-400"></i>
                        Tidak ada data pendaftar PSB yang ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>
@endsection
