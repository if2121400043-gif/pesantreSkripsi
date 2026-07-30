@extends('layouts.app')

@section('title', 'Dashboard Bendahara')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Dashboard Bendahara</h1>
        <p class="text-sm text-surface-500 mt-1">Sistem Informasi Keuangan Pesantren {{ $tahunAktif->nama ?? '' }}.</p>
    </div>
</div>
@endsection

@section('content')

{{-- Statistik Utama Keuangan --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    {{-- Card 1: Pemasukan (Green) --}}
    <div class="bg-success-50 rounded-xl border border-success-200 p-5 flex items-center justify-between transition-transform hover:scale-101">
        <div>
            <div class="text-sm font-semibold text-success-800">Total Pemasukan Kas</div>
            <div class="text-2xl font-bold text-success-900 mt-1">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
            <div class="text-xs text-success-600 mt-1">Penerimaan riil dari semua tagihan</div>
        </div>
        <div class="p-3 bg-success-500 text-white rounded-xl">
            <i data-lucide="wallet" class="w-6 h-6"></i>
        </div>
    </div>
    
    {{-- Card 2: Tunggakan (Red) --}}
    <div class="bg-danger-50 rounded-xl border border-danger-200 p-5 flex items-center justify-between transition-transform hover:scale-101">
        <div>
            <div class="text-sm font-semibold text-danger-800">Total Piutang/Tunggakan</div>
            <div class="text-2xl font-bold text-danger-900 mt-1">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</div>
            <div class="text-xs text-danger-600 mt-1">Sisa tagihan yang belum dibayarkan</div>
        </div>
        <div class="p-3 bg-danger-500 text-white rounded-xl">
            <i data-lucide="alert-circle" class="w-6 h-6"></i>
        </div>
    </div>

    {{-- Card 3: Realisasi (Emerald / Primary) --}}
    <div class="bg-primary-50 rounded-xl border border-primary-200 p-5 flex items-center justify-between transition-transform hover:scale-101">
        <div class="flex-1 mr-4">
            <div class="text-sm font-semibold text-primary-800">Realisasi Bulan Ini ({{ $rekapBulanIni['bulan'] }})</div>
            <div class="text-2xl font-bold text-primary-900 mt-1">Rp {{ number_format($rekapBulanIni['pemasukan'], 0, ',', '.') }}</div>
            <div class="text-xs text-primary-600 mt-1">{{ $rekapBulanIni['persenLunas'] }}% Terbayar (Dari Rp {{ number_format($rekapBulanIni['total'], 0, ',', '.') }})</div>
            
            {{-- Progress Bar --}}
            <div class="w-full bg-primary-200/50 rounded-full h-1.5 mt-2.5 overflow-hidden">
                <div class="bg-primary-600 h-1.5 rounded-full" style="width: {{ $rekapBulanIni['persenLunas'] }}%"></div>
            </div>
        </div>
        <div class="p-3 bg-primary-500 text-white rounded-xl flex-shrink-0">
            <i data-lucide="trending-up" class="w-6 h-6"></i>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    {{-- Transaksi Pembayaran Terbaru --}}
    <x-card title="Transaksi Pembayaran Terbaru">
        <div class="divide-y divide-surface-100 -mx-6 -my-4">
            @forelse($pembayaranTerbaru as $p)
                <div class="flex items-center justify-between p-4 px-6 hover:bg-surface-50/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-success-50 text-success-600 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="check" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-surface-900 leading-tight">{{ $p->tagihan->pesertaDidik->orang->nama_lengkap ?? '-' }}</h4>
                            <p class="text-[0.7rem] text-surface-450 font-mono mt-0.5">{{ $p->no_transaksi }} • {{ $p->tanggal_bayar->format('d/m/Y H:i') }}</p>
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-surface-600 mt-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>
                                {{ $p->tagihan->komponenBiaya->nama }} (T.A {{ $p->tagihan->tahunPelajaran->nama }})
                            </span>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span class="text-sm font-mono font-bold text-success-600 block">+ Rp {{ number_format($p->jumlah, 0, ',', '.') }}</span>
                        <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wider bg-surface-100 text-surface-700 border border-surface-200">{{ $p->metode }}</span>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-surface-500">
                    <i data-lucide="history" class="w-8 h-8 text-surface-300 mx-auto mb-2"></i>
                    <p class="text-sm">Belum ada transaksi pembayaran yang tercatat.</p>
                </div>
            @endforelse
        </div>
    </x-card>

    {{-- Tunggakan Jatuh Tempo Terdekat --}}
    <x-card title="Jatuh Tempo Terdekat">
        <div class="divide-y divide-surface-100 -mx-6 -my-4">
            @forelse($tunggakanJatuhTempo as $t)
                @php
                    $isOverdue = $t->jatuh_tempo && $t->jatuh_tempo->isPast();
                    $sisa = $t->total - $t->pembayaran->sum('jumlah');
                @endphp
                <div class="flex items-center justify-between p-4 px-6 hover:bg-surface-50/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 {{ $isOverdue ? 'bg-danger-50 text-danger-600' : 'bg-warning-50 text-warning-600' }}">
                            <i data-lucide="{{ $isOverdue ? 'alert-triangle' : 'calendar' }}" class="w-5 h-5"></i>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-sm font-bold text-surface-900 leading-tight truncate">{{ $t->pesertaDidik->orang->nama_lengkap ?? '-' }}</h4>
                            <p class="text-[11px] text-surface-500 mt-0.5 truncate">
                                {{ $t->komponenBiaya->nama }} (T.A {{ $t->tahunPelajaran->nama }})
                            </p>
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-semibold mt-1 px-2 py-0.5 rounded-full {{ $isOverdue ? 'bg-danger-50 text-danger-700 border border-danger-100' : 'bg-warning-50 text-warning-700 border border-warning-100' }}">
                                {{ $isOverdue ? 'Terlambat' : 'Jatuh Tempo' }}: {{ $t->jatuh_tempo ? $t->jatuh_tempo->format('d M Y') : '-' }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right flex items-center gap-4 flex-shrink-0">
                        <div>
                            <span class="text-sm font-mono font-bold text-danger-600 block">Rp {{ number_format($sisa, 0, ',', '.') }}</span>
                            <span class="text-[10px] text-surface-400 mt-0.5 block">Sisa Tagihan</span>
                        </div>
                        <a href="{{ route('bendahara.tagihan.show', $t) }}" class="btn-primary py-1.5 px-3.5 rounded-xl text-xs font-bold flex items-center gap-1">
                            <i data-lucide="banknote" class="w-3.5 h-3.5 text-white"></i>
                            <span class="text-white">Bayar</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-surface-500">
                    <i data-lucide="check-circle" class="w-8 h-8 text-success-300 mx-auto mb-2"></i>
                    <p class="text-sm">Luar biasa! Tidak ada tagihan yang belum lunas.</p>
                </div>
            @endforelse
        </div>
    </x-card>
</div>
@endsection
