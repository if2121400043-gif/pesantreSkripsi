@extends('layouts.portal')

@section('title', 'Keuangan & Tagihan Santri — PP Nurul Furqon')

@section('content')
<div class="space-y-6">

    {{-- Hero Summary Header --}}
    <div class="rounded-3xl p-6 sm:p-7 shadow-lg text-white" style="background: linear-gradient(135deg, #047857, #064e3b) !important;">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-white/15 text-[0.7rem] font-bold backdrop-blur-sm border border-white/20 mb-2">
                    <i data-lucide="wallet" class="w-3.5 h-3.5 text-emerald-300"></i>
                    Informasi Keuangan
                </span>
                <h1 class="text-xl sm:text-2xl font-extrabold font-heading">
                    Tagihan & Riwayat Pembayaran
                </h1>
                <p class="text-xs text-emerald-100 mt-1">
                    Santri: <strong class="text-white">{{ $activeAnak->orang->nama_lengkap ?? '-' }}</strong> ({{ $activeAnak->orang->niup ?? '-' }})
                </p>
            </div>

            <div class="bg-white/15 backdrop-blur-md rounded-2xl p-4 border border-white/20 text-right w-full sm:w-auto">
                <span class="text-[0.65rem] uppercase font-bold text-emerald-200 block">Total Tunggakan</span>
                <span class="text-2xl font-black text-white">Rp {{ number_format($totalTagihanBelumLunas, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Tagihan Table Card --}}
    <div class="bg-white rounded-3xl border border-surface-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-surface-100 flex justify-between items-center">
            <h2 class="text-base font-bold text-surface-900 flex items-center gap-2">
                <i data-lucide="receipt" class="w-5 h-5 text-emerald-700"></i>
                Daftar Tagihan Syahriah & Biaya
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-surface-50 border-b border-surface-200 text-xs text-surface-600 uppercase tracking-wider">
                        <th class="text-left py-3.5 px-5 font-bold">Komponen Biaya</th>
                        <th class="text-left py-3.5 px-5 font-bold">Periode / Bulan</th>
                        <th class="text-right py-3.5 px-5 font-bold">Total Tagihan</th>
                        <th class="text-center py-3.5 px-5 font-bold">Jatuh Tempo</th>
                        <th class="text-center py-3.5 px-5 font-bold">Status</th>
                        <th class="text-center py-3.5 px-5 font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100">
                    @forelse($tagihans as $tagihan)
                    <tr class="hover:bg-surface-50/50 transition-colors">
                        <td class="py-4 px-5">
                            <p class="font-bold text-surface-900">{{ $tagihan->komponenBiaya->nama ?? '-' }}</p>
                            <p class="text-xs text-surface-500">{{ $tagihan->tahunPelajaran->nama ?? '-' }}</p>
                        </td>
                        <td class="py-4 px-5 text-surface-700 font-medium">
                            {{ $tagihan->bulan ?? '-' }}
                        </td>
                        <td class="py-4 px-5 text-right font-extrabold text-surface-900">
                            Rp {{ number_format($tagihan->total, 0, ',', '.') }}
                        </td>
                        <td class="py-4 px-5 text-center text-xs text-surface-500">
                            {{ $tagihan->jatuh_tempo ? $tagihan->jatuh_tempo->format('d M Y') : '-' }}
                        </td>
                        <td class="py-4 px-5 text-center">
                            @php
                                $dibayarTagihan = $tagihan->pembayaran->sum('jumlah');
                                $sisaTagihan = max(0, $tagihan->total - $dibayarTagihan);
                                $statusTagihan = $sisaTagihan <= 0 ? 'LUNAS' : ($dibayarTagihan > 0 ? 'SEBAGIAN' : $tagihan->status);
                            @endphp
                            @if($statusTagihan === 'LUNAS')
                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-full border border-emerald-200">LUNAS</span>
                            @elseif($statusTagihan === 'SEBAGIAN')
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-800 text-xs font-bold rounded-full border border-amber-200">SEBAGIAN</span>
                            @else
                                <span class="px-2.5 py-1 bg-rose-100 text-rose-800 text-xs font-bold rounded-full border border-rose-200">BELUM LUNAS</span>
                            @endif
                        </td>
                        <td class="py-4 px-5 text-center">
                            @if($sisaTagihan > 0)
                                <a href="{{ route('portal.payment.show', $tagihan) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold rounded-xl shadow-sm transition-all duration-200">
                                    <i data-lucide="credit-card" class="w-3.5 h-3.5"></i>
                                    Bayar
                                </a>
                            @else
                                <span class="text-xs text-emerald-600 font-bold flex items-center justify-center gap-1">
                                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i> Lunas
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-surface-400">
                            <i data-lucide="check-circle" class="w-12 h-12 mx-auto mb-3 text-emerald-500"></i>
                            <p class="font-bold text-surface-800 text-base">Belum Ada Tagihan</p>
                            <p class="text-xs mt-1 text-surface-500">Semua administrasi keuangan ananda sudah lunas dan selesai.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tagihans->hasPages())
            <div class="p-4 border-t border-surface-100">
                {{ $tagihans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
