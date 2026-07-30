@extends('layouts.app')

@section('title', 'Manajemen Asrama')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Asrama & Gedung</h1>
        <p class="text-sm text-surface-500 mt-1">Kelola data asrama, gedung, dan kapasitas hunian santri.</p>
    </div>
    <button onclick="openModal()" class="btn-primary flex items-center gap-2">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Tambah Asrama</span>
    </button>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($asramas as $asrama)
    <x-card :padding="false" class="flex flex-col h-full hover:border-primary-300 transition-colors">
        <div class="p-6 border-b border-surface-100 flex-1">
            <div class="flex justify-between items-start mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
                        <i data-lucide="home" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-0.5">
                            <h3 class="font-bold text-surface-900 text-lg leading-tight">{{ $asrama->nama }}</h3>
                        </div>
                        <p class="text-xs text-primary-600 font-medium">
                            <i data-lucide="map-pin" class="w-3 h-3 inline mr-0.5"></i>
                            {{ $asrama->wilayahPesantren->nama ?? 'Tanpa Wilayah' }}
                        </p>
                    </div>
                </div>
                <div>
                    @if($asrama->is_active)
                        <x-badge variant="success" dot>Aktif</x-badge>
                    @else
                        <x-badge variant="danger" dot>Nonaktif</x-badge>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-6 mb-2">
                <div class="bg-surface-50 rounded-lg p-3 border border-surface-100">
                    <p class="text-xs text-surface-500 font-medium mb-1">Peruntukan</p>
                    <div class="font-semibold text-surface-900 flex items-center gap-2">
                        @if($asrama->jenis_kelamin === 'L')
                            <i data-lucide="users" class="w-4 h-4 text-primary-600"></i> Putra
                        @elseif($asrama->jenis_kelamin === 'P')
                            <i data-lucide="users" class="w-4 h-4 text-warning-600"></i> Putri
                        @else
                            <i data-lucide="users" class="w-4 h-4 text-success-600"></i> Campuran
                        @endif
                    </div>
                </div>
                <div class="bg-surface-50 rounded-lg p-3 border border-surface-100">
                    <p class="text-xs text-surface-500 font-medium mb-1">Kapasitas Maks</p>
                    <div class="font-semibold text-surface-900 flex items-center gap-2">
                        <i data-lucide="user-check" class="w-4 h-4 text-surface-400"></i> {{ $asrama->kapasitas }} Santri
                    </div>
                </div>
            </div>
            
            <p class="text-sm text-surface-600 mt-4 line-clamp-2">{{ $asrama->keterangan ?: 'Tidak ada deskripsi/keterangan.' }}</p>
        </div>
        <div class="p-4 bg-surface-50/50 flex justify-between items-center rounded-b-2xl">
            <div class="text-sm font-medium text-surface-600 flex items-center gap-2">
                <i data-lucide="door-closed" class="w-4 h-4"></i>
                {{ $asrama->kamar_count }} Kamar
            </div>
            <div class="flex gap-2">
                <button onclick="editAsrama({{ json_encode($asrama) }})" class="btn-secondary px-3 py-1.5 text-sm" title="Edit Data">
                    <i data-lucide="edit" class="w-4 h-4"></i>
                </button>
                <a href="{{ route('admin.asrama.show', $asrama) }}" class="btn-primary px-3 py-1.5 text-sm flex items-center gap-1">
                    Kelola Kamar <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </a>
            </div>
        </div>
    </x-card>
    @empty
    <div class="col-span-full">
        <x-card class="text-center py-12">
            <div class="flex flex-col items-center justify-center">
                <div class="w-16 h-16 bg-surface-100 text-surface-400 rounded-full flex items-center justify-center mb-4">
                    <i data-lucide="home" class="w-8 h-8"></i>
                </div>
                <h3 class="text-lg font-bold text-surface-900 mb-1">Belum Ada Data Asrama</h3>
                <p class="text-surface-500 max-w-md mx-auto mb-6">Kelola gedung asrama santri untuk memudahkan pembagian dan manajemen kamar.</p>
                <button onclick="openModal()" class="btn-primary">
                    Tambah Asrama Pertama
                </button>
            </div>
        </x-card>
    </div>
    @endforelse
</div>

