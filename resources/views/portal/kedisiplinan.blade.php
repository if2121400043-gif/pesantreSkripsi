@extends('layouts.portal')

@section('title', 'Kedisiplinan & Prestasi')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Kedisiplinan & Prestasi</h1>
        <p class="text-sm text-surface-500 mt-1">Catatan pembinaan dan pencapaian prestasi ananda.</p>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Kolom Kedisiplinan / Pelanggaran --}}
    <x-card title="Catatan Kedisiplinan" class="h-full">
        @if($pelanggarans->count() > 0)
            <div class="space-y-4">
                @foreach($pelanggarans as $pelanggaran)
                    <div class="p-4 rounded-xl border border-danger-200 bg-danger-50/50 flex gap-4 relative overflow-hidden group">
                        <div class="w-10 h-10 rounded-full bg-danger-100 text-danger-600 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="shield-alert" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start mb-1">
                                <h4 class="font-bold text-surface-900 text-sm truncate">{{ $pelanggaran->pesertaDidik->orang->nama ?? '-' }}</h4>
                                <span class="text-xs font-medium text-surface-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($pelanggaran->tanggal)->format('d M Y') }}</span>
                            </div>
                            <p class="text-sm text-surface-800 font-medium mb-1">{{ $pelanggaran->jenisPelanggaran->nama ?? '-' }}</p>
                            
                            @if($pelanggaran->tindakan)
                                <div class="mt-2 text-xs text-surface-600 bg-white/60 p-2 rounded border border-danger-100">
                                    <span class="font-semibold text-danger-700">Tindakan/Sanksi:</span> {{ $pelanggaran->tindakan }}
                                </div>
                            @endif
                        </div>
                        <div class="absolute top-4 right-4 text-2xl font-extrabold text-danger-500/20 group-hover:text-danger-500/40 transition-colors pointer-events-none">
                            -{{ $pelanggaran->poin }}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-12 text-center">
                <div class="w-16 h-16 bg-success-50 text-success-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="check" class="w-8 h-8"></i>
                </div>
                <h3 class="text-lg font-bold text-surface-900 mb-1">Alhamdulillah</h3>
                <p class="text-surface-500 text-sm max-w-xs mx-auto">Tidak ada catatan pelanggaran disiplin. Mari terus bimbing ananda untuk istiqomah.</p>
            </div>
        @endif
    </x-card>

    {{-- Kolom Prestasi --}}
    <x-card title="Catatan Prestasi" class="h-full">
        @if($prestasis->count() > 0)
            <div class="space-y-4">
                @foreach($prestasis as $prestasi)
                    <div class="p-4 rounded-xl border border-warning-200 bg-warning-50/50 flex gap-4 relative overflow-hidden group">
                        <div class="w-10 h-10 rounded-full bg-warning-100 text-warning-600 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="trophy" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start mb-1">
                                <h4 class="font-bold text-surface-900 text-sm truncate">{{ $prestasi->pesertaDidik->orang->nama ?? '-' }}</h4>
                                <span class="text-xs font-medium text-surface-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($prestasi->tanggal)->format('d M Y') }}</span>
                            </div>
                            <p class="text-sm text-surface-800 font-bold mb-1">{{ $prestasi->nama_prestasi }}</p>
                            <p class="text-xs text-surface-600 mb-2">
                                <span class="inline-block px-2 py-0.5 bg-white border border-warning-200 rounded text-warning-700 font-medium mr-1">{{ $prestasi->tingkat }}</span>
                                {{ $prestasi->penyelenggara }}
                            </p>
                            
                            @if($prestasi->keterangan)
                                <p class="text-xs text-surface-500 italic">{{ $prestasi->keterangan }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-12 text-center">
                <div class="w-16 h-16 bg-surface-50 text-surface-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="award" class="w-8 h-8"></i>
                </div>
                <h3 class="text-lg font-bold text-surface-900 mb-1">Belum Ada Catatan</h3>
                <p class="text-surface-500 text-sm max-w-xs mx-auto">Catatan prestasi akademik maupun non-akademik akan tampil di sini.</p>
            </div>
        @endif
    </x-card>

</div>
@endsection
