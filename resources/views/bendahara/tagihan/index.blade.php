@extends('layouts.bendahara')

@section('title', 'Data Tagihan & Pembayaran')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Data Tagihan Siswa</h1>
        <p class="text-sm text-surface-500 mt-1">Pantau tagihan yang belum lunas dan kelola riwayat tagihan.</p>
    </div>
    <div class="flex flex-wrap gap-3">
        @if($totalBelumLunas > 0)
        <form action="{{ route('bendahara.tagihan.blast-reminder') }}" method="POST"
              onsubmit="return confirm('📢 Kirim pengingat WhatsApp ke SEMUA wali santri yang punya tagihan belum lunas ({{ $totalBelumLunas }} tagihan)?\n\nProses ini memakan waktu ±{{ $totalBelumLunas * 2 }} detik.');">
            @csrf
            <button type="submit" class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold border-2 border-green-500 text-green-700 bg-green-50 hover:bg-green-100 transition-colors">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                <span>Blast Pengingat WA</span>
                <span class="inline-flex items-center justify-center w-5 h-5 text-[0.6rem] font-bold rounded-full bg-green-600 text-white">{{ $totalBelumLunas }}</span>
            </button>
        </form>
        @endif
        <a href="{{ route('bendahara.tagihan.create') }}" class="btn-primary flex items-center gap-2">
            <i data-lucide="calculator" class="w-4 h-4"></i>
            <span>Generate Tagihan Massal</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<x-card :padding="false">
    {{-- Search & Filter Bar --}}
    <div class="p-4 border-b border-surface-100 bg-surface-50">
        <form action="{{ route('bendahara.tagihan.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama Santri atau NIUP..." 
                       class="w-full pl-10 pr-4 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors">
            </div>
            <div class="sm:w-48">
                <select name="status" class="w-full px-4 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="BELUM_BAYAR" {{ request('status') == 'BELUM_BAYAR' ? 'selected' : '' }}>Belum Lunas</option>
                    <option value="SEBAGIAN" {{ request('status') == 'SEBAGIAN' ? 'selected' : '' }}>Lunas Sebagian (Cicil)</option>
                    <option value="LUNAS" {{ request('status') == 'LUNAS' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>
            <div class="sm:w-48">
                <select name="tahun_pelajaran_id" class="w-full px-4 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" onchange="this.form.submit()">
                    <option value="">Semua Tahun</option>
                    @foreach($tahuns as $tahun)
                        <option value="{{ $tahun->id }}" {{ request('tahun_pelajaran_id') == $tahun->id ? 'selected' : '' }}>
                            {{ $tahun->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-secondary px-4 py-2 hidden sm:block">Filter</button>
            @if(request()->anyFilled(['search', 'status', 'tahun_pelajaran_id']))
                <a href="{{ route('bendahara.tagihan.index') }}" class="btn-secondary px-4 py-2 text-danger-600 border-danger-200 hover:bg-danger-50 flex items-center justify-center">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </a>
            @endif
        </form>
    </div>

    {{-- Data Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-surface-50/50 text-surface-600 border-b border-surface-100">
                <tr>
                    <th class="px-6 py-4 font-semibold">Tgl Invoice</th>
                    <th class="px-6 py-4 font-semibold">Nama Santri</th>
                    <th class="px-6 py-4 font-semibold">Jenis Tagihan</th>
                    <th class="px-6 py-4 font-semibold text-right">Total (Rp)</th>
                    <th class="px-6 py-4 font-semibold text-center">Status</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100 text-surface-700">
                @forelse($tagihans as $tagihan)
                <tr class="hover:bg-surface-50/50 transition-colors">
                    <td class="px-6 py-4 text-surface-500 text-xs">
                        {{ $tagihan->created_at->format('d/m/Y') }}<br>
                        <span class="font-mono">{{ 'INV-'.str_pad($tagihan->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-surface-900">{{ $tagihan->pesertaDidik->orang->nama_lengkap }}</div>
                        <div class="text-xs text-primary-600 font-mono mt-0.5">{{ $tagihan->pesertaDidik->orang->niup }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-surface-900">{{ $tagihan->komponenBiaya->nama }}</div>
                        <div class="text-xs text-surface-500 mt-0.5">
                            {{ $tagihan->bulan ? 'Bulan: ' . $tagihan->bulan : 'Thn: ' . $tagihan->tahunPelajaran->nama }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right font-mono font-bold text-primary-700">
                        {{ number_format($tagihan->total, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($tagihan->status === 'LUNAS')
                            <x-badge variant="success" dot>LUNAS</x-badge>
                        @elseif($tagihan->status === 'SEBAGIAN')
                            <x-badge variant="warning" dot>MENCICIL</x-badge>
                        @else
                            <x-badge variant="danger" dot>BELUM LUNAS</x-badge>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('bendahara.tagihan.show', $tagihan) }}" class="inline-flex btn-primary py-1 px-3 text-xs gap-1">
                            <i data-lucide="eye" class="w-3 h-3"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-surface-500">
                        <div class="flex flex-col items-center justify-center">
                            <i data-lucide="receipt" class="w-12 h-12 text-surface-300 mb-3"></i>
                            <p class="font-medium text-surface-900 mb-1">Belum Ada Tagihan</p>
                            <p class="text-sm">Tidak ada invoice tagihan yang sesuai dengan filter pencarian.</p>
                        </div>
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
</x-card>
@endsection
