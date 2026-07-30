@extends('layouts.app')

@section('title', 'Berita & Kegiatan')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Berita & Kegiatan</h1>
        <p class="text-sm text-surface-500 mt-1">Kelola artikel berita dan kegiatan yang tampil di website publik.</p>
    </div>
    <a href="{{ route('admin.berita.create') }}" class="btn-primary flex items-center gap-2">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Tulis Berita Baru</span>
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
                    <th class="text-left py-3 px-4 font-semibold text-surface-600">Judul</th>
                    <th class="text-left py-3 px-4 font-semibold text-surface-600 w-32">Penulis</th>
                    <th class="text-center py-3 px-4 font-semibold text-surface-600 w-24">Status</th>
                    <th class="text-center py-3 px-4 font-semibold text-surface-600 w-20">Views</th>
                    <th class="text-left py-3 px-4 font-semibold text-surface-600 w-32">Tanggal</th>
                    <th class="text-center py-3 px-4 font-semibold text-surface-600 w-28">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100">
                @forelse($beritas as $berita)
                <tr class="hover:bg-surface-50/50 transition-colors">
                    <td class="py-3 px-4 text-surface-400">{{ $loop->iteration + ($beritas->currentPage() - 1) * $beritas->perPage() }}</td>
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-3">
                            @if($berita->gambar_cover)
                                <img src="{{ Storage::url($berita->gambar_cover) }}" alt="" class="w-14 h-10 rounded-lg object-cover flex-shrink-0 border border-surface-200">
                            @else
                                <div class="w-14 h-10 rounded-lg bg-surface-100 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="image" class="w-4 h-4 text-surface-400"></i>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="font-semibold text-surface-900 truncate max-w-xs">{{ $berita->judul }}</p>
                                <p class="text-xs text-surface-400 truncate max-w-xs">{{ $berita->ringkasan ?? Str::limit(strip_tags($berita->konten), 60) }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-4 text-surface-600">{{ $berita->penulis->name ?? '-' }}</td>
                    <td class="py-3 px-4 text-center">
                        @if($berita->is_published)
                            <x-badge variant="success">Publish</x-badge>
                        @else
                            <x-badge variant="warning">Draft</x-badge>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-center text-surface-500">{{ number_format($berita->view_count) }}</td>
                    <td class="py-3 px-4 text-surface-500">{{ $berita->created_at->format('d M Y') }}</td>
                    <td class="py-3 px-4">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('admin.berita.edit', $berita) }}" class="p-2 text-surface-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors" title="Edit">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('admin.berita.destroy', $berita) }}" method="POST" onsubmit="return confirm('Hapus berita ini?')">
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
                        <i data-lucide="newspaper" class="w-10 h-10 mx-auto mb-3 text-surface-300"></i>
                        <p class="font-medium text-surface-500">Belum ada berita</p>
                        <p class="text-sm mt-1">Tulis berita pertama Anda dengan tombol di atas.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($beritas->hasPages())
        <div class="mt-4 pt-4 border-t border-surface-100">
            {{ $beritas->links() }}
        </div>
    @endif
</x-card>
@endsection
