@extends('layouts.app')

@section('title', 'Data Master Orang (NIUP) — PP Nurul Furqon')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Data Induk Orang</h1>
        <p class="text-sm text-surface-500 mt-1">Sistem Identitas Tunggal (Single Identity Management) Pesantren.</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.orang.create') }}" class="btn-primary flex items-center gap-2 py-2.5 px-4 rounded-xl font-bold text-xs shadow-md shadow-primary-700/20">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>+ Register NIUP Baru</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="bg-white rounded-3xl border border-surface-200 shadow-sm overflow-hidden">
    
    {{-- Search & Filter Bar with Guaranteed Padding --}}
    <div class="p-4 border-b border-surface-100 bg-surface-50/70">
        <form action="{{ route('admin.orang.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            
            {{-- Search Box with Guaranteed Padding --}}
            <div class="flex-1 relative">
                <div class="absolute top-1/2 -translate-y-1/2 text-surface-400 pointer-events-none flex items-center justify-center" style="left: 1.25rem !important;">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama, NIUP, atau NIK..." 
                       class="w-full pr-4 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-medium text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors"
                       style="padding-left: 3.25rem !important;">
            </div>

            {{-- Filter Gender --}}
            <div class="sm:w-52">
                <select name="jenis_kelamin" class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-semibold text-surface-800 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" onchange="this.form.submit()">
                    <option value="">Semua Jenis Kelamin</option>
                    <option value="L" {{ request('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki (Putra)</option>
                    <option value="P" {{ request('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan (Putri)</option>
                </select>
            </div>

            {{-- Reset Button --}}
            @if(request()->anyFilled(['search', 'jenis_kelamin']))
                <a href="{{ route('admin.orang.index') }}" class="btn-secondary px-3.5 py-2.5 rounded-xl text-xs font-bold text-danger-600 border-danger-200 hover:bg-danger-50 flex items-center justify-center gap-1 shrink-0">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    <span>Reset</span>
                </a>
            @endif
        </form>
    </div>

    {{-- Data Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs whitespace-nowrap">
            <thead class="bg-surface-100/70 text-surface-600 border-b border-surface-200 uppercase tracking-wider text-[0.68rem] font-bold">
                <tr>
                    <th class="px-6 py-3.5">NIUP & Identitas</th>
                    <th class="px-6 py-4 font-semibold text-center">L/P</th>
                    <th class="px-6 py-4 font-semibold">Relasi Sistem</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100 text-surface-700">
                @forelse($orangs as $orang)
                <tr class="hover:bg-primary-50/30 transition-colors">
                    <td class="px-6 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 font-bold text-xs flex items-center justify-center shrink-0 border border-amber-200 overflow-hidden">
                                @if($orang->foto)
                                    <img src="{{ asset('storage/' . $orang->foto) }}" alt="Foto" class="w-full h-full object-cover">
                                @else
                                    {{ substr($orang->nama_lengkap ?? 'O', 0, 1) }}
                                @endif
                            </div>
                            <div>
                                <div class="font-extrabold text-surface-900 text-sm leading-tight">{{ $orang->nama_lengkap }}</div>
                                <div class="text-[0.68rem] text-primary-700 font-mono font-bold mt-0.5">NIUP: {{ $orang->niup }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-3.5 text-center">
                        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-md font-extrabold text-[0.65rem] uppercase {{ $orang->jenis_kelamin === 'L' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                            {{ $orang->jenis_kelamin }}
                        </span>
                    </td>
                    <td class="px-6 py-3.5">
                        <div class="flex gap-1 flex-wrap w-36">
                            @if($orang->pesertaDidik)
                                <span class="px-2 py-0.5 rounded text-[0.62rem] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">Santri</span>
                            @endif
                            
                            @if($orang->pegawai)
                                <span class="px-2 py-0.5 rounded text-[0.62rem] font-extrabold uppercase bg-blue-100 text-blue-800 border border-blue-200">Pegawai</span>
                            @endif
                            
                            @if($orang->user)
                                <span class="px-2 py-0.5 rounded text-[0.62rem] font-extrabold uppercase bg-amber-100 text-amber-800 border border-amber-200">Admin</span>
                            @endif
                            
                            @if(!$orang->pesertaDidik && !$orang->pegawai && !$orang->user)
                                <span class="px-2 py-0.5 rounded text-[0.62rem] font-extrabold uppercase bg-surface-200 text-surface-700 border border-surface-300">Identitas</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-3.5">
                        @if($orang->is_active)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[0.65rem] font-extrabold uppercase bg-emerald-100 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[0.65rem] font-extrabold uppercase bg-rose-100 text-rose-700 border border-rose-200">
                                Nonaktif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-3.5 text-right">
                        <div class="inline-flex items-center gap-1">
                            <a href="{{ route('admin.orang.show', $orang) }}" class="p-1.5 rounded-lg text-surface-500 hover:text-primary-700 hover:bg-primary-50 transition-colors" title="Lihat Detail">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            <a href="{{ route('admin.orang.edit', $orang) }}" class="p-1.5 rounded-lg text-primary-600 hover:text-primary-800 hover:bg-primary-50 transition-colors" title="Edit">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-surface-500">
                        <div class="flex flex-col items-center justify-center">
                            <i data-lucide="users" class="w-12 h-12 text-surface-300 mb-3"></i>
                            <p class="font-bold text-surface-900 mb-1">Data Induk Kosong</p>
                            <p class="text-xs text-surface-450">Belum ada data orang (NIUP) yang terdaftar atau ditemukan.</p>
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
</div>
@endsection
