@extends('layouts.app')

@section('title', 'Data Master Orang (NIUP)')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Data Induk Orang</h1>
        <p class="text-sm text-surface-500 mt-1">Sistem Identitas Tunggal (Single Identity Management) Pesantren.</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.orang.create') }}" class="btn-primary flex items-center gap-2">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>Register NIUP Baru</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<x-card :padding="false">
    {{-- Search & Filter Bar --}}
    <div class="p-4 border-b border-surface-100 bg-surface-50">
        <form action="{{ route('admin.orang.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama, NIUP, atau NIK..." 
                       class="w-full pl-10 pr-4 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors">
            </div>
            <div class="sm:w-48">
                <select name="jenis_kelamin" class="w-full px-4 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" onchange="this.form.submit()">
                    <option value="">Semua Jenis Kelamin</option>
                    <option value="L" {{ request('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ request('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            <button type="submit" class="btn-secondary px-4 py-2 hidden sm:block">Filter</button>
            @if(request()->anyFilled(['search', 'jenis_kelamin']))
                <a href="{{ route('admin.orang.index') }}" class="btn-secondary px-4 py-2 text-danger-600 border-danger-200 hover:bg-danger-50 flex items-center justify-center">
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
                    <th class="px-6 py-4 font-semibold">NIUP & Identitas</th>
                    <th class="px-6 py-4 font-semibold">L/P</th>
                    <th class="px-6 py-4 font-semibold">TTL</th>
                    <th class="px-6 py-4 font-semibold">Relasi</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100 text-surface-700">
                @forelse($orangs as $orang)
                <tr class="hover:bg-surface-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-surface-200 flex items-center justify-center text-surface-600 font-bold overflow-hidden">
                                @if($orang->foto)
                                    <img src="{{ asset('storage/' . $orang->foto) }}" alt="Foto" class="w-full h-full object-cover">
                                @else
                                    {{ substr($orang->nama_lengkap, 0, 1) }}
                                @endif
                            </div>
                            <div>
                                <div class="font-bold text-surface-900 text-base">{{ $orang->nama_lengkap }}</div>
                                <div class="text-xs text-primary-600 font-mono font-medium mt-0.5">{{ $orang->niup }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-md {{ $orang->jenis_kelamin === 'L' ? 'bg-primary-100 text-primary-700' : 'bg-warning-100 text-warning-700' }} font-bold text-xs">
                            {{ $orang->jenis_kelamin }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div>{{ $orang->tempat_lahir ?? '-' }},</div>
                        <div class="text-surface-500">{{ $orang->tanggal_lahir ? $orang->tanggal_lahir->format('d M Y') : '-' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-1 flex-wrap w-32">
                            @if($orang->pesertaDidik)
                                <x-badge variant="primary" class="text-[0.65rem] px-1.5 py-0.5">Santri</x-badge>
                            @endif
                            
                            @if($orang->pegawai)
                                <x-badge variant="secondary" class="text-[0.65rem] px-1.5 py-0.5">Pegawai</x-badge>
                            @endif
                            
                            @if($orang->user)
                                <x-badge variant="warning" class="text-[0.65rem] px-1.5 py-0.5">Admin</x-badge>
                            @endif
                            
                            @if(!$orang->pesertaDidik && !$orang->pegawai && !$orang->user)
                                <x-badge variant="surface" class="text-[0.65rem] px-1.5 py-0.5">Identitas</x-badge>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($orang->is_active)
                            <x-badge variant="success" dot>Aktif</x-badge>
                        @else
                            <x-badge variant="danger" dot>Meninggal/Nonaktif</x-badge>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.orang.show', $orang) }}" class="inline-flex text-surface-400 hover:text-primary-600 p-2 rounded-lg hover:bg-primary-50 transition-colors" title="Lihat Detail">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </a>
                        <a href="{{ route('admin.orang.edit', $orang) }}" class="inline-flex text-primary-600 hover:text-primary-700 p-2 rounded-lg hover:bg-primary-50 transition-colors" title="Edit">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-surface-500">
                        <div class="flex flex-col items-center justify-center">
                            <i data-lucide="users" class="w-12 h-12 text-surface-300 mb-3"></i>
                            <p class="font-medium text-surface-900 mb-1">Data Induk Kosong</p>
                            <p class="text-sm">Belum ada data orang (NIUP) yang terdaftar atau ditemukan.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination --}}
    @if($orangs->hasPages())
    <div class="p-4 border-t border-surface-100">
        {{ $orangs->links() }}
    </div>
    @endif
</x-card>
@endsection
