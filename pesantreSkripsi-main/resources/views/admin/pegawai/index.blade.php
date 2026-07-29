@extends('layouts.app')

@section('title', 'Data Pegawai & SDM')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Data Pegawai (SDM)</h1>
        <p class="text-sm text-surface-500 mt-1">Manajemen ustadz, guru, dan staff pengelola pesantren.</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.pegawai.create') }}" class="btn-primary flex items-center gap-2">
            <i data-lucide="briefcase" class="w-4 h-4"></i>
            <span>Daftarkan Pegawai</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<x-card :padding="false">
    {{-- Tabs --}}
    <div class="border-b border-surface-200">
        <nav class="-mb-px flex space-x-6 px-6" aria-label="Tabs">
            <a href="{{ route('admin.pegawai.index', ['tab' => 'aktif', 'search' => request('search'), 'jenis_pegawai' => request('jenis_pegawai')]) }}" 
               class="shrink-0 border-b-2 px-1 py-4 text-sm font-semibold {{ $tab === 'aktif' ? 'border-primary-500 text-primary-600' : 'border-transparent text-surface-500 hover:border-surface-300 hover:text-surface-700' }}">
                Pegawai Aktif
                <span class="ml-2 rounded-full px-2 py-0.5 text-xs font-semibold {{ $tab === 'aktif' ? 'bg-primary-100 text-primary-600' : 'bg-surface-100 text-surface-600' }}">{{ $countAktif }}</span>
            </a>
            <a href="{{ route('admin.pegawai.index', ['tab' => 'nonaktif', 'search' => request('search'), 'jenis_pegawai' => request('jenis_pegawai')]) }}" 
               class="shrink-0 border-b-2 px-1 py-4 text-sm font-semibold {{ $tab === 'nonaktif' ? 'border-primary-500 text-primary-600' : 'border-transparent text-surface-500 hover:border-surface-300 hover:text-surface-700' }}">
                Nonaktif (Arsip)
                <span class="ml-2 rounded-full px-2 py-0.5 text-xs font-semibold {{ $tab === 'nonaktif' ? 'bg-primary-100 text-primary-600' : 'bg-surface-100 text-surface-600' }}">{{ $countNonAktif }}</span>
            </a>
            <a href="{{ route('admin.pegawai.index', ['tab' => 'semua', 'search' => request('search'), 'jenis_pegawai' => request('jenis_pegawai')]) }}" 
               class="shrink-0 border-b-2 px-1 py-4 text-sm font-semibold {{ $tab === 'semua' ? 'border-primary-500 text-primary-600' : 'border-transparent text-surface-500 hover:border-surface-300 hover:text-surface-700' }}">
                Semua
            </a>
        </nav>
    </div>

    {{-- Search & Filter Bar --}}
    <div class="p-4 border-b border-surface-100 bg-surface-50">
        <form action="{{ route('admin.pegawai.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="flex-1 relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama, NIP, atau NUPTK..." 
                       class="w-full pl-10 pr-4 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors">
            </div>
            <div class="sm:w-48">
                <select name="jenis_pegawai" class="w-full px-4 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" onchange="this.form.submit()">
                    <option value="">Semua Jenis</option>
                    <option value="GURU" {{ request('jenis_pegawai') === 'GURU' ? 'selected' : '' }}>Guru / Ustadz</option>
                    <option value="PENGASUH" {{ request('jenis_pegawai') === 'PENGASUH' ? 'selected' : '' }}>Pengasuh</option>
                    <option value="STAFF_ADMIN" {{ request('jenis_pegawai') === 'STAFF_ADMIN' ? 'selected' : '' }}>Staff / Admin</option>
                    <option value="LAINNYA" {{ request('jenis_pegawai') === 'LAINNYA' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            <button type="submit" class="btn-secondary px-4 py-2 hidden sm:block">Filter</button>
            @if(request()->anyFilled(['search', 'jenis_pegawai']))
                <a href="{{ route('admin.pegawai.index', ['tab' => $tab]) }}" class="btn-secondary px-4 py-2 text-danger-600 border-danger-200 hover:bg-danger-50 flex items-center justify-center">
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
                    <th class="px-6 py-4 font-semibold">Identitas Pegawai</th>
                    <th class="px-6 py-4 font-semibold">Tugas Pokok</th>
                    <th class="px-6 py-4 font-semibold">NIP / NUPTK</th>
                    <th class="px-6 py-4 font-semibold">Status Pegawai</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100 text-surface-700">
                @forelse($pegawais as $pegawai)
                <tr class="hover:bg-surface-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-surface-900 text-base">{{ $pegawai->orang->nama_lengkap }}</div>
                        <div class="text-xs text-primary-600 font-mono font-medium mt-0.5">NIUP: {{ $pegawai->orang->niup }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <x-badge variant="info" class="mb-1">{{ str_replace('_', ' ', $pegawai->jenis_pegawai) }}</x-badge>
                        <div class="text-xs text-surface-600">{{ $pegawai->jabatan ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-surface-900">{{ $pegawai->nip ?? '-' }}</div>
                        <div class="text-xs text-surface-500 mt-0.5">{{ $pegawai->nuptk ?? 'Tanpa NUPTK' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div>{{ $pegawai->status_kepegawaian }}</div>
                        <div class="text-xs mt-0.5">
                            @if($pegawai->is_active)
                                <span class="text-success-600 font-medium">Aktif Bekerja</span>
                            @else
                                <span class="text-danger-600 font-medium">Nonaktif</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.pegawai.show', $pegawai) }}" class="inline-flex text-surface-400 hover:text-primary-600 p-2 rounded-lg hover:bg-primary-50 transition-colors" title="Lihat Riwayat & Profil">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </a>
                        <a href="{{ route('admin.pegawai.edit', $pegawai) }}" class="inline-flex text-primary-600 hover:text-primary-700 p-2 rounded-lg hover:bg-primary-50 transition-colors" title="Edit Data Pegawai">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-surface-500">
                        <div class="flex flex-col items-center justify-center">
                            <i data-lucide="briefcase" class="w-12 h-12 text-surface-300 mb-3"></i>
                            <p class="font-medium text-surface-900 mb-1">Belum Ada Pegawai</p>
                            <p class="text-sm">Tidak ada data pegawai yang terdaftar atau ditemukan.</p>
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
</x-card>
@endsection
