@extends('layouts.app')

@section('title', 'Riwayat Mutasi Santri')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Mutasi Santri</h1>
        <p class="text-sm text-surface-500 mt-1">Riwayat perpindahan Kamar Asrama dan Rombongan Belajar (Kelas) santri.</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.mutasi.create') }}" class="btn-primary flex items-center gap-2">
            <i data-lucide="shuffle" class="w-4 h-4"></i>
            <span>Mutasikan Santri</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<x-card :padding="false">
    {{-- Tabs --}}
    <div class="border-b border-surface-200">
        <nav class="-mb-px flex space-x-6 px-6" aria-label="Tabs">
            <a href="{{ route('admin.mutasi.index', ['search' => request('search')]) }}" 
               class="shrink-0 border-b-2 px-1 py-4 text-sm font-semibold {{ !request()->has('jenis_mutasi') ? 'border-primary-500 text-primary-600' : 'border-transparent text-surface-500 hover:border-surface-300 hover:text-surface-700' }}">
                Semua Riwayat
            </a>
            <a href="{{ route('admin.mutasi.index', ['jenis_mutasi' => 'ASRAMA', 'search' => request('search')]) }}" 
               class="shrink-0 border-b-2 px-1 py-4 text-sm font-semibold {{ request('jenis_mutasi') === 'ASRAMA' ? 'border-primary-500 text-primary-600' : 'border-transparent text-surface-500 hover:border-surface-300 hover:text-surface-700' }}">
                Mutasi Kamar Asrama
            </a>
            <a href="{{ route('admin.mutasi.index', ['jenis_mutasi' => 'ROMBEL', 'search' => request('search')]) }}" 
               class="shrink-0 border-b-2 px-1 py-4 text-sm font-semibold {{ request('jenis_mutasi') === 'ROMBEL' ? 'border-primary-500 text-primary-600' : 'border-transparent text-surface-500 hover:border-surface-300 hover:text-surface-700' }}">
                Mutasi Kelas (Rombel)
            </a>
        </nav>
    </div>

    {{-- Search Bar --}}
    <div class="p-4 border-b border-surface-100 bg-surface-50">
        <form action="{{ route('admin.mutasi.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            @if(request()->has('jenis_mutasi'))
                <input type="hidden" name="jenis_mutasi" value="{{ request('jenis_mutasi') }}">
            @endif
            <div class="flex-1 relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama santri atau NIK..." 
                       class="w-full pl-10 pr-4 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors">
            </div>
            <button type="submit" class="btn-primary px-6 py-2">Cari</button>
            @if(request()->anyFilled(['search', 'jenis_mutasi']))
                <a href="{{ route('admin.mutasi.index') }}" class="btn-secondary px-4 py-2 text-danger-600 border-danger-200 hover:bg-danger-50 flex items-center justify-center" title="Reset Filter">
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
                    <th class="px-6 py-4 font-semibold w-12 text-center">No</th>
                    <th class="px-6 py-4 font-semibold">Santri</th>
                    <th class="px-6 py-4 font-semibold">Jenis Mutasi</th>
                    <th class="px-6 py-4 font-semibold">Dari</th>
                    <th class="px-6 py-4 font-semibold">Ke (Tujuan)</th>
                    <th class="px-6 py-4 font-semibold">Tanggal</th>
                    <th class="px-6 py-4 font-semibold">Keterangan</th>
                    <th class="px-6 py-4 font-semibold">Operator</th>
                    <th class="px-6 py-4 font-semibold text-right w-20">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100 text-surface-700">
                @forelse($mutasis as $index => $m)
                <tr class="hover:bg-surface-50/50 transition-colors">
                    <td class="px-6 py-4 text-center text-surface-400 font-medium">
                        {{ $mutasis->firstItem() + $index }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-surface-900 text-base">
                            {{ $m->pesertaDidik->orang->nama_lengkap }}
                        </div>
                        <div class="text-xs text-primary-600 font-mono font-medium mt-0.5">
                            NIUP: {{ $m->pesertaDidik->orang->niup }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($m->jenis_mutasi === 'ASRAMA')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <i data-lucide="home" class="w-3.5 h-3.5"></i>
                                Kamar Asrama
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                <i data-lucide="book-open" class="w-3.5 h-3.5"></i>
                                Kelas (Rombel)
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-danger-600 flex items-center gap-1.5">
                            <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                            {{ $m->dari_posisi ?? '-' }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-emerald-600 flex items-center gap-1.5">
                            <i data-lucide="log-in" class="w-3.5 h-3.5"></i>
                            {{ $m->ke_posisi }}
                        </div>
                    </td>
                    <td class="px-6 py-4 font-medium text-surface-900">
                        {{ $m->tanggal_mutasi->translatedFormat('d M Y') }}
                    </td>
                    <td class="px-6 py-4 max-w-xs truncate" title="{{ $m->keterangan }}">
                        <span class="text-surface-600 text-sm">
                            {{ $m->keterangan ?? 'Tanpa keterangan' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs font-medium text-surface-500">
                        {{ $m->operator->name ?? 'Sistem' }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <form action="{{ route('admin.mutasi.destroy', $m) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus log mutasi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-danger-600 hover:text-danger-700 p-1.5 rounded-lg hover:bg-danger-50 transition-colors" title="Hapus Log">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-12 text-center text-surface-400">
                        <div class="w-16 h-16 bg-surface-50 text-surface-400 rounded-full flex items-center justify-center mx-auto mb-4 border border-surface-100">
                            <i data-lucide="shuffle" class="w-8 h-8"></i>
                        </div>
                        <h3 class="text-base font-bold text-surface-800 mb-1">Belum Ada Riwayat Mutasi</h3>
                        <p class="text-sm text-surface-500">Seluruh pemindahan kamar asrama dan rombel santri akan tercatat di sini secara otomatis.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($mutasis->hasPages())
    <div class="p-4 border-t border-surface-100">
        {{ $mutasis->links() }}
    </div>
    @endif
</x-card>
@endsection
