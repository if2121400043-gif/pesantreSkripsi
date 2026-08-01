@extends('layouts.app')

@section('title', 'Detail Kelas: ' . $rombel->nama)

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm text-surface-500 mb-2">
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
<div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
    
    {{-- Kolom Kiri: Informasi Kelas --}}
    <div class="xl:col-span-1 space-y-6">
        <x-card :padding="false">
            <div class="bg-primary-900 h-24 rounded-t-xl relative flex items-center justify-center">
                <div class="absolute -bottom-8 bg-white p-2 rounded-xl shadow-sm border border-surface-100">
                    <div class="w-16 h-16 bg-primary-100 text-primary-700 rounded-lg flex items-center justify-center">
                        <i data-lucide="book-open" class="w-8 h-8"></i>
                    </div>
                </div>
            </div>
            
            <div class="pt-12 p-6 text-center border-b border-surface-100">
                <h2 class="text-xl font-bold text-surface-900">{{ $rombel->nama }}</h2>
                <p class="text-surface-500 text-sm mt-1">{{ $rombel->lembaga->nama }}</p>
            </div>
            
            <div class="p-6 space-y-4 text-sm">
                <div>
                    <p class="text-surface-500 font-medium mb-1 text-xs uppercase tracking-wider">Tahun Pelajaran</p>
                    <p class="font-semibold text-surface-900">{{ $rombel->tahunPelajaran->nama }}</p>
                </div>
                <div>
                    <p class="text-surface-500 font-medium mb-1 text-xs uppercase tracking-wider">Tingkat</p>
                    <p class="font-semibold text-surface-900">{{ $rombel->tingkat ?? 'Belum Ditentukan' }}</p>
                </div>
                <div>
                    <p class="text-surface-500 font-medium mb-1 text-xs uppercase tracking-wider">Wali Kelas</p>
                    <div class="flex items-center gap-2 mt-1">
                        <div class="w-6 h-6 rounded-full bg-surface-200 flex items-center justify-center text-xs font-bold text-surface-600">
                            {{ $rombel->waliKelas ? substr($rombel->waliKelas->orang->nama_lengkap, 0, 1) : '?' }}
                        </div>
                        <span class="font-semibold text-surface-900">{{ $rombel->waliKelas->orang->nama_lengkap ?? 'Belum Ditentukan' }}</span>
                    </div>
                </div>
                
                @php
                    $activePeserta = $rombel->riwayatPeserta->where('status', 'AKTIF');
                    $activeCount = $activePeserta->count();
                    $displayPeserta = request('show_all') == '1' ? $rombel->riwayatPeserta : $activePeserta;
                @endphp
                <div class="pt-4 border-t border-surface-100">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-surface-600 font-medium">Kapasitas Active</span>
                        <span class="font-bold {{ $activeCount >= $rombel->kapasitas ? 'text-danger-600' : 'text-surface-900' }}">
                            {{ $activeCount }} / {{ $rombel->kapasitas }} Siswa
                        </span>
                    </div>
                    <div class="w-full bg-surface-200 rounded-full h-2">
                        @php
                            $percentage = min(($activeCount / max($rombel->kapasitas, 1)) * 100, 100);
                            $color = $percentage >= 100 ? 'bg-danger-500' : ($percentage >= 80 ? 'bg-warning-500' : 'bg-success-500');
                        @endphp
                        <div class="{{ $color }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Kolom Kanan: Daftar Siswa --}}
    <div class="xl:col-span-3 space-y-6">
        <x-card :padding="false">
            <div class="p-4 border-b border-surface-100 flex flex-col sm:flex-row justify-between items-center gap-4 bg-surface-50 rounded-t-xl">
                <div class="flex items-center gap-3">
                    <h3 class="font-bold text-surface-900 flex items-center gap-2">
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
                            <th class="px-6 py-3 font-semibold w-12 text-center">No</th>
                            <th class="px-6 py-3 font-semibold">Nama Siswa</th>
                            <th class="px-6 py-3 font-semibold">NIS / NISN</th>
                            <th class="px-6 py-3 font-semibold">L/P</th>
                            <th class="px-6 py-3 font-semibold">Status Penempatan</th>
                            <th class="px-6 py-3 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100 text-surface-700">
                        @forelse($displayPeserta as $index => $riwayat)
                            <tr class="hover:bg-surface-50 transition-colors">
                                <td class="px-6 py-3 text-center text-surface-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-3">
                                    <div class="font-medium text-surface-900">{{ $riwayat->pesertaDidik->orang->nama_lengkap }}</div>
                                    <div class="text-xs text-primary-600 font-mono">{{ $riwayat->pesertaDidik->orang->niup }}</div>
                                </td>
                                <td class="px-6 py-3">
                                    <div>{{ $riwayat->pesertaDidik->nis ?? '-' }}</div>
                                    <div class="text-xs text-surface-500">{{ $riwayat->pesertaDidik->nisn ?? 'Tanpa NISN' }}</div>
                                </td>
                                <td class="px-6 py-3 text-center">
                                    {{ $riwayat->pesertaDidik->orang->jenis_kelamin }}
                                </td>
                                <td class="px-6 py-3">
                                    @if($riwayat->status === 'AKTIF')
                                        <x-badge variant="success" dot class="text-xs">Aktif di Kelas</x-badge>
                                    @elseif($riwayat->status === 'PINDAH')
                                        <x-badge variant="warning" dot class="text-xs">Pindah Kelas</x-badge>
                                    @else
                                        <x-badge variant="surface" dot class="text-xs">Selesai/Lulus</x-badge>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <a href="{{ route('admin.peserta-didik.show', $riwayat->pesertaDidik) }}" class="inline-flex text-surface-400 hover:text-primary-600 p-1.5 rounded hover:bg-primary-50 transition-colors" title="Lihat Profil Akademik">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    {{-- Placeholder for remove from class --}}
                                    <form action="{{ route('admin.penempatan.remove') }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin mengeluarkan siswa ini dari kelas?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="riwayat_id" value="{{ $riwayat->id }}">
                                        <input type="hidden" name="hard_delete" value="1">
                                        <button type="submit" class="inline-flex text-danger-400 hover:text-danger-600 p-1.5 rounded hover:bg-danger-50 transition-colors" title="Keluarkan dari Kelas">
                                            <i data-lucide="user-minus" class="w-4 h-4"></i>
                                        </button>
                                    </form>
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
                                    {{-- Placeholder --}}
                                    <a href="{{ route('admin.penempatan.index', ['lembaga_id' => $rombel->lembaga_id, 'tahun_pelajaran_id' => $rombel->tahun_pelajaran_id]) }}" class="btn-secondary text-xs mt-3 inline-block">Masukan Siswa ke Kelas</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</div>
@endsection
