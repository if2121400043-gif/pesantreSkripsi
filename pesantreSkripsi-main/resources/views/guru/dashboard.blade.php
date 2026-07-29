@extends('layouts.app')

@section('title', 'Dashboard Guru')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Ahlan wa Sahlan, Ustadz/ah {{ auth()->user()->name }}</h1>
        <p class="text-sm text-surface-500 mt-1">Portal manajemen akademik dan kepesantrenan.</p>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    {{-- Card Jadwal Hari Ini --}}
    <div class="bg-white rounded-2xl p-6 border border-surface-100 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-primary-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-primary-100 text-primary-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="calendar-clock" class="w-6 h-6"></i>
                </div>
                <span class="text-xs font-bold text-primary-600 bg-primary-50 px-3 py-1 rounded-full border border-primary-100 uppercase tracking-wider">{{ $hariIni }}</span>
            </div>
            <h3 class="text-sm font-medium text-surface-500 mb-1">Jadwal Mengajar Hari Ini</h3>
            <p class="text-2xl font-bold text-surface-900">{{ $jadwalHariIni->count() }} Kelas</p>
        </div>
    </div>

    {{-- Card Total Kelas --}}
    <div class="bg-white rounded-2xl p-6 border border-surface-100 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-info-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-info-100 text-info-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
            </div>
            <h3 class="text-sm font-medium text-surface-500 mb-1">Total Kelas Diampu</h3>
            <p class="text-2xl font-bold text-surface-900">{{ $totalKelasDiajar }} Rombel</p>
        </div>
    </div>

    {{-- Card Wali Kelas --}}
    <div class="bg-white rounded-2xl p-6 border border-surface-100 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-success-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-success-100 text-success-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="award" class="w-6 h-6"></i>
                </div>
            </div>
            <h3 class="text-sm font-medium text-surface-500 mb-1">Amanah Wali Kelas</h3>
            @if($waliKelas->count() > 0)
                <p class="text-2xl font-bold text-surface-900">{{ $waliKelas->count() }} Kelas</p>
            @else
                <p class="text-lg font-medium text-surface-500 mt-1">Tidak Ada</p>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        {{-- Jadwal Hari Ini --}}
        <x-card title="Jadwal Mengajar Hari Ini ({{ $hariIni }})">
            @if($jadwalHariIni->count() > 0)
                <div class="space-y-4">
                    @foreach($jadwalHariIni as $jadwal)
                        <div class="flex items-start gap-4 p-4 rounded-xl border border-surface-200 bg-surface-50 hover:border-primary-300 hover:bg-primary-50/30 transition-all group">
                            <div class="w-14 h-14 rounded-xl bg-white border border-surface-200 flex flex-col items-center justify-center text-center flex-shrink-0 group-hover:border-primary-200 group-hover:shadow-sm">
                                <span class="text-xs text-surface-400 font-medium">Jam Ke</span>
                                <span class="text-lg font-bold text-surface-900">{{ $jadwal->jam_mulai }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <h4 class="font-bold text-surface-900 text-lg truncate group-hover:text-primary-700 transition-colors">{{ $jadwal->mataPelajaran->nama ?? '-' }}</h4>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-white border border-surface-200 text-xs font-bold text-surface-700 shadow-sm">
                                        <i data-lucide="users" class="w-3.5 h-3.5 text-surface-400"></i> {{ $jadwal->rombel->nama ?? '-' }}
                                    </span>
                                </div>
                                <div class="mt-2 flex items-center gap-3">
                                    <a href="{{ route('guru.presensi.create', $jadwal->id) }}" class="text-sm font-semibold text-primary-600 hover:text-primary-700 flex items-center gap-1.5">
                                        <i data-lucide="check-square" class="w-4 h-4"></i> Isi Presensi
                                    </a>
                                    <span class="w-1 h-1 rounded-full bg-surface-300"></span>
                                    <a href="{{ route('guru.penilaian.create', $jadwal->id) }}" class="text-sm font-semibold text-info-600 hover:text-info-700 flex items-center gap-1.5">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i> Input Nilai
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-12 text-center border-2 border-dashed border-surface-200 rounded-xl">
                    <div class="w-16 h-16 bg-surface-100 text-surface-400 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="coffee" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-lg font-bold text-surface-900 mb-1">Alhamdulillah</h3>
                    <p class="text-surface-500 text-sm max-w-xs mx-auto">Anda tidak memiliki jadwal mengajar di kelas pada hari {{ $hariIni }}.</p>
                </div>
            @endif
        </x-card>
    </div>
    
    <div class="lg:col-span-1 space-y-6">
        @if($waliKelas->count() > 0)
            <x-card title="Kelas Perwalian">
                <div class="space-y-3">
                    @foreach($waliKelas as $rombel)
                        <div class="p-4 rounded-xl border border-surface-200 bg-white shadow-sm flex justify-between items-center">
                            <div>
                                <h4 class="font-bold text-surface-900">{{ $rombel->nama }}</h4>
                                <p class="text-xs text-surface-500">{{ $rombel->lembaga->nama ?? '-' }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-success-50 text-success-600 flex items-center justify-center">
                                <i data-lucide="users" class="w-5 h-5"></i>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>
        @endif
        
        <x-card title="Pintasan">
            <div class="space-y-2">
                <a href="{{ route('guru.kedisiplinan.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-surface-50 transition-colors group border border-transparent hover:border-surface-200">
                    <div class="w-10 h-10 rounded-lg bg-danger-50 text-danger-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <i data-lucide="shield-alert" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="font-bold text-surface-900 text-sm">Lapor Pelanggaran</p>
                        <p class="text-xs text-surface-500">Catat kasus indisipliner santri</p>
                    </div>
                </a>
                <a href="{{ route('guru.kedisiplinan.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-surface-50 transition-colors group border border-transparent hover:border-surface-200">
                    <div class="w-10 h-10 rounded-lg bg-warning-50 text-warning-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <i data-lucide="trophy" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="font-bold text-surface-900 text-sm">Catat Prestasi</p>
                        <p class="text-xs text-surface-500">Berikan apresiasi pada santri</p>
                    </div>
                </a>
            </div>
        </x-card>
    </div>
</div>
@endsection
