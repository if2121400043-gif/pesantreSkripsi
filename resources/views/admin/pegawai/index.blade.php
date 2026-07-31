@extends('layouts.app')

@section('title', 'Data Pegawai & SDM — PP Nurul Furqon')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Data Pegawai (SDM)</h1>
        <p class="text-sm text-surface-500 mt-1">Manajemen ustadz, guru, dan staff pengelola pesantren.</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.pegawai.create') }}" class="btn-primary flex items-center gap-2 py-2.5 px-4 rounded-xl font-bold text-xs shadow-md shadow-primary-700/20">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>+ Daftarkan Pegawai</span>
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
                'aktif' => ['label' => 'Pegawai Aktif', 'count' => $countAktif, 'icon' => 'user-check'],
                'nonaktif' => ['label' => 'Nonaktif (Arsip)', 'count' => $countNonAktif, 'icon' => 'user-x'],
                'semua' => ['label' => 'Semua SDM', 'count' => null, 'icon' => 'users'],
            ];
        @endphp
        @foreach($tabs as $key => $t)
            @php $isActive = ($tab === $key); @endphp
            <a href="{{ route('admin.pegawai.index', ['tab' => $key, 'search' => request('search'), 'jenis_pegawai' => request('jenis_pegawai')]) }}" 
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
            <form action="{{ route('admin.pegawai.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <input type="hidden" name="tab" value="{{ $tab }}">
                
                {{-- Search Box with Guaranteed Padding --}}
                <div class="flex-1 relative">
                    <div class="absolute top-1/2 -translate-y-1/2 text-surface-400 pointer-events-none flex items-center justify-center" style="left: 1.25rem !important;">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama, NIP, atau NUPTK pegawai..." 
                           class="w-full pr-4 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-medium text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors"
                           style="padding-left: 3.25rem !important;">
                </div>

                {{-- Filter Jenis Pegawai --}}
                <div class="sm:w-52">
                    <select name="jenis_pegawai" class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-semibold text-surface-800 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" onchange="this.form.submit()">
                        <option value="">Semua Jenis SDM</option>
                        <option value="GURU" {{ request('jenis_pegawai') === 'GURU' ? 'selected' : '' }}>Guru / Ustadz</option>
                        <option value="PENGASUH" {{ request('jenis_pegawai') === 'PENGASUH' ? 'selected' : '' }}>Pengasuh</option>
                        <option value="STAFF_ADMIN" {{ request('jenis_pegawai') === 'STAFF_ADMIN' ? 'selected' : '' }}>Staff / Admin</option>
                        <option value="LAINNYA" {{ request('jenis_pegawai') === 'LAINNYA' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                {{-- Reset Button --}}
                @if(request()->anyFilled(['search', 'jenis_pegawai']))
                    <a href="{{ route('admin.pegawai.index', ['tab' => $tab]) }}" class="btn-secondary px-3.5 py-2.5 rounded-xl text-xs font-bold text-danger-600 border-danger-200 hover:bg-danger-50 flex items-center justify-center gap-1 shrink-0">
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
                        <th class="px-6 py-3.5">Identitas Pegawai</th>
                        <th class="px-6 py-4 font-semibold">Tugas Pokok</th>
                        <th class="px-6 py-4 font-semibold">NIP / NUPTK</th>
                        <th class="px-6 py-4 font-semibold">Status Pegawai</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100 text-surface-700">
                    @forelse($pegawais as $pegawai)
                    <tr class="hover:bg-primary-50/30 transition-colors">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-info-100 text-info-700 font-bold text-xs flex items-center justify-center shrink-0 border border-info-200">
                                    {{ substr($pegawai->orang->nama_lengkap ?? 'P', 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-extrabold text-surface-900 text-sm leading-tight">{{ $pegawai->orang->nama_lengkap }}</div>
                                    <div class="text-[0.68rem] text-primary-700 font-mono font-bold mt-0.5">NIUP: {{ $pegawai->orang->niup }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[0.65rem] font-extrabold bg-blue-100 text-blue-700 uppercase">
                                {{ str_replace('_', ' ', $pegawai->jenis_pegawai) }}
                            </span>
                            <div class="text-[0.65rem] text-surface-500 font-medium mt-0.5">{{ $pegawai->jabatan ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-3.5">
                            <div class="font-bold text-surface-900">{{ $pegawai->nip ?? '-' }}</div>
                            <div class="text-[0.65rem] text-surface-450 mt-0.5">{{ $pegawai->nuptk ?? 'Tanpa NUPTK' }}</div>
                        </td>
                        <td class="px-6 py-3.5">
                            <div class="font-semibold text-surface-800">{{ $pegawai->status_kepegawaian }}</div>
                            <div class="text-[0.65rem] mt-0.5">
                                @if($pegawai->is_active)
                                    <span class="text-emerald-700 font-extrabold">● Aktif Bekerja</span>
                                @else
                                    <span class="text-rose-600 font-extrabold">● Nonaktif</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-3.5 text-right">
                            <div class="inline-flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.pegawai.show', $pegawai) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white border border-emerald-200 text-xs font-bold transition-all shadow-2xs" title="Lihat Riwayat & Profil">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    <span>Detail</span>
                                </a>
                                <a href="{{ route('admin.pegawai.edit', $pegawai) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white border border-blue-200 text-xs font-bold transition-all shadow-2xs" title="Edit Data Pegawai">
                                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                    <span>Edit</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-surface-500">
                            <div class="flex flex-col items-center justify-center">
                                <i data-lucide="briefcase" class="w-12 h-12 text-surface-300 mb-3"></i>
                                <p class="font-bold text-surface-900 mb-1">Belum Ada Pegawai</p>
                                <p class="text-xs text-surface-450">Tidak ada data pegawai yang terdaftar atau ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($pegawais->hasPages())
        <div class="p-4 border-t border-surface-100">
            {{ $pegawais->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
