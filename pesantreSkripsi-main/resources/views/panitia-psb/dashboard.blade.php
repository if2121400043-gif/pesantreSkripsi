@extends('layouts.app')

@section('title', 'Dashboard Panitia PSB')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Dashboard Panitia PSB</h1>
        <p class="text-sm text-surface-500 mt-1">Ringkasan data Penerimaan Santri Baru {{ $tahunAktif->nama ?? '' }}.</p>
    </div>
</div>
@endsection

@section('content')

{{-- Statistik Kartu --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
    <div class="bg-white rounded-xl border border-surface-200 p-4 text-center">
        <div class="text-2xl font-bold text-surface-900">{{ $totalPendaftar }}</div>
        <div class="text-xs text-surface-500 mt-1">Total Pendaftar</div>
    </div>
    <div class="bg-info-50 rounded-xl border border-info-200 p-4 text-center">
        <div class="text-2xl font-bold text-info-700">{{ $baruMasuk }}</div>
        <div class="text-xs text-info-600 mt-1">Baru Masuk</div>
    </div>
    <div class="bg-warning-50 rounded-xl border border-warning-200 p-4 text-center">
        <div class="text-2xl font-bold text-warning-700">{{ $hadirTes }}</div>
        <div class="text-xs text-warning-600 mt-1">Hadir Tes</div>
    </div>
    <div class="bg-success-50 rounded-xl border border-success-200 p-4 text-center">
        <div class="text-2xl font-bold text-success-700">{{ $diterima }}</div>
        <div class="text-xs text-success-600 mt-1">Diterima</div>
    </div>
    <div class="bg-danger-50 rounded-xl border border-danger-200 p-4 text-center">
        <div class="text-2xl font-bold text-danger-700">{{ $tidakLulus }}</div>
        <div class="text-xs text-danger-600 mt-1">Tidak Lulus</div>
    </div>
    <div class="bg-surface-100 rounded-xl border border-surface-200 p-4 text-center">
        <div class="text-2xl font-bold text-surface-600">{{ $dibatalkan }}</div>
        <div class="text-xs text-surface-500 mt-1">Dibatalkan</div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    {{-- Gelombang Aktif --}}
    <x-card title="Gelombang Aktif" :padding="false">
        @forelse($gelombangsAktif as $gel)
        <div class="p-4 {{ !$loop->last ? 'border-b border-surface-100' : '' }}">
            <div class="flex justify-between items-start">
                <div>
                    <div class="font-bold text-surface-900">{{ $gel->nama }}</div>
                    <div class="text-xs text-surface-500 mt-0.5">
                        {{ \Carbon\Carbon::parse($gel->tanggal_buka)->format('d M Y') }} — {{ \Carbon\Carbon::parse($gel->tanggal_tutup)->format('d M Y') }}
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-lg font-bold text-primary-700">{{ $gel->calon_santri_count }}</div>
                    <div class="text-xs text-surface-500">/ {{ $gel->kuota }} kuota</div>
                </div>
            </div>
            @php
                $persen = $gel->kuota > 0 ? min(($gel->calon_santri_count / $gel->kuota) * 100, 100) : 0;
            @endphp
            <div class="w-full h-2 bg-surface-200 rounded-full mt-3 overflow-hidden">
                <div class="{{ $persen >= 100 ? 'bg-danger-500' : 'bg-primary-500' }} h-full rounded-full transition-all" style="width: {{ $persen }}%"></div>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-surface-500">
            <i data-lucide="door-open" class="w-10 h-10 text-surface-300 mx-auto mb-3"></i>
            <p class="text-sm">Belum ada gelombang aktif saat ini.</p>
        </div>
        @endforelse
    </x-card>

    {{-- Pendaftar Terbaru --}}
    <x-card title="Pendaftar Terbaru" :padding="false">
        <div class="divide-y divide-surface-100">
            @forelse($pendaftarTerbaru as $cs)
            <a href="{{ route('panitia-psb.calon-santri.show', $cs) }}" class="flex items-center justify-between p-4 hover:bg-surface-50 transition-colors">
                <div>
                    <div class="font-bold text-surface-900 text-sm">{{ $cs->nama_lengkap }}</div>
                    <div class="text-xs text-surface-500 mt-0.5">
                        <span class="font-mono text-primary-600">{{ $cs->no_pendaftaran }}</span>
                        • {{ $cs->created_at->diffForHumans() }}
                    </div>
                </div>
                <div>
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
                </div>
            </a>
            @empty
            <div class="p-8 text-center text-surface-500">
                <i data-lucide="users" class="w-10 h-10 text-surface-300 mx-auto mb-3"></i>
                <p class="text-sm">Belum ada pendaftar.</p>
            </div>
            @endforelse
        </div>
    </x-card>
</div>
@endsection
