@extends('layouts.app')

@section('title', 'Wilayah Pesantren (Zona/Daerah)')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Wilayah Pesantren (Zona / Daerah)</h1>
        <p class="text-sm text-surface-500 mt-1">Kelola zona/wilayah internal pesantren beserta pengelompokan asrama dan kamar.</p>
    </div>
    <div>
        <button type="button" onclick="openCreateModal()" class="btn-primary flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Wilayah Baru</span>
        </button>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Info Card --}}
    <div class="p-4 rounded-xl bg-primary-50/50 border border-primary-100 text-sm text-primary-900 flex items-start gap-3">
        <i data-lucide="map-pin" class="w-5 h-5 text-primary-600 shrink-0 mt-0.5"></i>
        <div class="space-y-1">
            <p class="font-semibold text-primary-950">Struktur Wilayah Internal Pesantren:</p>
            <p class="text-xs text-primary-800">
                <strong class="font-semibold">Wilayah Pesantren</strong> adalah pembagian daerah/zona di dalam kompleks pesantren (misal: <em>Wilayah Sunan Giri Putra</em>, <em>Wilayah Khadijah Putri</em>, <em>Wilayah Barat</em>). Setiap Wilayah dapat menampung beberapa <strong class="font-semibold">Asrama</strong>, dan setiap Asrama menampung beberapa <strong class="font-semibold">Kamar Santri</strong>.
            </p>
        </div>
    </div>

    {{-- Filter & Grid --}}
    <x-card :padding="false">
        <div class="p-4 border-b border-surface-100 bg-surface-50">
            <form action="{{ route('admin.wilayah.index') }}" method="GET" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode wilayah..." class="w-full px-3 py-2 text-sm rounded-lg border border-surface-300 bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                </div>
                <div class="w-full sm:w-auto">
                    <select name="jenis_kelamin" class="w-full px-3 py-2 text-sm rounded-lg border border-surface-300 bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" onchange="this.form.submit()">
                        <option value="">Semua Peruntukan (Putra/Putri/Campuran)</option>
                        <option value="L" {{ request('jenis_kelamin') == 'L' ? 'selected' : '' }}>Khusus Putra (Laki-laki)</option>
                        <option value="P" {{ request('jenis_kelamin') == 'P' ? 'selected' : '' }}>Khusus Putri (Perempuan)</option>
                        <option value="CAMPURAN" {{ request('jenis_kelamin') == 'CAMPURAN' ? 'selected' : '' }}>Campuran</option>
                    </select>
                </div>
                <button type="submit" class="btn-secondary text-sm py-2 px-4">Cari</button>
            </form>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($wilayahs as $w)
                    <div class="border border-surface-200 rounded-xl overflow-hidden hover:border-primary-300 transition-colors flex flex-col h-full bg-white shadow-2xs">
                        <div class="p-4 border-b border-surface-100 flex justify-between items-start bg-surface-50/50">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[0.65rem] font-mono font-bold text-primary-600 bg-primary-50 px-2 py-0.5 rounded border border-primary-100">
                                        {{ $w->kode ?? 'KODE-' . $w->id }}
                                    </span>
                                    @if($w->jenis_kelamin === 'L')
                                        <x-badge variant="info" size="sm">Putra</x-badge>
                                    @elseif($w->jenis_kelamin === 'P')
                                        <x-badge variant="warning" size="sm">Putri</x-badge>
                                    @else
                                        <x-badge variant="neutral" size="sm">Campuran</x-badge>
                                    @endif
                                </div>
                                <h3 class="font-bold text-lg text-surface-900 leading-tight">{{ $w->nama }}</h3>
                            </div>
                        </div>

                        <div class="p-4 flex-1 space-y-3">
                            <p class="text-xs text-surface-600 line-clamp-2">
                                {{ $w->keterangan ?? 'Tidak ada keterangan khusus.' }}
                            </p>

                            <div class="pt-3 border-t border-surface-100 flex items-center justify-between text-xs text-surface-500">
                                <span class="flex items-center gap-1 font-medium">
                                    <i data-lucide="home" class="w-4 h-4 text-primary-500"></i>
                                    {{ $w->asrama_count }} Asrama Terdaftar
                                </span>
                                
                                <span class="font-semibold text-surface-700">
                                    @php
                                        $totalKamar = $w->asrama->sum(fn($a) => $a->kamar->count());
                                    @endphp
                                    {{ $totalKamar }} Kamar Total
                                </span>
                            </div>

                            @if($w->asrama->count() > 0)
                                <div class="space-y-1">
                                    <p class="text-[0.7rem] font-semibold text-surface-400 uppercase tracking-wider">Daftar Asrama:</p>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($w->asrama->take(4) as $asr)
                                            <span class="text-[0.7rem] bg-surface-100 text-surface-700 px-2 py-0.5 rounded border border-surface-200">
                                                {{ $asr->nama }}
                                            </span>
                                        @endforeach
                                        @if($w->asrama->count() > 4)
                                            <span class="text-[0.7rem] bg-surface-200 text-surface-600 px-1.5 py-0.5 rounded font-bold">
                                                +{{ $w->asrama->count() - 4 }} lagi
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="border-t border-surface-100 p-2 flex bg-surface-50 gap-2">
                            <button type="button" onclick="openEditModal({{ json_encode($w) }})" class="flex-1 py-1.5 text-xs font-semibold text-surface-700 hover:bg-surface-200/60 rounded-lg transition-colors text-center border border-surface-200 bg-white">
                                <i data-lucide="edit" class="w-3.5 h-3.5 inline mr-1"></i> Edit
                            </button>
                            <a href="{{ route('admin.asrama.index', ['wilayah_id' => $w->id]) }}" class="flex-1 py-1.5 text-xs font-semibold text-primary-700 hover:bg-primary-50 rounded-lg transition-colors text-center border border-primary-200 bg-primary-50/50">
                                <i data-lucide="home" class="w-3.5 h-3.5 inline mr-1"></i> Kelola Asrama
                            </a>
                            @if($w->asrama_count == 0)
                                <form action="{{ route('admin.wilayah.destroy', $w->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus wilayah ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-danger-500 hover:bg-danger-50 rounded-lg transition-colors border border-danger-200 bg-white" title="Hapus Wilayah">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-surface-500">
                        <i data-lucide="map-pin-off" class="w-12 h-12 text-surface-300 mx-auto mb-3"></i>
                        <h3 class="font-bold text-surface-900 text-lg mb-1">Belum Ada Wilayah Pesantren</h3>
                        <p class="text-sm max-w-md mx-auto mb-4">Tambahkan wilayah internal pesantren untuk mengelompokkan gedung asrama dan kamar santri.</p>
                        <button type="button" onclick="openCreateModal()" class="btn-primary text-sm inline-flex items-center gap-2">
                            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Wilayah Pertama
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        @if($wilayahs->hasPages())
            <div class="p-4 border-t border-surface-100">
                {{ $wilayahs->links() }}
            </div>
        @endif
    </x-card>