{{-- Modal Tambah / Edit Asrama --}}
<div id="modal-asrama" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-surface-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="px-6 py-4 border-b border-surface-100 flex justify-between items-center bg-surface-50">
                    <h3 class="text-lg font-bold text-surface-900 font-heading" id="modal-title">Tambah Asrama</h3>
                    <button type="button" onclick="closeModal()" class="text-surface-400 hover:text-surface-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <form id="form-asrama" action="{{ route('admin.asrama.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="form-method" value="POST">
                    
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label for="asrama_wilayah" class="block text-sm font-medium text-surface-700 mb-1">Wilayah Pesantren (Zona) <span class="text-danger-500">*</span></label>
                            <select name="wilayah_pesantren_id" id="asrama_wilayah" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm text-surface-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20" required>
                                <option value="" disabled selected>Pilih Wilayah Pesantren...</option>
                                @foreach($wilayahs as $w)
                                    <option value="{{ $w->id }}">{{ $w->nama }} ({{ $w->jenis_kelamin == 'L' ? 'Putra' : ($w->jenis_kelamin == 'P' ? 'Putri' : 'Campuran') }})</option>
                                @endforeach
                            </select>
                        </div>

                        <x-form-input name="nama" id="asrama_nama" label="Nama Gedung / Asrama" required placeholder="Contoh: Asrama Al-Ghazali" />
                        
                        <div class="grid grid-cols-2 gap-4">
                            <x-form-input name="kode" id="asrama_kode" label="Kode Gedung" placeholder="Contoh: AGZ" />
                            <div>
                                <label for="asrama_jk" class="block text-sm font-medium text-surface-700 mb-1">Peruntukan <span class="text-danger-500">*</span></label>
                                <select name="jenis_kelamin" id="asrama_jk" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm text-surface-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20" required>
                                    <option value="L">Putra</option>
                                    <option value="P">Putri</option>
                                    <option value="CAMPURAN">Campuran (Gedung Terpisah)</option>
                                </select>
                            </div>
                        </div>

                        <x-form-input type="number" name="kapasitas" id="asrama_kapasitas" label="Kapasitas Total Santri" required placeholder="Contoh: 150" />

                        <div>
                            <label for="asrama_ket" class="block text-sm font-medium text-surface-700 mb-1">Keterangan / Fasilitas</label>
                            <textarea name="keterangan" id="asrama_ket" rows="3" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm text-surface-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20" placeholder="Kondisi bangunan, fasilitas khusus, dsb."></textarea>
                        </div>

                        <div class="flex items-center gap-2 mt-4">
                            <input type="checkbox" name="is_active" id="asrama_is_active" value="1" checked class="rounded border-surface-300 text-primary-600 focus:ring-primary-500 w-4 h-4">
                            <label for="asrama_is_active" class="text-sm font-medium text-surface-700">Gedung / Asrama Aktif</label>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-surface-50 border-t border-surface-100 flex justify-end gap-3">
                        <button type="button" onclick="closeModal()" class="btn-secondary">Batal</button>
                        <button type="submit" class="btn-primary">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const modal = document.getElementById('modal-asrama');
    const form = document.getElementById('form-asrama');
    const title = document.getElementById('modal-title');
    const methodInput = document.getElementById('form-method');

    function openModal() {
        form.reset();
        form.action = "{{ route('admin.asrama.store') }}";
        methodInput.value = "POST";
        title.innerText = "Tambah Asrama Baru";
        document.getElementById('asrama_is_active').checked = true;
        
        modal.classList.remove('hidden');
    }

    function editAsrama(data) {
        form.action = `/admin/asrama/${data.id}`;
        methodInput.value = "PUT";
        title.innerText = "Edit Asrama";
        
        document.getElementById('asrama_wilayah').value = data.wilayah_pesantren_id || '';
        document.getElementById('asrama_nama').value = data.nama;
        document.getElementById('asrama_kode').value = data.kode || '';
        document.getElementById('asrama_jk').value = data.jenis_kelamin;
        document.getElementById('asrama_kapasitas').value = data.kapasitas;
        document.getElementById('asrama_ket').value = data.keterangan || '';
        document.getElementById('asrama_is_active').checked = data.is_active;
        
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }
</script>
@endpush
