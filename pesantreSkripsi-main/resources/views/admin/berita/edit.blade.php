@extends('layouts.app')

@section('title', 'Edit Berita')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm text-surface-500 mb-2">
            <a href="{{ route('admin.berita.index') }}" class="hover:text-primary-600 transition-colors">Berita & Kegiatan</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-surface-900 font-medium">Edit</span>
        </div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Edit: {{ Str::limit($berita->judul, 40) }}</h1>
    </div>
    <a href="{{ route('admin.berita.index') }}" class="btn-secondary flex items-center gap-2">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Kembali</span>
    </a>
</div>
@endsection

@section('content')
<form action="{{ route('admin.berita.update', $berita) }}" method="POST" enctype="multipart/form-data" class="max-w-4xl mx-auto space-y-6">
    @csrf
    @method('PUT')

    @if($errors->any())
        <div class="bg-danger-50 text-danger-700 p-4 rounded-xl border border-danger-200">
            <ul class="list-disc pl-5 space-y-1 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-card title="Konten Berita">
        <div class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-surface-700 mb-1">Judul Berita <span class="text-danger-500">*</span></label>
                <input type="text" name="judul" value="{{ old('judul', $berita->judul) }}" required
                       class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-surface-700 mb-1">Ringkasan <span class="text-surface-400 font-normal">(Opsional)</span></label>
                <textarea name="ringkasan" rows="2"
                          class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">{{ old('ringkasan', $berita->ringkasan) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-surface-700 mb-1">Konten Lengkap <span class="text-danger-500">*</span></label>
                <textarea name="konten" rows="12" required
                          class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">{{ old('konten', $berita->konten) }}</textarea>
            </div>
        </div>
    </x-card>

    <x-card title="Gambar & Pengaturan">
        <div class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-surface-700 mb-1">Gambar Cover</label>
                @if($berita->gambar_cover)
                    <div class="mb-3 flex items-center gap-4">
                        <img src="{{ Storage::url($berita->gambar_cover) }}" alt="Cover saat ini" class="w-32 h-20 object-cover rounded-lg border border-surface-200">
                        <span class="text-xs text-surface-400">Cover saat ini</span>
                    </div>
                @endif
                <input type="file" name="gambar_cover" accept="image/*"
                       class="w-full text-sm text-surface-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
                <p class="text-xs text-surface-400 mt-1">Upload baru untuk mengganti gambar cover. Kosongkan jika tidak ingin mengubah.</p>
            </div>

            <div class="flex items-center gap-3 p-4 bg-surface-50 rounded-xl border border-surface-200">
                <input type="checkbox" name="is_published" value="1" id="is_published" {{ $berita->is_published ? 'checked' : '' }}
                       class="w-4 h-4 text-primary-600 border-surface-300 rounded focus:ring-primary-500">
                <label for="is_published" class="text-sm font-medium text-surface-700">
                    Publikasikan
                    <span class="block text-xs text-surface-400 font-normal mt-0.5">Berita yang tidak dipublikasikan tidak akan tampil di website.</span>
                </label>
            </div>
        </div>
    </x-card>

    <div class="flex justify-end gap-3">
        <a href="{{ route('admin.berita.index') }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary flex items-center gap-2">
            <i data-lucide="save" class="w-4 h-4"></i>
            Perbarui Berita
        </button>
    </div>
</form>
@endsection
