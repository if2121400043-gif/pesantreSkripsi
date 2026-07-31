@extends('layouts.portal')

@section('title', 'Keuangan & Tagihan')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Keuangan & Tagihan</h1>
        <p class="text-sm text-surface-500 mt-1">Informasi tagihan dan riwayat pembayaran santri.</p>
    </div>
</div>
@endsection

@section('content')
<x-card>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-surface-200">
                    <th class="text-left py-3 px-4 font-semibold text-surface-600">Santri</th>
                    <th class="text-left py-3 px-4 font-semibold text-surface-600">Komponen Biaya</th>
                    <th class="text-left py-3 px-4 font-semibold text-surface-600">Periode / Bulan</th>
                    <th class="text-right py-3 px-4 font-semibold text-surface-600">Total Tagihan</th>
                    <th class="text-center py-3 px-4 font-semibold text-surface-600">Jatuh Tempo</th>
                    <th class="text-center py-3 px-4 font-semibold text-surface-600">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100">
                @forelse($tagihans as $tagihan)
                <tr class="hover:bg-surface-50/50 transition-colors">
                    <td class="py-3 px-4">
                        <p class="font-semibold text-surface-900">{{ $tagihan->pesertaDidik->orang->nama ?? '-' }}</p>
                    </td>
                    <td class="py-3 px-4">
                        <p class="font-medium text-surface-800">{{ $tagihan->komponenBiaya->nama ?? '-' }}</p>
                        <p class="text-xs text-surface-500">{{ $tagihan->tahunPelajaran->nama ?? '-' }}</p>
                    </td>
                    <td class="py-3 px-4 text-surface-700">
                        {{ $tagihan->bulan ?? '-' }}
                    </td>
                    <td class="py-3 px-4 text-right font-medium text-surface-900">
                        Rp {{ number_format($tagihan->total, 0, ',', '.') }}
                    </td>
                    <td class="py-3 px-4 text-center">
                        @if($tagihan->jatuh_tempo)
                            <span class="text-xs text-surface-500">{{ $tagihan->jatuh_tempo->format('d M Y') }}</span>
                        @else
                            <span class="text-xs text-surface-400">-</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-center">
                        @php
                            $dibayarTagihan = $tagihan->pembayaran->sum('jumlah');
                            $sisaTagihan = max(0, $tagihan->total - $dibayarTagihan);
                            $statusTagihan = $sisaTagihan <= 0 ? 'LUNAS' : ($dibayarTagihan > 0 ? 'SEBAGIAN' : $tagihan->status);
                        @endphp
                        @if($statusTagihan === 'LUNAS')
                            <x-badge variant="success">LUNAS</x-badge>
                        @elseif($statusTagihan === 'SEBAGIAN')
                            <x-badge variant="warning">SEBAGIAN</x-badge>
                        @else
                            <x-badge variant="danger">BELUM LUNAS</x-badge>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-12 text-center text-surface-400">
                        <i data-lucide="check-circle" class="w-10 h-10 mx-auto mb-3 text-success-400"></i>
                        <p class="font-medium text-surface-500">Belum ada tagihan.</p>
                        <p class="text-sm mt-1 text-surface-400">Semua administrasi keuangan ananda sudah selesai.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tagihans->hasPages())
        <div class="mt-4 pt-4 border-t border-surface-100">
            {{ $tagihans->links() }}
        </div>
    @endif
</x-card>
@endsection
