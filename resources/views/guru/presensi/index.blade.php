@extends('layouts.guru')

@section('title', 'Input Presensi Kelas — PP Nurul Furqon')

@section('content')
@php
$labelHari = [
    'SENIN'  => 'Senin',
    'SELASA' => 'Selasa',
    'RABU'   => 'Rabu',
    'KAMIS'  => 'Kamis',
    'JUMAT'  => 'Jumat',
    'SABTU'  => 'Sabtu',
    'AHAD'   => 'Ahad',
];
@endphp

<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-3xl border border-surface-200 shadow-sm">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-primary-100 text-primary-700 flex items-center justify-center font-bold shrink-0">
                <i data-lucide="clipboard-check" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-surface-900 font-heading">Input Presensi Santri</h1>
                <p class="text-xs text-surface-500 mt-0.5">Pilih jadwal mengajar untuk mengisi daftar hadir santri harian.</p>
            </div>
        </div>

        <a href="{{ route('guru.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-surface-100 text-surface-700 font-bold text-xs rounded-xl hover:bg-surface-200 transition-colors shrink-0">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Beranda
        </a>
    </div>

    {{-- Filter Day Pill Tabs --}}
    <div class="flex gap-2 overflow-x-auto pb-1 border-b border-surface-200">
        @foreach($daftarHari as $h)
            @php
                $isActive = ($hari === $h);
            @endphp
            <a href="{{ route('guru.presensi.index', ['hari' => $h]) }}" 
               class="px-4 py-2 rounded-2xl text-xs font-bold whitespace-nowrap transition-all border flex items-center gap-1.5 {{ $isActive ? 'bg-primary-800 text-white border-primary-800 shadow-md shadow-primary-800/20 scale-105' : 'bg-white text-surface-600 border-surface-200 hover:bg-surface-50 hover:text-surface-900' }}"
               style="{{ $isActive ? 'color: #ffffff !important; background-color: #064e3b !important;' : '' }}">
                <i data-lucide="calendar" class="w-3.5 h-3.5 {{ $isActive ? 'text-warning-300' : 'text-surface-400' }}"></i>
                <span style="{{ $isActive ? 'color: #ffffff !important;' : '' }}">Hari {{ $labelHari[$h] ?? $h }}</span>
            </a>
        @endforeach
    </div>

    {{-- Schedule Cards List --}}
    <div class="bg-white rounded-3xl p-6 border border-surface-200 shadow-sm">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-bold text-surface-900 text-base flex items-center gap-2">
                <i data-lucide="clock" class="w-5 h-5 text-primary-600"></i>
                Jadwal Hari {{ $labelHari[$hari] ?? $hari }}
            </h3>
            <span class="text-xs text-surface-500 font-semibold bg-surface-100 px-3 py-1 rounded-full border border-surface-200">
                {{ $jadwals->count() }} Kelas Ditemukan
            </span>
        </div>

        @if($jadwals->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($jadwals as $jadwal)
                    @php
                        $jamFormatted = substr($jadwal->jam_mulai, 0, 5);
                        $jamSelesaiFormatted = substr($jadwal->jam_selesai, 0, 5);
                    @endphp
                    <div class="bg-white border border-surface-200 rounded-3xl p-5 shadow-sm hover:shadow-lg hover:border-primary-400 transition-all duration-300 flex flex-col justify-between group relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-primary-50/60 rounded-bl-full -z-10 group-hover:scale-110 transition-transform"></div>
                        
                        <div>
                            {{-- Header: Time Badge --}}
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-primary-50 text-primary-800 font-bold text-xs border border-primary-100">
                                    <i data-lucide="clock" class="w-3.5 h-3.5 text-primary-600"></i>
                                    <span>Pukul {{ $jamFormatted }} @if($jamSelesaiFormatted && $jamSelesaiFormatted !== '00:00') - {{ $jamSelesaiFormatted }} @endif WIB</span>
                                </div>
                            </div>

                            {{-- Subject Name --}}
                            <h4 class="text-base font-extrabold text-surface-900 group-hover:text-primary-700 transition-colors leading-snug">
                                {{ $jadwal->mataPelajaran->nama ?? 'Mata Pelajaran Tidak Diatur' }}
                            </h4>

                            {{-- Rombel Badge & Gender --}}
                            <div class="flex items-center gap-2 mt-2.5 flex-wrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-surface-100 text-surface-800 text-xs font-bold border border-surface-200">
                                    <i data-lucide="users" class="w-3.5 h-3.5 text-surface-500"></i>
                                    {{ str_starts_with(strtolower($jadwal->rombel->nama ?? ''), 'kelas') ? $jadwal->rombel->nama : 'Kelas ' . ($jadwal->rombel->nama ?? '-') }}
                                </span>
                                @if(isset($jadwal->rombel->gender_target))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[0.65rem] font-extrabold uppercase {{ $jadwal->rombel->gender_target == 'PUTRA' ? 'bg-blue-100 text-blue-700' : ($jadwal->rombel->gender_target == 'PUTRI' ? 'bg-pink-100 text-pink-700' : 'bg-surface-200 text-surface-700') }}">
                                        {{ $jadwal->rombel->gender_target }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Action Button --}}
                        <div class="mt-6 pt-4 border-t border-surface-100">
                            <a href="{{ route('guru.presensi.create', $jadwal->id) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-primary-700 hover:bg-primary-800 text-white font-bold text-xs shadow-md transition-all" style="color: #ffffff !important; background-color: #065f46 !important;">
                                <i data-lucide="check-square" class="w-4 h-4 text-white" style="color: #ffffff !important;"></i>
                                <span style="color: #ffffff !important;">Isi Presensi Santri</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-12 text-center border-2 border-dashed border-surface-200 rounded-3xl bg-surface-50">
                <div class="w-14 h-14 bg-surface-100 text-surface-400 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="calendar-x" class="w-7 h-7"></i>
                </div>
                <h3 class="text-base font-bold text-surface-900 mb-1">Tidak Ada Jadwal Mengajar</h3>
                <p class="text-xs text-surface-500 max-w-xs mx-auto">Anda tidak memiliki jadwal mengajar pada hari {{ $labelHari[$hari] ?? $hari }}.</p>
            </div>
        @endif
    </div>

</div>
@endsection
