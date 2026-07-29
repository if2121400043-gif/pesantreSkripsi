@extends('layouts.app')

@section('title', 'Data Pendaftar (Calon Santri)')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Data Calon Santri</h1>
        <p class="text-sm text-surface-500 mt-1">Daftar calon santri baru yang mendaftar melalui portal PSB.</p>
    </div>
    <a href="{{ route('admin.psb.calon-santri.create') }}" class="btn-primary flex items-center gap-2">
        <i data-lucide="user-plus" class="w-4 h-4"></i>
        <span>Pendaftaran Offline</span>
    </a>
</div>
@endsection

@section('content')
<x-card :padding="false">
    {{-- Search & Filter Bar --}}
    <div class="p-4 border-b border-surface-100 bg-surface-50">
        <form action="{{ route('admin.psb.calon-santri.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama Pendaftar atau No Registrasi..." 
                       class="w-full pl-10 pr-4 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors">
            </div>
            <div class="sm:w-48">
                <select name="gelombang_id" class="w-full px-4 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" onchange="this.form.submit()">
                    <option value="">Semua Gelombang</option>
                    @foreach($gelombangs as $g)
                        <option value="{{ $g->id }}" {{ request('gelombang_id') == $g->id ? 'selected' : '' }}>
                            {{ $g->nama }} (T.A {{ $g->tahunPelajaran->nama }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="sm:w-40">
                @php
                    $statusBg = 'bg-white';
                    $statusText = 'text-surface-900';
                    if (request('status') == 'BARU_MASUK') { $statusBg = 'bg-info-100'; $statusText = 'text-info-900'; }
                    elseif (request('status') == 'HADIR_TES') { $statusBg = 'bg-warning-100'; $statusText = 'text-warning-900'; }
                    elseif (request('status') == 'DITERIMA') { $statusBg = 'bg-success-100'; $statusText = 'text-success-900'; }
                    elseif (request('status') == 'TIDAK_LULUS') { $statusBg = 'bg-danger-100'; $statusText = 'text-danger-900'; }
                    elseif (request('status') == 'DIBATALKAN') { $statusBg = 'bg-surface-200'; $statusText = 'text-surface-600'; }
                @endphp
                <select name="status" class="w-full px-4 py-2 rounded-lg border border-surface-300 {{ $statusBg }} {{ $statusText }} font-medium text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors" onchange="this.form.submit()">
                    <option value="" class="bg-white text-surface-900">Semua Status</option>
                    <option value="BARU_MASUK" class="bg-info-100 text-info-900 font-medium" {{ request('status') == 'BARU_MASUK' ? 'selected' : '' }}>Baru Masuk</option>
                    <option value="HADIR_TES" class="bg-warning-100 text-warning-900 font-medium" {{ request('status') == 'HADIR_TES' ? 'selected' : '' }}>Hadir Tes / Bayar</option>
                    <option value="DITERIMA" class="bg-success-100 text-success-900 font-medium" {{ request('status') == 'DITERIMA' ? 'selected' : '' }}>Diterima</option>
                    <option value="TIDAK_LULUS" class="bg-danger-100 text-danger-900 font-medium" {{ request('status') == 'TIDAK_LULUS' ? 'selected' : '' }}>Tidak Lulus</option>
                    <option value="DIBATALKAN" class="bg-surface-200 text-surface-600 font-medium" {{ request('status') == 'DIBATALKAN' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <button type="submit" class="btn-secondary px-4 py-2 hidden sm:block">Cari</button>
            @if(request()->anyFilled(['search', 'gelombang_id', 'status']))
                <a href="{{ route('admin.psb.calon-santri.index') }}" class="btn-secondary px-4 py-2 text-danger-600 border-danger-200 hover:bg-danger-50 flex items-center justify-center">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </a>
            @endif
        </form>
    </div>

    {{-- Data Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-surface-50/50 text-surface-600 border-b border-surface-100">
                <tr>
                    <th class="px-6 py-4 font-semibold">Tgl Daftar & No. Reg</th>
                    <th class="px-6 py-4 font-semibold">Nama Pendaftar</th>
                    <th class="px-6 py-4 font-semibold">Gelombang</th>
                    <th class="px-6 py-4 font-semibold text-center">Tujuan Lembaga</th>
                    <th class="px-6 py-4 font-semibold text-center">Status</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100 text-surface-700">
                @forelse($calonSantris as $cs)
                <tr class="hover:bg-surface-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="text-xs text-surface-500">{{ $cs->created_at->format('d/m/Y H:i') }}</div>
                        <div class="font-mono font-bold text-primary-700 mt-0.5">{{ $cs->no_pendaftaran }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-surface-900">{{ $cs->nama_lengkap }}</div>
                        <div class="text-xs text-surface-500 flex items-center gap-1 mt-0.5">
                            <span class="{{ $cs->jenis_kelamin == 'L' ? 'text-blue-600' : 'text-pink-600' }}">{{ $cs->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                            @if($cs->asal_sekolah) • Dari: {{ $cs->asal_sekolah }} @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-surface-900">{{ $cs->gelombang->nama }}</div>
                        <div class="text-xs text-surface-500 mt-0.5">T.A {{ $cs->gelombang->tahunPelajaran->nama }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <x-badge variant="surface">{{ $cs->lembagaTujuan->singkatan ?? $cs->lembagaTujuan->nama ?? 'Umum/Pesantren' }}</x-badge>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($cs->status === 'DITERIMA')
                            <x-badge type="success" dot>Diterima</x-badge>
                        @elseif($cs->status === 'TIDAK_LULUS')
                            <x-badge type="danger" dot>Tidak Lulus</x-badge>
                        @elseif($cs->status === 'HADIR_TES')
                            <x-badge type="warning" dot>Hadir Tes</x-badge>
                        @elseif($cs->status === 'DIBATALKAN')
                            <x-badge type="surface" dot>Dibatalkan</x-badge>
                        @else
                            <x-badge type="info" dot>Baru Masuk</x-badge>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.psb.calon-santri.show', $cs) }}" class="inline-flex btn-primary py-1.5 px-3 text-xs gap-1">
                            <i data-lucide="clipboard-check" class="w-3 h-3"></i> Proses
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-surface-500">
                        <div class="flex flex-col items-center justify-center">
                            <i data-lucide="user-x" class="w-12 h-12 text-surface-300 mb-3"></i>
                            <p class="font-medium text-surface-900 mb-1">Belum Ada Pendaftar</p>
                            <p class="text-sm">Belum ada calon santri yang sesuai kriteria pencarian Anda.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($calonSantris->hasPages())
    <div class="p-4 border-t border-surface-100">
        {{ $calonSantris->links() }}
    </div>
    @endif
</x-card>
@endsection
