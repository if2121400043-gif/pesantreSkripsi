@extends('layouts.app')

@section('title', 'Tambah Media Baru')

@section('page_header')
<div class="flex items-center gap-4">
    <a href="{{ route('admin.media.index') }}" class="p-2 text-surface-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors">
        <i data-lucide="arrow-left" class="w-5 h-5"></i>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Tambah Media Baru</h1>
        <p class="text-sm text-surface-500 mt-1">Unggah foto atau tambahkan video YouTube ke galeri.</p>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-3xl">
    <x-card>
        <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Judul -->
                <div class="md:col-span-2">
                    <label for="judul" class="block text-sm font-semibold text-surface-700 mb-2">Judul Media <span class="text-danger-500">*</span></label>
                    <input type="text" name="judul" id="judul" class="form-input @error('judul') border-danger-500 @enderror" value="{{ old('judul') }}" placeholder="Masukkan judul foto atau video..." required>
                    @error('judul') <p class="mt-1 text-xs text-danger-500">{{ $message }}</p> @enderror
                </div>

                <!-- Tipe Media -->
                <div>
                    <label for="tipe" class="block text-sm font-semibold text-surface-700 mb-2">Tipe Media <span class="text-danger-500">*</span></label>
                    <select name="tipe" id="tipe" class="form-input" onchange="toggleInputs(this.value)">
                        <option value="IMAGE" {{ old('tipe') == 'IMAGE' ? 'selected' : '' }}>Foto (Unggah Lokal)</option>
                        <option value="VIDEO" {{ old('tipe') == 'VIDEO' ? 'selected' : '' }}>Video (YouTube Link)</option>
                    </select>
                </div>

                <!-- Kategori -->
                <div>
                    <label for="kategori" class="block text-sm font-semibold text-surface-700 mb-2">Kategori</label>
                    <input type="text" name="kategori" id="kategori" class="form-input" value="{{ old('kategori') }}" placeholder="Contoh: Fasilitas, Kegiatan, dll">
                    <p class="mt-1 text-[10px] text-surface-400">Gunakan kategori yang sama untuk mengelompokkan media.</p>
                </div>

                <!-- Input Khusus Image -->
                <div id="input-image" class="md:col-span-2 {{ old('tipe') == 'VIDEO' ? 'hidden' : '' }}">
                    <label for="file_image" class="block text-sm font-semibold text-surface-700 mb-2">Pilih Foto <span class="text-danger-500">*</span></label>
                    <input type="file" name="file_image" id="file_image" class="form-input" accept="image/*">
                    <div class="mt-2 flex items-center gap-2 text-xs text-surface-500">
                        <i data-lucide="info" class="w-3.5 h-3.5 text-primary-500"></i>
                        <span>Ket: <strong>max: 1 mb</strong>. Sistem akan mengompres foto secara otomatis.</span>
                    </div>
                    @error('file_image') <p class="mt-1 text-xs text-danger-500">{{ $message }}</p> @enderror
                </div>

                <!-- Input Khusus Video -->
                <div id="input-video" class="md:col-span-2 {{ old('tipe') != 'VIDEO' ? 'hidden' : '' }}">
                    <label for="url_video" class="block text-sm font-semibold text-surface-700 mb-2">URL Video YouTube <span class="text-danger-500">*</span></label>
                    <input type="url" name="url_video" id="url_video" class="form-input" value="{{ old('url_video') }}" placeholder="https://www.youtube.com/watch?v=...">
                    <p class="mt-2 text-xs text-surface-500">Tempelkan link lengkap video YouTube (Contoh: https://www.youtube.com/watch?v=dQw4w9WgXcQ)</p>
                    @error('url_video') <p class="mt-1 text-xs text-danger-500">{{ $message }}</p> @enderror
                </div>

                <!-- Deskripsi -->
                <div class="md:col-span-2">
                    <label for="deskripsi" class="block text-sm font-semibold text-surface-700 mb-2">Deskripsi (Opsional)</label>
                    <textarea name="deskripsi" id="deskripsi" rows="3" class="form-input" placeholder="Tulis keterangan singkat mengenai media ini...">{{ old('deskripsi') }}</textarea>
                </div>

                <!-- Status Aktif -->
                <div class="md:col-span-2">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" name="is_active" class="sr-only peer" checked value="1">
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
                    <span>Simpan Media</span>
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
