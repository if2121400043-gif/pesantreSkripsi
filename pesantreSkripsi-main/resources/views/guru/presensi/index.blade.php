@extends('layouts.app')

@section('title', 'Input Presensi Kelas')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Input Presensi Kelas</h1>
        <p class="text-sm text-surface-500 mt-1">Pilih jadwal mengajar untuk mengisi daftar hadir santri.</p>
    </div>
</div>
@endsection

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

<div class="mb-6 flex gap-2 overflow-x-auto pb-2">
    @foreach($daftarHari as $h)
        <a href="{{ route('guru.presensi.index', ['hari' => $h]) }}" 
           class="px-4 py-2 rounded-xl text-sm font-bold whitespace-nowrap transition-colors border {{ $hari === $h ? 'bg-primary-600 text-white border-primary-600 shadow-md shadow-primary-500/20' : 'bg-white text-surface-600 border-surface-200 hover:bg-surface-50 hover:text-surface-900' }}">
            {{ $labelHari[$h] ?? $h }}
        </a>
    @endforeach
</div>

<x-card title="Jadwal Hari {{ $hari }}">
    @if($jadwals->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($jadwals as $jadwal)
                <div class="border border-surface-200 rounded-xl p-5 bg-white hover:border-primary-300 hover:shadow-md transition-all group relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-surface-50 rounded-bl-full -z-10 group-hover:bg-primary-50 transition-colors"></div>
                    
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-surface-100 flex flex-col items-center justify-center font-bold text-surface-700 group-hover:bg-primary-100 group-hover:text-primary-700 transition-colors">
                            <span class="text-[0.65rem] uppercase tracking-wider text-surface-500 group-hover:text-primary-500">Jam Ke</span>
                            <span class="text-lg leading-none">{{ $jadwal->jam_mulai }}</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-surface-900 leading-tight group-hover:text-primary-700">{{ $jadwal->mataPelajaran->nama ?? '-' }}</h4>
                            <p class="text-sm text-surface-500">{{ $jadwal->rombel->nama ?? '-' }}</p>
                        </div>
                    </div>
                    
                    <a href="{{ route('guru.presensi.create', $jadwal->id) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-surface-50 hover:bg-primary-600 hover:text-white text-surface-700 font-semibold text-sm transition-colors">
                        <i data-lucide="check-square" class="w-4 h-4"></i> Isi Presensi
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="py-12 text-center">
            <div class="w-16 h-16 bg-surface-50 text-surface-400 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="calendar-x" class="w-8 h-8"></i>
            </div>
            <h3 class="text-lg font-bold text-surface-900 mb-1">Tidak Ada Jadwal</h3>
            <p class="text-surface-500 text-sm">Anda tidak memiliki jadwal mengajar pada hari {{ $labelHari[$hari] ?? $hari }}.</p>
        </div>
    @endif
</x-card>
@endsection