</div>

{{-- Modal Create / Edit Wilayah --}}
<div id="modal-wilayah" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-surface-200 space-y-4">
        <div class="flex justify-between items-center border-b border-surface-100 pb-3">
            <h3 class="font-bold text-lg text-surface-900" id="modal-title">Tambah Wilayah Pesantren</h3>
            <button type="button" onclick="closeModal()" class="text-surface-400 hover:text-surface-600 p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="form-wilayah" method="POST" action="{{ route('admin.wilayah.store') }}" class="space-y-4">
            @csrf
            <div id="method-field"></div>

            <div>
                <label class="block text-sm font-medium text-surface-700 mb-1">Nama Wilayah / Zona <span class="text-danger-500">*</span></label>
                <input type="text" name="nama" id="input-nama" required placeholder="Contoh: Wilayah Sunan Giri (Putra)" class="w-full px-3 py-2 text-sm rounded-lg border border-surface-300 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Kode Singkatan (Opsional)</label>
                    <input type="text" name="kode" id="input-kode" placeholder="Otomatis jika dikosongkan" class="w-full px-3 py-2 text-sm rounded-lg border border-surface-300 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    <p class="text-[0.7rem] text-primary-600 mt-1"><i data-lucide="sparkles" class="w-3 h-3 inline"></i> Preview: <strong id="preview-kode" class="font-mono bg-primary-100 px-1 py-0.5 rounded text-primary-800">-</strong></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Peruntukan Gender <span class="text-danger-500">*</span></label>
                    <select name="jenis_kelamin" id="input-jk" required class="w-full px-3 py-2 text-sm rounded-lg border border-surface-300 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        <option value="L">Khusus Putra (Santri Laki-laki)</option>
                        <option value="P">Khusus Putri (Santri Perempuan)</option>
                        <option value="CAMPURAN">Campuran (Putra & Putri)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-surface-700 mb-1">Keterangan / Lokasi</label>
                <textarea name="keterangan" id="input-keterangan" rows="3" placeholder="Contoh: Kompleks asrama timur dekat masjid utama" class="w-full px-3 py-2 text-sm rounded-lg border border-surface-300 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-surface-100">
                <button type="button" onclick="closeModal()" class="btn-secondary text-sm">Batal</button>
                <button type="submit" class="btn-primary text-sm">Simpan Wilayah</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openCreateModal() {
        document.getElementById('modal-title').innerText = 'Tambah Wilayah Pesantren Baru';
        document.getElementById('form-wilayah').action = "{{ route('admin.wilayah.store') }}";
        document.getElementById('method-field').innerHTML = '';
        document.getElementById('input-nama').value = '';
        document.getElementById('input-kode').value = '';
        document.getElementById('input-jk').value = 'L';
        document.getElementById('input-keterangan').value = '';
        document.getElementById('modal-wilayah').classList.remove('hidden');
    }

    function openEditModal(w) {
        document.getElementById('modal-title').innerText = 'Edit Wilayah Pesantren: ' + w.nama;
        document.getElementById('form-wilayah').action = "/admin/wilayah/" + w.id;
        document.getElementById('method-field').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('input-nama').value = w.nama;
        document.getElementById('input-kode').value = w.kode ?? '';
        document.getElementById('input-jk').value = w.jenis_kelamin ?? 'L';
        document.getElementById('input-keterangan').value = w.keterangan ?? '';
        document.getElementById('modal-wilayah').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('modal-wilayah').classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const inputNama = document.getElementById('input-nama');
        const inputKode = document.getElementById('input-kode');
        const previewKode = document.getElementById('preview-kode');

        function updatePreview() {
            if (!previewKode) return;
            if (inputKode && inputKode.value.trim() !== '') {
                previewKode.innerText = inputKode.value.trim().toUpperCase();
                return;
            }
            const val = inputNama ? inputNama.value.trim() : '';
            if (!val) {
                previewKode.innerText = '-';
                return;
            }
            const words = val.split(/\s+/).filter(w => w.length > 0);
            let code = '';
            if (words.length >= 2) {
                words.forEach(w => code += w[0].toUpperCase());
            } else if (words.length === 1) {
                code = words[0].substring(0, 4).toUpperCase();
            }
            previewKode.innerText = code || '-';
        }

        if (inputNama) inputNama.addEventListener('input', updatePreview);
        if (inputKode) inputKode.addEventListener('input', updatePreview);
    });
</script>
@endpush
@endsection
