@extends('layouts.app')

@section('title', 'Data Peserta Didik (Santri) — PP Nurul Furqon')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Data Peserta Didik (Santri)</h1>
        <p class="text-sm text-surface-500 mt-1">Manajemen profil akademik santri dan siswa pesantren.</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.peserta-didik.create') }}" class="btn-primary flex items-center gap-2 py-2.5 px-4 rounded-xl font-bold text-xs shadow-md shadow-primary-700/20">
            <i data-lucide="graduation-cap" class="w-4 h-4"></i>
            <span>+ Daftarkan Santri Baru</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-4">

    {{-- Pill Tabs --}}
    <div class="flex gap-2 overflow-x-auto pb-1 border-b border-surface-200">
        @php
            $tabs = [
                'aktif' => ['label' => 'Santri Aktif', 'count' => $countAktif, 'icon' => 'user-check'],
                'alumni' => ['label' => 'Alumni', 'count' => $countAlumni, 'icon' => 'award'],
                'keluar' => ['label' => 'Keluar / Mutasi', 'count' => $countKeluar, 'icon' => 'user-minus'],
                'semua' => ['label' => 'Semua Santri', 'count' => null, 'icon' => 'users'],
            ];
        @endphp
        @foreach($tabs as $key => $t)
            @php $isActive = ($tab === $key); @endphp
            <a href="{{ route('admin.peserta-didik.index', ['tab' => $key, 'search' => request('search'), 'lembaga_id' => request('lembaga_id'), 'angkatan' => request('angkatan')]) }}" 
               class="px-4 py-2 rounded-2xl text-xs font-bold whitespace-nowrap transition-all border flex items-center gap-2 {{ $isActive ? 'bg-primary-700 text-white border-primary-700 shadow-md shadow-primary-700/20 scale-105' : 'bg-white text-surface-600 border-surface-200 hover:bg-surface-50 hover:text-surface-900' }}"
               style="{{ $isActive ? 'color: #ffffff !important; background-color: #047857 !important;' : '' }}">
                <i data-lucide="{{ $t['icon'] }}" class="w-3.5 h-3.5 {{ $isActive ? 'text-warning-300' : 'text-surface-400' }}"></i>
                <span style="{{ $isActive ? 'color: #ffffff !important;' : '' }}">{{ $t['label'] }}</span>
                @if($t['count'] !== null)
                    <span class="px-2 py-0.5 rounded-full text-[0.65rem] font-extrabold {{ $isActive ? 'bg-white/20 text-white' : 'bg-surface-100 text-surface-600' }}">
                        {{ $t['count'] }}
                    </span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- Main Table Card --}}
    <div class="bg-white rounded-3xl border border-surface-200 shadow-sm overflow-hidden">
        
        {{-- Search & Filter Bar --}}
        <div class="p-4 border-b border-surface-100 bg-surface-50/70">
            <form action="{{ route('admin.peserta-didik.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <input type="hidden" name="tab" value="{{ $tab }}">
                
                {{-- Search Box with Guaranteed Padding --}}
                <div class="flex-1 relative">
                    <div class="absolute top-1/2 -translate-y-1/2 text-surface-400 pointer-events-none flex items-center justify-center" style="left: 1.25rem !important;">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama, NIUP, NIS, atau NISN santri..." 
                           class="w-full pr-4 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-medium text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors"
                           style="padding-left: 3.25rem !important;">
                </div>

                {{-- Filter Lembaga --}}
                <div class="sm:w-52">
                    <select name="lembaga_id" class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-semibold text-surface-800 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" onchange="this.form.submit()">
                        <option value="">Semua Lembaga</option>
                        @foreach($lembagas as $l)
                            <option value="{{ $l->id }}" {{ request('lembaga_id') == $l->id ? 'selected' : '' }}>{{ $l->nama }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Angkatan --}}
                <div class="sm:w-44">
                    <input type="number" name="angkatan" value="{{ request('angkatan') }}" placeholder="Tahun Angkatan..." 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-medium text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors"
                           onchange="this.form.submit()">
                </div>

                {{-- Reset Button --}}
                @if(request()->anyFilled(['search', 'lembaga_id', 'angkatan']))
                    <a href="{{ route('admin.peserta-didik.index', ['tab' => $tab]) }}" class="btn-secondary px-3.5 py-2.5 rounded-xl text-xs font-bold text-danger-600 border-danger-200 hover:bg-danger-50 flex items-center justify-center gap-1 shrink-0">
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
                        <th class="px-6 py-3.5">Identitas Santri</th>
                        <th class="px-6 py-4 font-semibold">NIS / NISN</th>
                        <th class="px-6 py-4 font-semibold text-center">L/P</th>
                        <th class="px-6 py-4 font-semibold">Tgl Masuk</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100 text-surface-700">
                    @forelse($pesertaDidiks as $pd)
                    <tr class="hover:bg-primary-50/30 transition-colors">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-primary-100 text-primary-700 font-bold text-xs flex items-center justify-center shrink-0 border border-primary-200">
                                    {{ substr($pd->orang->nama_lengkap ?? 'S', 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-extrabold text-surface-900 text-sm leading-tight">{{ $pd->orang->nama_lengkap }}</div>
                                    <div class="text-[0.68rem] text-primary-700 font-mono font-bold mt-0.5">NIUP: {{ $pd->orang->niup }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3.5">
                            <div class="font-bold text-surface-900">{{ $pd->nis ?? '-' }}</div>
                            <div class="text-[0.65rem] text-surface-450 mt-0.5">{{ $pd->nisn ?? 'Tanpa NISN' }}</div>
                        </td>
                        <td class="px-6 py-3.5 text-center">
                            <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-md font-extrabold text-[0.65rem] uppercase {{ $pd->orang->jenis_kelamin == 'L' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                {{ $pd->orang->jenis_kelamin }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-surface-600 font-medium">
                            {{ $pd->tanggal_masuk ? $pd->tanggal_masuk->format('d M Y') : '-' }}
                        </td>
                        <td class="px-6 py-3.5">
                            @if($pd->status === 'AKTIF')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[0.65rem] font-extrabold uppercase bg-emerald-100 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Aktif
                                </span>
                            @elseif($pd->status === 'LULUS')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[0.65rem] font-extrabold uppercase bg-blue-100 text-blue-700 border border-blue-200">
                                    Lulus
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[0.65rem] font-extrabold uppercase bg-rose-100 text-rose-700 border border-rose-200">
                                    {{ str_replace('_', ' ', $pd->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-right">
                            <div class="inline-flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.peserta-didik.show', $pd) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white border border-emerald-200 text-xs font-bold transition-all shadow-2xs" title="Lihat Profil Akademik">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    <span>Detail</span>
                                </a>
                                <a href="{{ route('admin.peserta-didik.edit', $pd) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white border border-blue-200 text-xs font-bold transition-all shadow-2xs" title="Edit Data Santri">
                                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                    <span>Edit</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-surface-500">
                            <div class="flex flex-col items-center justify-center">
                                <i data-lucide="graduation-cap" class="w-12 h-12 text-surface-300 mb-3"></i>
                                <p class="font-bold text-surface-900 mb-1">Belum Ada Santri</p>
                                <p class="text-xs text-surface-450">Tidak ada data peserta didik yang terdaftar atau ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($pesertaDidiks->hasPages())
        <div class="p-4 border-t border-surface-100">
            {{ $pesertaDidiks->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
