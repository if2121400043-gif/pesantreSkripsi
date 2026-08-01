@extends('layouts.app')

@section('title', 'Detail Kelas: ' . $rombel->nama)

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-surface-500 mb-1">
            <a href="{{ route('admin.rombel.index') }}" class="hover:text-primary-600 transition-colors">Data Rombel</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-surface-900 font-medium">Detail Kelas</span>
        </div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">
            {{ str_starts_with(strtolower($rombel->nama), 'kelas') ? $rombel->nama : (str_contains(strtoupper($rombel->nama), strtoupper($rombel->tingkat ?? '')) ? 'Kelas ' . $rombel->nama : 'Kelas ' . ($rombel->tingkat ? $rombel->tingkat . ' - ' : '') . $rombel->nama) }}
        </h1>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.rombel.index') }}" class="btn-secondary flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span class="hidden sm:inline">Kembali</span>
        </a>
        <a href="{{ route('admin.rombel.edit', $rombel) }}" class="btn-primary flex items-center gap-2">
            <i data-lucide="edit" class="w-4 h-4"></i>
            <span class="hidden sm:inline">Edit Kelas</span>
        </a>
    </div>
</div>
@endsection

@section('content')
@php
    $activePeserta = $rombel->riwayatPeserta->where('status', 'AKTIF');
    $activeCount = $activePeserta->count();
    $displayPeserta = request('show_all') == '1' ? $rombel->riwayatPeserta : $activePeserta;
    $percentage = min(($activeCount / max($rombel->kapasitas, 1)) * 100, 100);
    $color = $percentage >= 100 ? 'bg-danger-500' : ($percentage >= 80 ? 'bg-warning-500' : 'bg-success-500');
@endphp

