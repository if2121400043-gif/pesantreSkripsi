@extends('layouts.app')

@section('title', 'Jenis Presensi')

@section('page_header')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Jenis Presensi</h1>
            <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">Kelola jenis-jenis presensi pesantren.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.jenis-presensi.create') }}" class="btn-primary">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Tambah Jenis Presensi
            </a>
        </div>
    </div>
@endsection

@section('content')
    @if(session('success'))
        <div class="p-4 mb-4 rounded-lg bg-success-50 text-success-800 dark:bg-success-900/30 dark:text-success-400 border border-success-200 dark:border-success-800 flex items-start">
            <i data-lucide="check-circle" class="w-5 h-5 mr-3 shrink-0 mt-0.5"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800/50">
                        <th class="p-4 text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider w-16 text-center">No</th>
                        <th class="p-4 text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider">Nama</th>
                        <th class="p-4 text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider">Kode</th>
                        <th class="p-4 text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider">Target Gender</th>
                        <th class="p-4 text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider">Tipe Target</th>
                        <th class="p-4 text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider">Jam</th>
                        <th class="p-4 text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider">Status</th>
                        <th class="p-4 text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-200 dark:divide-surface-700">
                    @forelse($jenisPresensi as $jp)
                        <tr class="hover:bg-surface-50 dark:hover:bg-surface-800/50 transition-colors">
                            <td class="p-4 text-sm text-surface-900 dark:text-surface-100 text-center">{{ $loop->iteration }}</td>
                            <td class="p-4">
                                <div class="text-sm font-medium text-surface-900 dark:text-surface-100">{{ $jp->nama }}</div>
                                @if($jp->deskripsi)
                                    <div class="text-xs text-surface-500 dark:text-surface-400 mt-1">{{ \Illuminate\Support\Str::limit($jp->deskripsi, 50) }}</div>
                                @endif
                            </td>
                            <td class="p-4 text-sm text-surface-900 dark:text-surface-100 font-mono">{{ $jp->kode }}</td>
                            <td class="p-4">
                                @if($jp->target_gender === 'SEMUA')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-info-100 text-info-800 dark:bg-info-900/30 dark:text-info-400">SEMUA</span>
                                @elseif($jp->target_gender === 'PUTRA')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-400">PUTRA</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-warning-100 text-warning-800 dark:bg-warning-900/30 dark:text-warning-400">PUTRI</span>
                                @endif
                            </td>
                            <td class="p-4">
                                @if($jp->tipe_target === 'SEMUA_SANTRI')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-success-100 text-success-800 dark:bg-success-900/30 dark:text-success-400">SEMUA SANTRI</span>
                                @elseif($jp->tipe_target === 'PER_ROMBEL')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-info-100 text-info-800 dark:bg-info-900/30 dark:text-info-400">PER ROMBEL</span>
                                @elseif($jp->tipe_target === 'PER_ASRAMA')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-warning-100 text-warning-800 dark:bg-warning-900/30 dark:text-warning-400">PER ASRAMA</span>
                                @endif
                            </td>
                            <td class="p-4 text-sm text-surface-500 dark:text-surface-400">
                                @if($jp->jam_mulai || $jp->jam_selesai)
                                    {{ $jp->jam_mulai ? substr($jp->jam_mulai, 0, 5) : '-' }} s/d {{ $jp->jam_selesai ? substr($jp->jam_selesai, 0, 5) : '-' }}
                                @else
                                    <span class="italic text-surface-400">Tidak diatur</span>
                                @endif
                            </td>
                            <td class="p-4">
                                @if($jp->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-success-100 text-success-800 dark:bg-success-900/30 dark:text-success-400">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-danger-100 text-danger-800 dark:bg-danger-900/30 dark:text-danger-400">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.jenis-presensi.edit', $jp) }}" class="text-surface-400 hover:text-primary-600 transition-colors" title="Edit">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('admin.jenis-presensi.destroy', $jp) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jenis presensi ini? Data presensi terkait mungkin akan terpengaruh.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-surface-400 hover:text-danger-600 transition-colors" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-surface-500 dark:text-surface-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-surface-100 dark:bg-surface-800 p-3 rounded-full mb-3">
                                        <i data-lucide="list-checks" class="w-8 h-8 text-surface-400"></i>
                                    </div>
                                    <p class="text-surface-600 dark:text-surface-300 font-medium">Belum ada data Jenis Presensi</p>
                                    <p class="text-sm mt-1">Silakan tambahkan jenis presensi baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
@endsection
