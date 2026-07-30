@extends('layouts.app')

@section('title', 'Profil Pegawai: ' . $pegawai->orang->nama_lengkap)

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm text-surface-500 mb-2">
            <a href="{{ route('admin.pegawai.index') }}" class="hover:text-primary-600 transition-colors">Data Pegawai</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-surface-900 font-medium">Profil Kepegawaian</span>
        </div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">{{ $pegawai->orang->nama_lengkap }}</h1>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.pegawai.index') }}" class="btn-secondary flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span class="hidden sm:inline">Kembali</span>
        </a>
        <a href="{{ route('admin.pegawai.edit', $pegawai) }}" class="btn-primary flex items-center gap-2">
            <i data-lucide="edit" class="w-4 h-4"></i>
            <span class="hidden sm:inline">Edit SDM</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    {{-- Kolom Kiri --}}
    <div class="lg:col-span-1 space-y-6">
        <x-card :padding="false">
            <div class="p-6 text-center border-b border-surface-100">
                <div class="w-24 h-24 rounded-full bg-primary-100 text-primary-700 mx-auto flex items-center justify-center text-3xl font-bold mb-4">
                    {{ substr($pegawai->orang->nama_lengkap, 0, 1) }}
                </div>
                <h2 class="text-lg font-bold text-surface-900">{{ $pegawai->orang->nama_lengkap }}</h2>
                <p class="text-surface-500 text-sm mt-1">NIUP: <span class="font-mono text-primary-600 font-medium">{{ $pegawai->orang->niup }}</span></p>
                
                <div class="mt-4">
                    <x-badge variant="info" class="px-3 py-1 text-sm mb-2 block w-max mx-auto">
                        {{ str_replace('_', ' ', $pegawai->jenis_pegawai) }}
                    </x-badge>
                    @if($pegawai->is_active)
                        <span class="text-success-600 text-sm font-semibold flex items-center justify-center gap-1">
                            <i data-lucide="check-circle" class="w-4 h-4"></i> Aktif Bekerja
                        </span>
                    @else
                        <span class="text-danger-600 text-sm font-semibold flex items-center justify-center gap-1">
                            <i data-lucide="x-circle" class="w-4 h-4"></i> Tidak Aktif
                        </span>
                    @endif
                </div>
            </div>
            
            <div class="p-6 space-y-4">
                <div>
                    <p class="text-xs text-surface-500 font-medium mb-1">Status Kepegawaian</p>
                    <p class="font-medium text-surface-900">{{ $pegawai->status_kepegawaian }}</p>
                </div>
                <div>
                    <p class="text-xs text-surface-500 font-medium mb-1">NUPTK (Nasional)</p>
                    <p class="font-medium text-surface-900 font-mono">{{ $pegawai->nuptk ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-surface-500 font-medium mb-1">NIP (Lokal/Pesantren)</p>
                    <p class="font-medium text-surface-900 font-mono">{{ $pegawai->nip ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-surface-500 font-medium mb-1">Mulai Bekerja</p>
                    <p class="font-medium text-surface-900">{{ $pegawai->tanggal_masuk ? $pegawai->tanggal_masuk->format('d M Y') : '-' }}</p>
                </div>
                
                <div class="pt-4 border-t border-surface-100">
                    <a href="{{ route('admin.orang.show', $pegawai->orang_id) }}" class="text-sm font-semibold text-primary-600 hover:text-primary-700 flex items-center justify-between">
                        <span>Lihat Biodata Lengkap Induk</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Kolom Kanan --}}
    <div class="lg:col-span-2 space-y-6">
        
        <x-card title="Data Spesifik Kepegawaian">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-surface-500 font-medium mb-1">Jabatan Struktural</p>
                    <p class="font-medium text-surface-900">{{ $pegawai->jabatan ?? 'Tidak menjabat struktural khusus' }}</p>
                </div>
                <div>
                    <p class="text-xs text-surface-500 font-medium mb-1">Pendidikan Terakhir</p>
                    <p class="font-medium text-surface-900">{{ $pegawai->pendidikan_terakhir ?? 'Belum didata' }}</p>
                </div>
                <div>
                    <p class="text-xs text-surface-500 font-medium mb-1">Jurusan / Program Studi</p>
                    <p class="font-medium text-surface-900">{{ $pegawai->jurusan_pendidikan ?? '-' }}</p>
                </div>
            </div>
        </x-card>

        {{-- Riwayat Jabatan (Timeline) --}}
        <x-card title="Riwayat Jabatan & Kepengurusan">
            <div class="space-y-4">
                @if($pegawai->riwayatJabatan->count() > 0)
                    <div class="relative border-l-2 border-surface-200 ml-3 py-2 space-y-6">
                        @foreach($pegawai->riwayatJabatan as $rj)
                            <div class="relative pl-6">
                                <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-white border-2 border-primary-500"></div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-bold text-surface-900">{{ $rj->jabatan }}</h4>
                                        <x-badge variant="info" class="text-[0.65rem] px-1.5 py-0.5">{{ $rj->jenis_pegawai }}</x-badge>
                                        @if(is_null($rj->tanggal_selesai))
                                            <x-badge variant="success" class="text-[0.65rem] px-1.5 py-0.5">Aktif Saat Ini</x-badge>
                                        @endif
                                    </div>
                                    <p class="text-xs text-surface-500 mt-1">
                                        Periode: {{ $rj->tanggal_mulai->format('d M Y') }} - 
                                        {{ $rj->tanggal_selesai ? $rj->tanggal_selesai->format('d M Y') : 'Sekarang' }}
                                    </p>
                                    @if($rj->keterangan)
                                        <p class="text-sm text-surface-600 mt-2 bg-surface-50 p-2.5 rounded-lg border border-surface-100 italic">
                                            {{ $rj->keterangan }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 bg-surface-50 rounded-lg border border-surface-100 border-dashed">
                        <i data-lucide="award" class="w-8 h-8 text-surface-300 mx-auto mb-2"></i>
                        <p class="text-sm text-surface-500">Belum ada catatan riwayat perpindahan jabatan. Jabatan saat ini: <strong>{{ $pegawai->jabatan ?? '-' }}</strong></p>
                    </div>
                @endif
            </div>
        </x-card>

        {{-- Catatan Tambahan --}}
        <x-card title="Catatan SDM">
            @if($pegawai->catatan)
                <p class="text-surface-700 whitespace-pre-wrap">{{ $pegawai->catatan }}</p>
            @else
                <p class="text-surface-400 italic">Tidak ada catatan kepegawaian.</p>
            @endif
        </x-card>
        
    </div>
</div>
@endsection
