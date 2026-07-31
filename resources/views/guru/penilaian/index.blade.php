@extends('layouts.guru')

@section('title', 'Input Nilai Akademik — PP Nurul Furqon')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-3xl border border-surface-200 shadow-sm">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-success-100 text-success-700 flex items-center justify-center font-bold shrink-0">
                <i data-lucide="award" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-surface-900 font-heading">Input Nilai Akademik Rapor</h1>
                <p class="text-xs text-surface-500 mt-0.5">Pilih kelas dan mata pelajaran yang Anda ampu untuk menginput nilai santri.</p>
            </div>
        </div>

        <a href="{{ route('guru.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-surface-100 text-surface-700 font-bold text-xs rounded-xl hover:bg-surface-200 transition-colors shrink-0">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Beranda
        </a>
    </div>

    {{-- Cards List --}}
    <div class="bg-white rounded-3xl p-6 border border-surface-200 shadow-sm">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-bold text-surface-900 text-base flex items-center gap-2">
                <i data-lucide="book-open" class="w-5 h-5 text-success-600"></i>
                Daftar Kelas & Mata Pelajaran Diampu
            </h3>
            <span class="text-xs text-surface-500 font-semibold bg-surface-100 px-3 py-1 rounded-full border border-surface-200">
                {{ $jadwals->count() }} Kelas Ditemukan
            </span>
        </div>

        @if($jadwals->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($jadwals as $jadwal)
                    <div class="bg-white border border-surface-200 rounded-3xl p-5 shadow-sm hover:shadow-lg hover:border-success-400 transition-all duration-300 flex flex-col justify-between group relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-success-50/60 rounded-bl-full -z-10 group-hover:scale-110 transition-transform"></div>
                        
                        <div>
                            <div class="w-12 h-12 bg-success-100 text-success-700 rounded-2xl flex items-center justify-center mb-4 group-hover:rotate-6 transition-transform">
                                <i data-lucide="book-open" class="w-6 h-6"></i>
                            </div>

                            <h3 class="text-base font-extrabold text-surface-900 mb-1 group-hover:text-success-700 transition-colors">
                                {{ $jadwal->mataPelajaran->nama ?? '-' }}
                            </h3>

                            <div class="flex items-center gap-2 mt-2 flex-wrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-surface-100 text-surface-800 text-xs font-bold border border-surface-200">
                                    <i data-lucide="users" class="w-3.5 h-3.5 text-surface-500"></i>
                                    Kelas {{ $jadwal->rombel->nama ?? '-' }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-surface-100">
                            <a href="{{ route('guru.penilaian.create', $jadwal->id) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-success-600 text-white font-bold text-xs rounded-xl hover:bg-success-700 transition-all shadow-sm shadow-success-600/20">
                                <i data-lucide="edit-3" class="w-4 h-4"></i> Input Nilai Santri
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-12 text-center border-2 border-dashed border-surface-200 rounded-3xl bg-surface-50">
                <div class="w-14 h-14 bg-surface-100 text-surface-400 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="folder-x" class="w-7 h-7"></i>
                </div>
                <h3 class="text-base font-bold text-surface-900 mb-1">Tidak Ada Data Kelas</h3>
                <p class="text-xs text-surface-500 max-w-xs mx-auto">Anda belum ditugaskan mengajar kelas manapun pada tahun ajaran aktif.</p>
            </div>
        @endif
    </div>

</div>
@endsection
