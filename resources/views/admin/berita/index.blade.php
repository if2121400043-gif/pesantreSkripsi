@extends('layouts.app')

@section('title', 'Berita & Pengumuman')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Berita & Pengumuman</h1>
        <p class="text-sm text-surface-500 mt-1">Kelola artikel berita, kegiatan, dan pengumuman yang tampil di website.</p>
    </div>
    <a href="{{ route('admin.berita.create') }}" class="btn-primary flex items-center gap-2">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Tulis Baru</span>
    </a>
</div>
@endsection

@section('content')

{{-- Statistik Ringkasan --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
    <a href="{{ route('admin.berita.index') }}" class="bg-white rounded-xl border {{ !request('tipe') && !request('status') ? 'border-primary-300 ring-2 ring-primary-100' : 'border-surface-200' }} p-3.5 text-center hover:border-primary-300 transition-colors group">
        <p class="text-2xl font-bold text-surface-900 group-hover:text-primary-600">{{ $stats['total'] }}</p>
        <p class="text-xs text-surface-500 font-medium mt-0.5">Total</p>
    </a>
    <a href="{{ route('admin.berita.index', ['tipe' => 'berita']) }}" class="bg-white rounded-xl border {{ request('tipe') === 'berita' ? 'border-info-300 ring-2 ring-info-100' : 'border-surface-200' }} p-3.5 text-center hover:border-info-300 transition-colors group">
        <p class="text-2xl font-bold text-info-600">{{ $stats['berita'] }}</p>
        <p class="text-xs text-surface-500 font-medium mt-0.5">Berita</p>
    </a>
    <a href="{{ route('admin.berita.index', ['tipe' => 'pengumuman']) }}" class="bg-white rounded-xl border {{ request('tipe') === 'pengumuman' ? 'border-warning-300 ring-2 ring-warning-100' : 'border-surface-200' }} p-3.5 text-center hover:border-warning-300 transition-colors group">
        <p class="text-2xl font-bold text-warning-600">{{ $stats['pengumuman'] }}</p>
        <p class="text-xs text-surface-500 font-medium mt-0.5">Pengumuman</p>
    </a>
    <a href="{{ route('admin.berita.index', ['status' => 'published']) }}" class="bg-white rounded-xl border {{ request('status') === 'published' ? 'border-success-300 ring-2 ring-success-100' : 'border-surface-200' }} p-3.5 text-center hover:border-success-300 transition-colors group">
        <p class="text-2xl font-bold text-success-600">{{ $stats['published'] }}</p>
        <p class="text-xs text-surface-500 font-medium mt-0.5">Published</p>
    </a>
    <a href="{{ route('admin.berita.index', ['status' => 'draft']) }}" class="bg-white rounded-xl border {{ request('status') === 'draft' ? 'border-surface-400 ring-2 ring-surface-200' : 'border-surface-200' }} p-3.5 text-center hover:border-surface-400 transition-colors group">
        <p class="text-2xl font-bold text-surface-600">{{ $stats['draft'] }}</p>
        <p class="text-xs text-surface-500 font-medium mt-0.5">Draft</p>
    </a>
    <a href="{{ route('admin.berita.index', ['status' => 'pinned']) }}" class="bg-white rounded-xl border {{ request('status') === 'pinned' ? 'border-accent-300 ring-2 ring-accent-100' : 'border-surface-200' }} p-3.5 text-center hover:border-accent-300 transition-colors group">
        <p class="text-2xl font-bold text-accent-600">{{ $stats['pinned'] }}</p>
        <p class="text-xs text-surface-500 font-medium mt-0.5">Disematkan</p>
    </a>
</div>

<x-card>
    @if(session('success'))
        <div class="bg-success-50 text-success-700 p-3 rounded-xl border border-success-200 text-sm font-medium mb-4 flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Filter & Pencarian --}}
    <form method="GET" action="{{ route('admin.berita.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 mb-5">
        <div class="relative flex-grow">
            <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-surface-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Cari judul berita atau pengumuman..."
                   class="w-full pl-9 pr-4 py-2 rounded-lg border border-surface-300 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
        </div>
        <select name="tipe" class="rounded-lg border border-surface-300 text-sm py-2 px-3 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
            <option value="">Semua Tipe</option>
            <option value="berita" {{ request('tipe') === 'berita' ? 'selected' : '' }}>📰 Berita</option>
            <option value="pengumuman" {{ request('tipe') === 'pengumuman' ? 'selected' : '' }}>📋 Pengumuman</option>
        </select>
        <select name="status" class="rounded-lg border border-surface-300 text-sm py-2 px-3 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
            <option value="">Semua Status</option>
            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>✅ Published</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>📝 Draft</option>
            <option value="pinned" {{ request('status') === 'pinned' ? 'selected' : '' }}>📌 Disematkan</option>
        </select>
        <div class="flex gap-2">
            <button type="submit" class="btn-primary py-2 px-4 text-sm">Filter</button>
            @if(request()->hasAny(['search', 'tipe', 'status']))
                <a href="{{ route('admin.berita.index') }}" class="btn-secondary py-2 px-4 text-sm">Reset</a>
            @endif
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-surface-200">
                    <th class="text-left py-3 px-4 font-semibold text-surface-600 w-12">#</th>
                    <th class="text-left py-3 px-4 font-semibold text-surface-600">Judul</th>
                    <th class="text-center py-3 px-4 font-semibold text-surface-600 w-28">Tipe</th>
                    <th class="text-left py-3 px-4 font-semibold text-surface-600 w-28">Penulis</th>
                    <th class="text-center py-3 px-4 font-semibold text-surface-600 w-24">Status</th>
                    <th class="text-center py-3 px-4 font-semibold text-surface-600 w-20">Views</th>
                    <th class="text-left py-3 px-4 font-semibold text-surface-600 w-28">Tanggal</th>
                    <th class="text-center py-3 px-4 font-semibold text-surface-600 w-36">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100">
                @forelse($beritas as $berita)
                <tr class="hover:bg-surface-50/50 transition-colors {{ $berita->is_pinned ? 'bg-accent-50/30' : '' }}">
                    <td class="py-3 px-4 text-surface-400">
                        @if($berita->is_pinned)
                            <i data-lucide="pin" class="w-4 h-4 text-accent-500" title="Disematkan"></i>
                        @else
                            {{ $loop->iteration + ($beritas->currentPage() - 1) * $beritas->perPage() }}
                        @endif
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-3">
                            @if($berita->gambar_cover)
                                <img src="{{ Storage::url($berita->gambar_cover) }}" alt="" class="w-14 h-10 rounded-lg object-cover flex-shrink-0 border border-surface-200">
                            @else
                                <div class="w-14 h-10 rounded-lg bg-surface-100 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="{{ $berita->tipe === 'pengumuman' ? 'megaphone' : 'image' }}" class="w-4 h-4 text-surface-400"></i>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="font-semibold text-surface-900 truncate max-w-xs">{{ $berita->judul }}</p>
                                <p class="text-xs text-surface-400 truncate max-w-xs">{{ $berita->ringkasan ?? Str::limit(strip_tags($berita->konten), 60) }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-4 text-center">
                        @if($berita->tipe === 'pengumuman')
                            <x-badge variant="warning">📋 Pengumuman</x-badge>
                        @else
                            <x-badge variant="info">📰 Berita</x-badge>
                        @endif
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
                        <div class="flex items-center justify-center gap-0.5">
                            {{-- Toggle Pin --}}
                            <form action="{{ route('admin.berita.toggle-pin', $berita) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="p-2 {{ $berita->is_pinned ? 'text-accent-500 bg-accent-50' : 'text-surface-400 hover:text-accent-600 hover:bg-accent-50' }} rounded-lg transition-colors" title="{{ $berita->is_pinned ? 'Lepas Sematan' : 'Sematkan' }}">
                                    <i data-lucide="pin" class="w-4 h-4"></i>
                                </button>
                            </form>
                            {{-- Toggle Publish --}}
                            <form action="{{ route('admin.berita.toggle-publish', $berita) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="p-2 {{ $berita->is_published ? 'text-success-500 bg-success-50' : 'text-surface-400 hover:text-success-600 hover:bg-success-50' }} rounded-lg transition-colors" title="{{ $berita->is_published ? 'Jadikan Draft' : 'Publikasikan' }}">
                                    <i data-lucide="{{ $berita->is_published ? 'eye' : 'eye-off' }}" class="w-4 h-4"></i>
                                </button>
                            </form>
                            {{-- Edit --}}
                            <a href="{{ route('admin.berita.edit', $berita) }}" class="p-2 text-surface-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors" title="Edit">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                            {{-- Delete --}}
                            <form action="{{ route('admin.berita.destroy', $berita) }}" method="POST" onsubmit="return confirm('Hapus item ini?')">
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
                    <td colspan="8" class="py-12 text-center text-surface-400">
                        <i data-lucide="newspaper" class="w-10 h-10 mx-auto mb-3 text-surface-300"></i>
                        <p class="font-medium text-surface-500">Belum ada berita atau pengumuman</p>
                        <p class="text-sm mt-1">Tulis yang pertama dengan tombol di atas.</p>
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
