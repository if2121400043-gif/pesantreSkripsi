@extends('layouts.app')

@section('title', 'Pilih Kelas Penilaian')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Input Nilai Akademik</h1>
        <p class="text-sm text-surface-500 mt-1">Pilih kelas dan mata pelajaran yang Anda ampu untuk menginput nilai rapor.</p>
    </div>
</div>
@endsection

@section('content')
<x-card title="Daftar Kelas & Mata Pelajaran Diampu">
    @if($jadwals->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($jadwals as $jadwal)
                <div class="bg-white border border-surface-200 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-primary-300 transition-all flex flex-col h-full group relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-primary-50 rounded-bl-full -z-10 group-hover:scale-110 transition-transform"></div>
                    
                    <div class="flex-1">
                        <div class="w-12 h-12 bg-primary-100 text-primary-600 rounded-xl flex items-center justify-center mb-4">
                            <i data-lucide="book-open" class="w-6 h-6"></i>
                        </div>
                        <h3 class="text-lg font-bold text-surface-900 mb-1 group-hover:text-primary-700 transition-colors">{{ $jadwal->mataPelajaran->nama ?? '-' }}</h3>
                        <p class="text-sm font-medium text-surface-600 mb-4">{{ $jadwal->rombel->nama ?? '-' }}</p>
                        
                        <div class="flex items-center gap-2 text-xs text-surface-500 bg-surface-50 p-2 rounded-lg mb-6 border border-surface-100">
                            <i data-lucide="calendar" class="w-4 h-4 text-surface-400"></i>
                            T.P. {{ $jadwal->tahunPelajaran->nama ?? '-' }}
                        </div>
                    </div>
                    
                    <a href="{{ route('guru.penilaian.create', $jadwal->id) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-primary-50 text-primary-700 font-bold rounded-xl hover:bg-primary-600 hover:text-white transition-colors border border-primary-100 hover:border-primary-600">
                        <i data-lucide="edit-3" class="w-4 h-4"></i> Input Nilai
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="py-12 text-center border-2 border-dashed border-surface-200 rounded-2xl">
            <div class="w-16 h-16 bg-surface-50 text-surface-400 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="folder-x" class="w-8 h-8"></i>
            </div>
            <h3 class="text-lg font-bold text-surface-900 mb-1">Tidak Ada Data</h3>
            <p class="text-surface-500 text-sm max-w-sm mx-auto">Anda tidak ditugaskan untuk mengajar di kelas manapun pada tahun pelajaran aktif saat ini.</p>
        </div>
    @endif
</x-card>
@endsection
