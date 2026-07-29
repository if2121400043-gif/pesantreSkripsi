@extends('layouts.app')

@section('title', 'Edit Media')

@section('page_header')
<div class="flex items-center gap-4">
    <a href="{{ route('admin.media.index') }}" class="p-2 text-surface-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors">
        <i data-lucide="arrow-left" class="w-5 h-5"></i>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Edit Media</h1>
        <p class="text-sm text-surface-500 mt-1">Perbarui informasi foto atau video galeri.</p>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-3xl">
    <x-card>
        <form action="{{ route('admin.media.update', $media) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Judul -->
                <div class="md:col-span-2">
                    <label for="judul" class="block text-sm font-semibold text-surface-700 mb-2">Judul Media <span class="text-danger-500">*</span></label>
                    <input type="text" name="judul" id="judul" class="form-input @error('judul') border-danger-500 @enderror" value="{{ old('judul', $media->judul) }}" placeholder="Masukkan judul foto atau video..." required>
                    @error('judul') <p class="mt-1 text-xs text-danger-500">{{ $message }}</p> @enderror
                </div>

                <!-- Tipe Media -->
                <div>
                    <label for="tipe" class="block text-sm font-semibold text-surface-700 mb-2">Tipe Media <span class="text-danger-500">*</span></label>
                    <select name="tipe" id="tipe" class="form-input" onchange="toggleInputs(this.value)">
                        <option value="IMAGE" {{ old('tipe', $media->tipe) == 'IMAGE' ? 'selected' : '' }}>Foto (Unggah Lokal)</option>
                        <option value="VIDEO" {{ old('tipe', $media->tipe) == 'VIDEO' ? 'selected' : '' }}>Video (YouTube Link)</option>
                    </select>
                </div>

                <!-- Kategori -->
                <div>
                    <label for="kategori" class="block text-sm font-semibold text-surface-700 mb-2">Kategori</label>
                    <input type="text" name="kategori" id="kategori" class="form-input" value="{{ old('kategori', $media->kategori) }}" placeholder="Contoh: Fasilitas, Kegiatan, dll">
                </div>

                <!-- Preview Saat Ini -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-surface-700 mb-2">Konten Saat Ini:</label>
                    <div class="p-3 border border-surface-200 rounded-xl bg-surface-50 inline-block">
                        @if($media->tipe === 'IMAGE')
                            <img src="{{ $media->media_url }}" alt="" class="max-w-xs h-auto rounded-lg shadow-sm border border-surface-200">
                        @else
                            <div class="relative max-w-xs h-40 rounded-lg overflow-hidden border border-surface-200 shadow-sm bg-surface-900">
                                <img src="{{ $media->youtube_thumbnail }}" alt="" class="w-full h-full object-cover opacity-60">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <i data-lucide="play-circle" class="w-10 h-10 text-white opacity-90"></i>
                                </div>
                            </div>
                            <p class="text-[10px] text-surface-500 mt-2 truncate">{{ $media->url }}</p>
                        @endif
                    </div>
                </div>

                <!-- Input Khusus Image -->
                <div id="input-image" class="md:col-span-2 {{ old('tipe', $media->tipe) == 'VIDEO' ? 'hidden' : '' }}">
                    <label for="file_image" class="block text-sm font-semibold text-surface-700 mb-2">Ganti Foto (Opsional)</label>
                    <input type="file" name="file_image" id="file_image" class="form-input" accept="image/*">
                    <div class="mt-2 flex items-center gap-2 text-xs text-surface-500">
                        <i data-lucide="info" class="w-3.5 h-3.5 text-primary-500"></i>
                        <span>Ket: <strong>max: 1 mb</strong>. Kosongkan jika tidak ingin mengganti foto.</span>
                    </div>
                    @error('file_image') <p class="mt-1 text-xs text-danger-500">{{ $message }}</p> @enderror
                </div>

                <!-- Input Khusus Video -->
                <div id="input-video" class="md:col-span-2 {{ old('tipe', $media->tipe) != 'VIDEO' ? 'hidden' : '' }}">
                    <label for="url_video" class="block text-sm font-semibold text-surface-700 mb-2">URL Video YouTube <span class="text-danger-500">*</span></label>
                    <input type="url" name="url_video" id="url_video" class="form-input" value="{{ old('url_video', $media->tipe == 'VIDEO' ? $media->url : '') }}" placeholder="https://www.youtube.com/watch?v=...">
                    @error('url_video') <p class="mt-1 text-xs text-danger-500">{{ $message }}</p> @enderror
                </div>

                <!-- Deskripsi -->
                <div class="md:col-span-2">
                    <label for="deskripsi" class="block text-sm font-semibold text-surface-700 mb-2">Deskripsi (Opsional)</label>
                    <textarea name="deskripsi" id="deskripsi" rows="3" class="form-input" placeholder="Tulis keterangan singkat mengenai media ini...">{{ old('deskripsi', $media->deskripsi) }}</textarea>
                </div>

                <!-- Status Aktif -->
                <div class="md:col-span-2">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" name="is_active" class="sr-only peer" value="1" {{ old('is_active', $media->is_active) ? 'checked' : '' }}>
                            <div class="w-10 h-5 bg-surface-200 rounded-full peer peer-checked:bg-primary-600 transition-colors"></div>
                            <div class="absolute left-1 top-1 w-3 h-3 bg-white rounded-full transition-transform peer-checked:translate-x-5"></div>
                        </div>
                        <span class="text-sm font-medium text-surface-700 group-hover:text-primary-600 transition-colors">Tampilkan di website publik</span>
                    </label>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-surface-100 flex justify-end gap-3">
                <a href="{{ route('admin.media.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </x-card>
</div>

@push('scripts')
<script>
    function toggleInputs(tipe) {
        const inputImage = document.getElementById('input-image');
        const inputVideo = document.getElementById('input-video');
        
        if (tipe === 'IMAGE') {
            inputImage.classList.remove('hidden');
            inputVideo.classList.add('hidden');
        } else {
            inputImage.classList.add('hidden');
            inputVideo.classList.remove('hidden');
        }
    }
</script>
@endpush
@endsection
