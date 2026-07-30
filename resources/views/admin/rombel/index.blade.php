@extends('layouts.app')

@section('title', 'Rombongan Belajar (Kelas)')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Rombongan Belajar (Kelas)</h1>
        <p class="text-sm text-surface-500 mt-1">Kelola kelas, kapasitas, dan wali kelas per lembaga.</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.rombel.create') }}" class="btn-primary flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Kelas</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<x-card :padding="false">
    {{-- Filter Bar --}}
    <div class="p-4 border-b border-surface-100 bg-surface-50">
        <form action="{{ route('admin.rombel.index') }}" method="GET" class="flex flex-wrap gap-4">
            <div class="w-full sm:w-auto flex-1 sm:flex-none">
                <select name="lembaga_id" class="w-full px-4 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" onchange="this.form.submit()">
                    <option value="">Semua Lembaga</option>
                    @foreach($lembagas as $lembaga)
                        <option value="{{ $lembaga->id }}" {{ request('lembaga_id') == $lembaga->id ? 'selected' : '' }}>
                            {{ $lembaga->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-auto flex-1 sm:flex-none">
                <select name="tahun_pelajaran_id" class="w-full px-4 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" onchange="this.form.submit()">
                    <option value="">Semua Tahun Ajaran</option>
                    @foreach($tahuns as $tahun)
                        <option value="{{ $tahun->id }}" {{ request('tahun_pelajaran_id') == $tahun->id ? 'selected' : ($loop->first && !request()->has('tahun_pelajaran_id') ? 'selected' : '') }}>
                            {{ $tahun->nama }} {{ $tahun->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    {{-- Data Grid (Cards instead of Table for better visualization of classes) --}}
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse($rombels as $rombel)
                <div class="border border-surface-200 rounded-xl overflow-hidden hover:border-primary-300 transition-colors flex flex-col h-full bg-white">
                    <div class="p-4 border-b border-surface-100 flex justify-between items-start">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[0.65rem] font-bold text-primary-600 uppercase tracking-wider">{{ $rombel->lembaga->singkatan ?? $rombel->lembaga->nama }}</span>
                                @if($rombel->gender_target === 'PUTRA')
                                    <x-badge variant="info" size="sm">Putra Only</x-badge>
                                @elseif($rombel->gender_target === 'PUTRI')
                                    <x-badge variant="warning" size="sm">Putri Only</x-badge>
                                @else
                                    <x-badge variant="neutral" size="sm">Campur</x-badge>
                                @endif
                            </div>
                            <h3 class="font-bold text-lg text-surface-900 leading-tight">Kelas {{ $rombel->tingkat ? $rombel->tingkat . ' - ' : '' }}{{ $rombel->nama }}</h3>
                        </div>
                    </div>
                    <div class="p-4 flex-1 flex flex-col justify-center">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center font-bold text-sm shrink-0">
                                @if($rombel->waliKelas)
                                    {{ substr($rombel->waliKelas->orang->nama_lengkap, 0, 1) }}
                                @else
                                    ?
                                @endif
                            </div>
                            <div>
                                <p class="text-xs text-surface-500 font-medium">Wali Kelas</p>
                                <p class="text-sm font-semibold text-surface-900 truncate" title="{{ $rombel->waliKelas->orang->nama_lengkap ?? 'Belum Ditentukan' }}">
                                    {{ $rombel->waliKelas->orang->nama_lengkap ?? 'Belum Ditentukan' }}
                                </p>
                            </div>
                        </div>

                        <div class="bg-surface-50 rounded-lg p-3 border border-surface-100 mt-auto">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs font-medium text-surface-600">Kapasitas: {{ $rombel->riwayatPeserta->count() }} / {{ $rombel->kapasitas }}</span>
                                <span class="text-xs font-bold {{ $rombel->riwayatPeserta->count() >= $rombel->kapasitas ? 'text-danger-600' : 'text-success-600' }}">
                                    {{ round(($rombel->riwayatPeserta->count() / max($rombel->kapasitas, 1)) * 100) }}%
                                </span>
                            </div>
                            <div class="w-full bg-surface-200 rounded-full h-1.5">
                                @php
                                    $percentage = min(($rombel->riwayatPeserta->count() / max($rombel->kapasitas, 1)) * 100, 100);
                                    $color = $percentage >= 100 ? 'bg-danger-500' : ($percentage >= 80 ? 'bg-warning-500' : 'bg-success-500');
                                @endphp
                                <div class="{{ $color }} h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="border-t border-surface-100 p-2 flex bg-surface-50 gap-1">
                        <a href="{{ route('admin.rombel.show', $rombel) }}" class="flex-1 text-center py-2 text-sm font-semibold text-primary-600 hover:bg-primary-50 rounded-lg transition-colors">
                            Kelola Siswa
                        </a>
                        <div class="w-px bg-surface-200 my-2"></div>
                        <a href="{{ route('admin.rombel.edit', $rombel) }}" class="flex-1 text-center py-2 text-sm font-semibold text-surface-600 hover:bg-surface-100 rounded-lg transition-colors">
                            Edit
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center">
                    <div class="w-16 h-16 bg-surface-100 text-surface-400 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="book-open" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-lg font-bold text-surface-900 mb-1">Tidak Ada Rombongan Belajar</h3>
                    <p class="text-surface-500 max-w-md mx-auto mb-6">Belum ada kelas yang didaftarkan untuk kriteria yang dipilih.</p>
                </div>
            @endforelse
        </div>
    </div>
    
    @if($rombels->hasPages())
    <div class="p-4 border-t border-surface-100">
        {{ $rombels->links() }}
    </div>
    @endif
</x-card>
@endsection
