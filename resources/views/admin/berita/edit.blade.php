@extends('layouts.app')

@section('title', 'Edit: ' . Str::limit($berita->judul, 30))

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm text-surface-500 mb-2">
            <a href="{{ route('admin.berita.index') }}" class="hover:text-primary-600 transition-colors">Berita & Pengumuman</a>
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
<form action="{{ route('admin.berita.update', $berita) }}" method="POST" enctype="multipart/form-data" class="max-w-4xl mx-auto space-y-6" id="form-berita">
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

    <x-card title="Konten">
        <div class="space-y-5">
            {{-- Tipe --}}
            <div>
                <label class="block text-sm font-medium text-surface-700 mb-2">Tipe Konten <span class="text-danger-500">*</span></label>
                <input type="hidden" name="tipe" id="input-tipe" value="{{ old('tipe', $berita->tipe ?? 'berita') }}">
                <div class="flex gap-3">
                    <div id="tipe-berita" onclick="pilihTipe('berita')" class="flex-1 cursor-pointer border-2 rounded-xl p-4 transition-all text-center {{ old('tipe', $berita->tipe ?? 'berita') === 'berita' ? 'border-info-500 bg-info-50 ring-2 ring-info-200' : 'border-surface-200 hover:border-info-300' }}">
                        <i data-lucide="newspaper" class="w-6 h-6 mx-auto mb-2 text-info-500"></i>
                        <p class="font-semibold text-surface-900 text-sm">📰 Berita</p>
                        <p class="text-xs text-surface-500 mt-1">Artikel berita & kegiatan pesantren</p>
                    </div>
                    <div id="tipe-pengumuman" onclick="pilihTipe('pengumuman')" class="flex-1 cursor-pointer border-2 rounded-xl p-4 transition-all text-center {{ old('tipe', $berita->tipe ?? 'berita') === 'pengumuman' ? 'border-warning-500 bg-warning-50 ring-2 ring-warning-200' : 'border-surface-200 hover:border-warning-300' }}">
                        <i data-lucide="megaphone" class="w-6 h-6 mx-auto mb-2 text-warning-500"></i>
                        <p class="font-semibold text-surface-900 text-sm">📋 Pengumuman</p>
                        <p class="text-xs text-surface-500 mt-1">Pengumuman resmi pesantren</p>
                    </div>
                </div>
            </div>

            {{-- Judul --}}
            <div>
                <label class="block text-sm font-medium text-surface-700 mb-1">Judul <span class="text-danger-500">*</span></label>
                <input type="text" name="judul" value="{{ old('judul', $berita->judul) }}" required
                       class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
            </div>

            {{-- Ringkasan --}}
            <div>
                <label class="block text-sm font-medium text-surface-700 mb-1">Ringkasan <span class="text-surface-400 font-normal">(Opsional)</span></label>
                <textarea name="ringkasan" rows="2"
                          class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">{{ old('ringkasan', $berita->ringkasan) }}</textarea>
            </div>

            {{-- Konten Editor --}}
            <div>
                <label class="block text-sm font-medium text-surface-700 mb-1">Konten Lengkap <span class="text-danger-500">*</span></label>
                {{-- Toolbar --}}
                <div class="flex flex-wrap gap-1 p-2 bg-surface-50 border border-surface-300 rounded-t-lg border-b-0">
                    <button type="button" onclick="formatDoc('bold')" class="p-2 rounded hover:bg-surface-200 text-surface-600 font-bold text-sm" title="Bold"><b>B</b></button>
                    <button type="button" onclick="formatDoc('italic')" class="p-2 rounded hover:bg-surface-200 text-surface-600 italic text-sm" title="Italic"><i>I</i></button>
                    <button type="button" onclick="formatDoc('underline')" class="p-2 rounded hover:bg-surface-200 text-surface-600 underline text-sm" title="Underline"><u>U</u></button>
                    <span class="w-px bg-surface-300 mx-1"></span>
                    <button type="button" onclick="formatDoc('insertUnorderedList')" class="p-2 rounded hover:bg-surface-200 text-surface-600 text-sm" title="Bullet List">• List</button>
                    <button type="button" onclick="formatDoc('insertOrderedList')" class="p-2 rounded hover:bg-surface-200 text-surface-600 text-sm" title="Numbered List">1. List</button>
                    <span class="w-px bg-surface-300 mx-1"></span>
                    <button type="button" onclick="formatDoc('formatBlock', 'h2')" class="p-2 rounded hover:bg-surface-200 text-surface-600 text-sm font-bold" title="Heading 2">H2</button>
                    <button type="button" onclick="formatDoc('formatBlock', 'h3')" class="p-2 rounded hover:bg-surface-200 text-surface-600 text-sm font-bold" title="Heading 3">H3</button>
                    <button type="button" onclick="formatDoc('formatBlock', 'p')" class="p-2 rounded hover:bg-surface-200 text-surface-600 text-sm" title="Paragraph">¶</button>
                    <span class="w-px bg-surface-300 mx-1"></span>
                    <button type="button" onclick="insertLink()" class="p-2 rounded hover:bg-surface-200 text-surface-600 text-sm" title="Link">🔗</button>
                    <button type="button" onclick="formatDoc('removeFormat')" class="p-2 rounded hover:bg-surface-200 text-surface-400 text-sm" title="Clear Format">✕</button>
                </div>
                {{-- Editable Area --}}
                <div id="editor-area" contenteditable="true"
                     class="w-full min-h-[300px] p-4 border border-surface-300 rounded-b-lg bg-white text-sm text-surface-800 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none prose prose-sm max-w-none"
                     style="font-family: 'Inter', sans-serif; line-height: 1.7;">{!! old('konten', $berita->konten) !!}</div>
                {{-- Hidden input to store HTML --}}
                <input type="hidden" name="konten" id="konten-hidden">
            </div>
        </div>
    </x-card>

    <x-card title="Gambar & Pengaturan">
        <div class="space-y-5">
            {{-- Gambar Cover dengan Preview --}}
            <div>
                <label class="block text-sm font-medium text-surface-700 mb-1">Gambar Cover</label>
                <div class="flex items-start gap-4">
                    <div id="cover-preview-container" class="{{ $berita->gambar_cover ? '' : 'hidden' }}">
                        <img id="cover-preview" src="{{ $berita->gambar_cover ? Storage::url($berita->gambar_cover) : '' }}" alt="Cover" class="w-32 h-20 object-cover rounded-lg border border-surface-200">
                        @if($berita->gambar_cover)
                            <span class="text-xs text-surface-400 block mt-1">Cover saat ini</span>
                        @endif
                    </div>
                    <div class="flex-1">
                        <input type="file" name="gambar_cover" accept="image/*" id="input-gambar-cover"
                               class="w-full text-sm text-surface-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
                        <p class="text-xs text-surface-400 mt-1">Upload baru untuk mengganti. Kosongkan jika tidak ingin mengubah.</p>
                    </div>
                </div>
            </div>

            {{-- Publish & Pin --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex items-center gap-3 p-4 bg-surface-50 rounded-xl border border-surface-200">
                    <input type="checkbox" name="is_published" value="1" id="is_published" {{ $berita->is_published ? 'checked' : '' }}
                           class="w-4 h-4 text-primary-600 border-surface-300 rounded focus:ring-primary-500">
                    <label for="is_published" class="text-sm font-medium text-surface-700">
                        <i data-lucide="eye" class="w-4 h-4 inline-block mr-1 text-success-500"></i>
                        Publikasikan
                        <span class="block text-xs text-surface-400 font-normal mt-0.5">Yang tidak dipublikasikan tidak tampil di website.</span>
                    </label>
                </div>
                <div class="flex items-center gap-3 p-4 bg-surface-50 rounded-xl border border-surface-200">
                    <input type="checkbox" name="is_pinned" value="1" id="is_pinned" {{ $berita->is_pinned ? 'checked' : '' }}
                           class="w-4 h-4 text-accent-600 border-surface-300 rounded focus:ring-accent-500">
                    <label for="is_pinned" class="text-sm font-medium text-surface-700">
                        <i data-lucide="pin" class="w-4 h-4 inline-block mr-1 text-accent-500"></i>
                        Sematkan di Atas
                        <span class="block text-xs text-surface-400 font-normal mt-0.5">Akan tampil paling atas di halaman website.</span>
                    </label>
                </div>
            </div>
        </div>
    </x-card>

    <div class="flex justify-end gap-3">
        <a href="{{ route('admin.berita.index') }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary flex items-center gap-2">
            <i data-lucide="save" class="w-4 h-4"></i>
            Perbarui
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // ── Tipe Selector ──
    function pilihTipe(tipe) {
        document.getElementById('input-tipe').value = tipe;
        const b = document.getElementById('tipe-berita');
        const p = document.getElementById('tipe-pengumuman');
        b.className = 'flex-1 cursor-pointer border-2 rounded-xl p-4 transition-all text-center border-surface-200 hover:border-info-300';
        p.className = 'flex-1 cursor-pointer border-2 rounded-xl p-4 transition-all text-center border-surface-200 hover:border-warning-300';
        if (tipe === 'berita') {
            b.className = 'flex-1 cursor-pointer border-2 rounded-xl p-4 transition-all text-center border-info-500 bg-info-50 ring-2 ring-info-200';
        } else {
            p.className = 'flex-1 cursor-pointer border-2 rounded-xl p-4 transition-all text-center border-warning-500 bg-warning-50 ring-2 ring-warning-200';
        }
    }

    // ── Rich Text Editor (no external library) ──
    function formatDoc(cmd, value) {
        document.execCommand(cmd, false, value || null);
        document.getElementById('editor-area').focus();
    }

    function insertLink() {
        const url = prompt('Masukkan URL:', 'https://');
        if (url) { document.execCommand('createLink', false, url); }
    }

    // ── Sync editor → hidden input on form submit ──
    document.getElementById('form-berita').addEventListener('submit', function() {
        document.getElementById('konten-hidden').value = document.getElementById('editor-area').innerHTML;
    });

    // ── Cover Image Preview ──
    document.getElementById('input-gambar-cover').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('cover-preview');
        const container = document.getElementById('cover-preview-container');
        if (file) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                preview.src = ev.target.result;
                container.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