{{-- Ringkasan Informasi Kelas (Top Grid Header) --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    {{-- Card 1: Identitas Kelas --}}
    <x-card class="bg-white border border-surface-200">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
                <i data-lucide="book-open" class="w-6 h-6"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-semibold text-primary-600 uppercase tracking-wider truncate">{{ $rombel->lembaga->singkatan ?? $rombel->lembaga->nama }}</p>
                <h3 class="text-lg font-bold text-surface-900 truncate">{{ $rombel->nama }}</h3>
                <div class="mt-1">
                    @if($rombel->gender_target === 'PUTRA')
                        <x-badge variant="info" size="sm">Putra Only</x-badge>
                    @elseif($rombel->gender_target === 'PUTRI')
                        <x-badge variant="warning" size="sm">Putri Only</x-badge>
                    @else
                        <x-badge variant="neutral" size="sm">Campur</x-badge>
                    @endif
                </div>
            </div>
        </div>
    </x-card>

    {{-- Card 2: Tahun Pelajaran & Tingkat --}}
    <x-card class="bg-white border border-surface-200">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-accent-50 text-accent-600 flex items-center justify-center shrink-0">
                <i data-lucide="graduation-cap" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-surface-500 uppercase tracking-wider">Tahun Pelajaran</p>
                <h4 class="text-base font-bold text-surface-900">{{ $rombel->tahunPelajaran->nama }}</h4>
                <p class="text-xs text-surface-500 mt-0.5">Tingkat: <span class="font-semibold text-surface-800">{{ $rombel->tingkat ?? '-' }}</span></p>
            </div>
        </div>
    </x-card>

    {{-- Card 3: Wali Kelas --}}
    <x-card class="bg-white border border-surface-200">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-info-50 text-info-600 flex items-center justify-center font-bold text-base shrink-0">
                {{ $rombel->waliKelas ? substr($rombel->waliKelas->orang->nama_lengkap, 0, 1) : '?' }}
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-medium text-surface-500 uppercase tracking-wider">Wali Kelas</p>
                <h4 class="text-sm font-bold text-surface-900 truncate" title="{{ $rombel->waliKelas->orang->nama_lengkap ?? 'Belum Ditentukan' }}">
                    {{ $rombel->waliKelas->orang->nama_lengkap ?? 'Belum Ditentukan' }}
                </h4>
                <p class="text-xs text-surface-500 mt-0.5">Pengampu Rombel</p>
            </div>
        </div>
    </x-card>

    {{-- Card 4: Kapasitas Siswa --}}
    <x-card class="bg-white border border-surface-200">
        <div class="flex flex-col justify-center h-full">
            <div class="flex justify-between items-center mb-1">
                <span class="text-xs font-medium text-surface-500 uppercase tracking-wider">Kapasitas Siswa</span>
                <span class="text-xs font-bold {{ $activeCount >= $rombel->kapasitas ? 'text-danger-600' : 'text-success-600' }}">
                    {{ round($percentage) }}%
                </span>
            </div>
            <p class="text-lg font-bold text-surface-900 mb-2">
                {{ $activeCount }} <span class="text-xs text-surface-500 font-normal">/ {{ $rombel->kapasitas }} Siswa Aktif</span>
            </p>
            <div class="w-full bg-surface-100 rounded-full h-2">
                <div class="{{ $color }} h-2 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
            </div>
        </div>
    </x-card>
</div>

{{-- Tabel Daftar Siswa (Full-Width 100%) --}}
<x-card :padding="false" class="bg-white border border-surface-200">
    <div class="p-4 border-b border-surface-100 flex flex-col sm:flex-row justify-between items-center gap-4 bg-surface-50 rounded-t-xl">
        <div class="flex items-center gap-3">
            <h3 class="font-bold text-surface-900 flex items-center gap-2 text-base">
                <i data-lucide="users" class="w-5 h-5 text-primary-500"></i>
                Daftar Siswa {{ request('show_all') == '1' ? 'Semua (Termasuk Alumni/Pindah)' : 'Aktif' }}
            </h3>
            @if($rombel->riwayatPeserta->where('status', '!=', 'AKTIF')->count() > 0)
                @if(request('show_all') == '1')
                    <a href="{{ route('admin.rombel.show', $rombel) }}" class="text-xs text-primary-600 hover:underline font-medium">Tampilkan Siswa Aktif Saja</a>
                @else
                    <a href="{{ route('admin.rombel.show', [$rombel, 'show_all' => 1]) }}" class="text-xs text-surface-500 hover:underline font-medium">Tampilkan Riwayat Pindah ({{ $rombel->riwayatPeserta->where('status', '!=', 'AKTIF')->count() }})</a>
                @endif
            @endif
        </div>
        <div class="flex gap-2 w-full sm:w-auto flex-wrap">
            @if($activeCount > 0)
                <form action="{{ route('admin.penempatan.empty-rombel') }}" method="POST" class="inline" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin mengeluarkan SELURUH santri ({{ $activeCount }} santri) dari kelas {{ $rombel->nama }}?')">
                    @csrf
                    <input type="hidden" name="rombel_id" value="{{ $rombel->id }}">
                    <button type="submit" class="btn-secondary text-danger-600 border-danger-200 hover:bg-danger-50 w-full sm:w-auto text-sm py-1.5 flex justify-center items-center gap-2">
                        <i data-lucide="user-x" class="w-4 h-4"></i> Kosongkan Kelas
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.penempatan.index', ['lembaga_id' => $rombel->lembaga_id, 'tahun_pelajaran_id' => $rombel->tahun_pelajaran_id]) }}" class="btn-primary w-full sm:w-auto text-sm py-1.5 flex justify-center items-center gap-2">
                <i data-lucide="user-plus" class="w-4 h-4"></i> Kelola Penempatan Siswa
            </a>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-surface-50 text-surface-600 border-b border-surface-100">
                <tr>
                    <th class="px-6 py-3.5 font-semibold w-12 text-center">No</th>
                    <th class="px-6 py-3.5 font-semibold">Nama Siswa</th>
                    <th class="px-6 py-3.5 font-semibold">NIS / NISN</th>
                    <th class="px-6 py-3.5 font-semibold text-center">L/P</th>
                    <th class="px-6 py-3.5 font-semibold">Status Penempatan</th>
                    <th class="px-6 py-3.5 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100 text-surface-700">
                @forelse($displayPeserta as $index => $riwayat)
                    <tr class="hover:bg-surface-50 transition-colors">
                        <td class="px-6 py-3.5 text-center text-surface-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-3.5">
                            <div class="font-semibold text-surface-900">{{ $riwayat->pesertaDidik->orang->nama_lengkap }}</div>
                            <div class="text-xs text-primary-600 font-mono">{{ $riwayat->pesertaDidik->orang->niup }}</div>
                        </td>
                        <td class="px-6 py-3.5">
                            <div class="font-medium text-surface-800">{{ $riwayat->pesertaDidik->nis ?? '-' }}</div>
                            <div class="text-xs text-surface-500">{{ $riwayat->pesertaDidik->nisn ?? 'Tanpa NISN' }}</div>
                        </td>
                        <td class="px-6 py-3.5 text-center font-medium">
                            {{ $riwayat->pesertaDidik->orang->jenis_kelamin }}
                        </td>
                        <td class="px-6 py-3.5">
                            @if($riwayat->status === 'AKTIF')
                                <x-badge variant="success" dot class="text-xs">Aktif di Kelas</x-badge>
                            @elseif($riwayat->status === 'PINDAH')
                                <x-badge variant="warning" dot class="text-xs">Pindah Kelas</x-badge>
                            @else
                                <x-badge variant="surface" dot class="text-xs">Selesai/Lulus</x-badge>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.peserta-didik.show', $riwayat->pesertaDidik) }}" class="inline-flex text-surface-400 hover:text-primary-600 p-1.5 rounded-lg hover:bg-primary-50 transition-colors" title="Lihat Profil Akademik">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('admin.penempatan.remove') }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin mengeluarkan siswa ini dari kelas?')">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="riwayat_id" value="{{ $riwayat->id }}">
                                    <input type="hidden" name="hard_delete" value="1">
                                    <button type="submit" class="inline-flex text-danger-400 hover:text-danger-600 p-1.5 rounded-lg hover:bg-danger-50 transition-colors" title="Keluarkan dari Kelas">
                                        <i data-lucide="user-minus" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-surface-500">
                            <div class="w-12 h-12 bg-surface-100 text-surface-400 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="users" class="w-6 h-6"></i>
                            </div>
                            <p class="font-medium text-surface-900 mb-1">Belum Ada Siswa</p>
                            <p class="text-sm">Kelas ini belum memiliki siswa terdaftar.</p>
                            <a href="{{ route('admin.penempatan.index', ['lembaga_id' => $rombel->lembaga_id, 'tahun_pelajaran_id' => $rombel->tahun_pelajaran_id]) }}" class="btn-secondary text-xs mt-3 inline-block">Masukan Siswa ke Kelas</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>
@endsection
