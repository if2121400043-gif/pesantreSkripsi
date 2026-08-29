@extends('layouts.app')

@section('title', 'Pusat Laporan & Rekapitulasi')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Pusat Laporan & Rekapitulasi</h1>
        <p class="text-sm text-surface-500 mt-1">
            Akses seluruh laporan dan rekapitulasi data pesantren dari satu tempat.
            @if($tahunAktif)
                <span class="font-semibold text-primary-700">Tahun Pelajaran: {{ $tahunAktif->nama }}</span>
            @endif
        </p>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-8">

    {{-- Overview Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-primary-50 rounded-2xl border border-primary-200 p-5 text-center transition-transform hover:scale-[1.02]">
            <div class="text-3xl font-extrabold text-primary-900 font-mono">{{ number_format($stats['totalSantriAktif']) }}</div>
            <div class="text-[11px] font-bold text-primary-700 uppercase tracking-wider mt-1">Santri Aktif</div>
        </div>
        <div class="bg-primary-50 rounded-2xl border border-primary-200 p-5 text-center transition-transform hover:scale-[1.02]">
            <div class="text-3xl font-extrabold text-primary-900 font-mono">{{ number_format($stats['totalPresensi']) }}</div>
            <div class="text-[11px] font-bold text-primary-700 uppercase tracking-wider mt-1">Record Presensi</div>
        </div>
        <div class="bg-rose-50 rounded-2xl border border-rose-200 p-5 text-center transition-transform hover:scale-[1.02]">
            <div class="text-3xl font-extrabold text-rose-900 font-mono">{{ number_format($stats['totalPelanggaran']) }}</div>
            <div class="text-[11px] font-bold text-rose-700 uppercase tracking-wider mt-1">Pelanggaran</div>
        </div>
        <div class="bg-amber-50 rounded-2xl border border-amber-200 p-5 text-center transition-transform hover:scale-[1.02]">
            <div class="text-3xl font-extrabold text-amber-900 font-mono">{{ number_format($stats['totalPrestasi']) }}</div>
            <div class="text-[11px] font-bold text-amber-700 uppercase tracking-wider mt-1">Prestasi</div>
        </div>
        <div class="bg-sky-50 rounded-2xl border border-sky-200 p-5 text-center transition-transform hover:scale-[1.02]">
            <div class="text-3xl font-extrabold text-sky-900 font-mono">{{ number_format($stats['totalPendaftar']) }}</div>
            <div class="text-[11px] font-bold text-sky-700 uppercase tracking-wider mt-1">Pendaftar PSB</div>
        </div>
    </div>

    {{-- Navigation Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        {{-- Laporan Santri --}}
        <a href="{{ route('admin.laporan.santri') }}" class="group bg-white rounded-3xl p-6 border border-surface-200 shadow-sm hover:shadow-xl hover:border-primary-400 hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-primary-500/5 rounded-full -translate-x-6 -translate-y-6 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative">
                <div class="w-12 h-12 bg-primary-100 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-primary-500 transition-colors">
                    <i data-lucide="users" class="w-6 h-6 text-primary-600 group-hover:text-white transition-colors"></i>
                </div>
                <h3 class="text-lg font-extrabold text-surface-900 group-hover:text-primary-700 transition-colors">Laporan Data Santri</h3>
                <p class="text-xs text-surface-500 mt-2 leading-relaxed">Statistik santri per lembaga, gender, status. Export daftar santri aktif ke CSV.</p>
                <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-primary-600 group-hover:text-primary-700">
                    <span>Buka Laporan</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </div>
        </a>

        {{-- Laporan Presensi --}}
        <a href="{{ route('admin.laporan.presensi') }}" class="group bg-white rounded-3xl p-6 border border-surface-200 shadow-sm hover:shadow-xl hover:border-primary-400 hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-primary-500/5 rounded-full -translate-x-6 -translate-y-6 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative">
                <div class="w-12 h-12 bg-primary-100 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-primary-500 transition-colors">
                    <i data-lucide="check-square" class="w-6 h-6 text-primary-600 group-hover:text-white transition-colors"></i>
                </div>
                <h3 class="text-lg font-extrabold text-surface-900 group-hover:text-primary-700 transition-colors">Laporan Presensi</h3>
                <p class="text-xs text-surface-500 mt-2 leading-relaxed">Rekapitulasi kehadiran santri per jenis presensi, rombel, asrama & periode.</p>
                <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-primary-600 group-hover:text-primary-700">
                    <span>Buka Laporan</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </div>
        </a>

        {{-- Laporan Keuangan --}}
        <a href="{{ route('admin.laporan-keuangan.index') }}" class="group bg-white rounded-3xl p-6 border border-surface-200 shadow-sm hover:shadow-xl hover:border-accent-400 hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-accent-500/5 rounded-full -translate-x-6 -translate-y-6 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative">
                <div class="w-12 h-12 bg-accent-100 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-accent-500 transition-colors">
                    <i data-lucide="wallet" class="w-6 h-6 text-accent-600 group-hover:text-white transition-colors"></i>
                </div>
                <h3 class="text-lg font-extrabold text-surface-900 group-hover:text-accent-700 transition-colors">Laporan Keuangan</h3>
                <p class="text-xs text-surface-500 mt-2 leading-relaxed">Rekapitulasi kas masuk, transaksi pembayaran, filter per metode & periode.</p>
                <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-accent-600 group-hover:text-accent-700">
                    <span>Buka Laporan</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </div>
        </a>

        {{-- Laporan Kedisiplinan --}}
        <a href="{{ route('admin.laporan.kedisiplinan') }}" class="group bg-white rounded-3xl p-6 border border-surface-200 shadow-sm hover:shadow-xl hover:border-rose-400 hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-rose-500/5 rounded-full -translate-x-6 -translate-y-6 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative">
                <div class="w-12 h-12 bg-rose-100 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-rose-500 transition-colors">
                    <i data-lucide="shield-alert" class="w-6 h-6 text-rose-600 group-hover:text-white transition-colors"></i>
                </div>
                <h3 class="text-lg font-extrabold text-surface-900 group-hover:text-rose-700 transition-colors">Laporan Kedisiplinan</h3>
                <p class="text-xs text-surface-500 mt-2 leading-relaxed">Catatan pelanggaran & prestasi santri per tahun pelajaran dan periode.</p>
                <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-rose-600 group-hover:text-rose-700">
                    <span>Buka Laporan</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </div>
        </a>

        {{-- Laporan PSB --}}
        <a href="{{ route('admin.laporan.psb') }}" class="group bg-white rounded-3xl p-6 border border-surface-200 shadow-sm hover:shadow-xl hover:border-sky-400 hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-sky-500/5 rounded-full -translate-x-6 -translate-y-6 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative">
                <div class="w-12 h-12 bg-sky-100 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-sky-500 transition-colors">
                    <i data-lucide="clipboard-list" class="w-6 h-6 text-sky-600 group-hover:text-white transition-colors"></i>
                </div>
                <h3 class="text-lg font-extrabold text-surface-900 group-hover:text-sky-700 transition-colors">Laporan PSB</h3>
                <p class="text-xs text-surface-500 mt-2 leading-relaxed">Statistik penerimaan santri baru per gelombang, status, gender & lembaga tujuan.</p>
                <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-sky-600 group-hover:text-sky-700">
                    <span>Buka Laporan</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </div>
        </a>

    </div>
</div>
@endsection
