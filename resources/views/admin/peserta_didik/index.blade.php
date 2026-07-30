@extends('layouts.app')

@section('title', 'Data Peserta Didik (Santri)')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Data Peserta Didik</h1>
        <p class="text-sm text-surface-500 mt-1">Manajemen profil akademik santri dan siswa pesantren.</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.peserta-didik.create') }}" class="btn-primary flex items-center gap-2">
            <i data-lucide="graduation-cap" class="w-4 h-4"></i>
            <span>Daftarkan Santri</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<x-card :padding="false">
    {{-- Tabs --}}
    <div class="border-b border-surface-200">
        <nav class="-mb-px flex space-x-6 px-6" aria-label="Tabs">
            <a href="{{ route('admin.peserta-didik.index', ['tab' => 'aktif', 'search' => request('search'), 'lembaga_id' => request('lembaga_id'), 'angkatan' => request('angkatan')]) }}" 
               class="shrink-0 border-b-2 px-1 py-4 text-sm font-semibold {{ $tab === 'aktif' ? 'border-primary-500 text-primary-600' : 'border-transparent text-surface-500 hover:border-surface-300 hover:text-surface-700' }}">
                Santri Aktif
                <span class="ml-2 rounded-full px-2 py-0.5 text-xs font-semibold {{ $tab === 'aktif' ? 'bg-primary-100 text-primary-600' : 'bg-surface-100 text-surface-600' }}">{{ $countAktif }}</span>
            </a>
            <a href="{{ route('admin.peserta-didik.index', ['tab' => 'alumni', 'search' => request('search'), 'lembaga_id' => request('lembaga_id'), 'angkatan' => request('angkatan')]) }}" 
               class="shrink-0 border-b-2 px-1 py-4 text-sm font-semibold {{ $tab === 'alumni' ? 'border-primary-500 text-primary-600' : 'border-transparent text-surface-500 hover:border-surface-300 hover:text-surface-700' }}">
                Alumni
                <span class="ml-2 rounded-full px-2 py-0.5 text-xs font-semibold {{ $tab === 'alumni' ? 'bg-primary-100 text-primary-600' : 'bg-surface-100 text-surface-600' }}">{{ $countAlumni }}</span>
            </a>
            <a href="{{ route('admin.peserta-didik.index', ['tab' => 'keluar', 'search' => request('search'), 'lembaga_id' => request('lembaga_id'), 'angkatan' => request('angkatan')]) }}" 
               class="shrink-0 border-b-2 px-1 py-4 text-sm font-semibold {{ $tab === 'keluar' ? 'border-primary-500 text-primary-600' : 'border-transparent text-surface-500 hover:border-surface-300 hover:text-surface-700' }}">
                Keluar/Mutasi
                <span class="ml-2 rounded-full px-2 py-0.5 text-xs font-semibold {{ $tab === 'keluar' ? 'bg-primary-100 text-primary-600' : 'bg-surface-100 text-surface-600' }}">{{ $countKeluar }}</span>
            </a>
            <a href="{{ route('admin.peserta-didik.index', ['tab' => 'semua', 'search' => request('search'), 'lembaga_id' => request('lembaga_id'), 'angkatan' => request('angkatan')]) }}" 
               class="shrink-0 border-b-2 px-1 py-4 text-sm font-semibold {{ $tab === 'semua' ? 'border-primary-500 text-primary-600' : 'border-transparent text-surface-500 hover:border-surface-300 hover:text-surface-700' }}">
                Semua
            </a>
        </nav>
    </div>

    {{-- Search & Filter Bar --}}
    <div class="p-4 border-b border-surface-100 bg-surface-50">
        <form action="{{ route('admin.peserta-didik.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="flex-1 relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama, NIUP, NIS, atau NISN..." 
                       class="w-full pl-10 pr-4 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors">
            </div>
            <div class="sm:w-48">
                <select name="lembaga_id" class="w-full px-4 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" onchange="this.form.submit()">
                    <option value="">Semua Lembaga</option>
                    @foreach($lembagas as $l)
                        <option value="{{ $l->id }}" {{ request('lembaga_id') == $l->id ? 'selected' : '' }}>{{ $l->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:w-48">
                <input type="number" name="angkatan" value="{{ request('angkatan') }}" placeholder="Tahun Angkatan..." 
                       class="w-full px-4 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors"
                       onchange="this.form.submit()">
            </div>
            @if(request()->anyFilled(['search', 'lembaga_id', 'angkatan']))
                <a href="{{ route('admin.peserta-didik.index', ['tab' => $tab]) }}" class="btn-secondary px-4 py-2 text-danger-600 border-danger-200 hover:bg-danger-50 flex items-center justify-center">
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
                    <th class="px-6 py-4 font-semibold">Identitas Santri</th>
                    <th class="px-6 py-4 font-semibold">NIS / NISN</th>
                    <th class="px-6 py-4 font-semibold">L/P</th>
                    <th class="px-6 py-4 font-semibold">Tgl Masuk</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100 text-surface-700">
                @forelse($pesertaDidiks as $pd)
                <tr class="hover:bg-surface-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-surface-900 text-base">{{ $pd->orang->nama_lengkap }}</div>
                        <div class="text-xs text-primary-600 font-mono font-medium mt-0.5">NIUP: {{ $pd->orang->niup }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-surface-900">{{ $pd->nis ?? '-' }}</div>
                        <div class="text-xs text-surface-500 mt-0.5">{{ $pd->nisn ?? 'Tanpa NISN' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        {{ $pd->orang->jenis_kelamin }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $pd->tanggal_masuk ? $pd->tanggal_masuk->format('d M Y') : '-' }}
                    </td>
                    <td class="px-6 py-4">
                        @if($pd->status === 'AKTIF')
                            <x-badge variant="success" dot>Aktif</x-badge>
                        @elseif($pd->status === 'LULUS')
                            <x-badge variant="info" dot>Lulus</x-badge>
                        @else
                            <x-badge variant="danger" dot>{{ str_replace('_', ' ', $pd->status) }}</x-badge>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.peserta-didik.show', $pd) }}" class="inline-flex text-surface-400 hover:text-primary-600 p-2 rounded-lg hover:bg-primary-50 transition-colors" title="Lihat Profil Akademik">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </a>
                        <a href="{{ route('admin.peserta-didik.edit', $pd) }}" class="inline-flex text-primary-600 hover:text-primary-700 p-2 rounded-lg hover:bg-primary-50 transition-colors" title="Edit Data Santri">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-surface-500">
                        <div class="flex flex-col items-center justify-center">
                            <i data-lucide="graduation-cap" class="w-12 h-12 text-surface-300 mb-3"></i>
                            <p class="font-medium text-surface-900 mb-1">Belum Ada Santri</p>
                            <p class="text-sm">Tidak ada data peserta didik yang terdaftar atau ditemukan.</p>
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
</x-card>
@endsection
