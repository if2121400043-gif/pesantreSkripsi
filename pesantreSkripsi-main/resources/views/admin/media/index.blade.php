@extends('layouts.app')

@section('title', 'Galeri Media')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Galeri Media</h1>
        <p class="text-sm text-surface-500 mt-1">Kelola foto dan video YouTube untuk galeri website.</p>
    </div>
    <a href="{{ route('admin.media.create') }}" class="btn-primary flex items-center gap-2">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Tambah Media Baru</span>
    </a>
</div>
@endsection

@section('content')
<x-card>
    @if(session('success'))
        <div class="bg-success-50 text-success-700 p-3 rounded-xl border border-success-200 text-sm font-medium mb-4 flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4"></i> {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-surface-200">
                    <th class="text-left py-3 px-4 font-semibold text-surface-600 w-12">#</th>
                    <th class="text-left py-3 px-4 font-semibold text-surface-600">Preview</th>
                    <th class="text-left py-3 px-4 font-semibold text-surface-600">Judul & Kategori</th>
                    <th class="text-center py-3 px-4 font-semibold text-surface-600 w-24">Tipe</th>
                    <th class="text-center py-3 px-4 font-semibold text-surface-600 w-24">Status</th>
                    <th class="text-left py-3 px-4 font-semibold text-surface-600 w-32">Tanggal</th>
                    <th class="text-center py-3 px-4 font-semibold text-surface-600 w-28">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100">
                @forelse($medias as $media)
                <tr class="hover:bg-surface-50/50 transition-colors">
                    <td class="py-3 px-4 text-surface-400">{{ $loop->iteration + ($medias->currentPage() - 1) * $medias->perPage() }}</td>
                    <td class="py-3 px-4">
                        @if($media->tipe === 'IMAGE')
                            <img src="{{ $media->media_url }}" alt="" class="w-20 h-14 rounded-lg object-cover border border-surface-200 shadow-sm">
                        @else
                            <div class="relative w-20 h-14 rounded-lg overflow-hidden border border-surface-200 shadow-sm bg-surface-900">
                                <img src="{{ $media->youtube_thumbnail }}" alt="" class="w-full h-full object-cover opacity-60">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <i data-lucide="play-circle" class="w-6 h-6 text-white opacity-90"></i>
                                </div>
                            </div>
                        @endif
                    </td>
                    <td class="py-3 px-4">
                        <div class="min-w-0">
                            <p class="font-semibold text-surface-900 truncate max-w-xs">{{ $media->judul }}</p>
                            <p class="text-xs text-primary-600 font-medium">{{ $media->kategori ?? 'Tanpa Kategori' }}</p>
                        </div>
                    </td>
                    <td class="py-3 px-4 text-center">
                        @if($media->tipe === 'IMAGE')
                            <x-badge variant="info">Foto</x-badge>
                        @else
                            <x-badge variant="danger">Video</x-badge>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-center">
                        @if($media->is_active)
                            <x-badge variant="success">Aktif</x-badge>
                        @else
                            <x-badge variant="warning">Nonaktif</x-badge>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-surface-500 text-xs">{{ $media->created_at->format('d/m/Y H:i') }}</td>
                    <td class="py-3 px-4">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('admin.media.edit', $media) }}" class="p-2 text-surface-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors" title="Edit">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('admin.media.destroy', $media) }}" method="POST" onsubmit="return confirm('Hapus media ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-surface-400 hover:text-danger-600 hover:bg-danger-50 rounded-lg transition-colors" title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-12 text-center text-surface-400">
                        <i data-lucide="image" class="w-10 h-10 mx-auto mb-3 text-surface-300"></i>
                        <p class="font-medium text-surface-500">Belum ada media</p>
                        <p class="text-sm mt-1">Unggah foto atau tambahkan video pertama Anda.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($medias->hasPages())
        <div class="mt-4 pt-4 border-t border-surface-100">
            {{ $medias->links() }}
        </div>
    @endif
</x-card>
@endsection
