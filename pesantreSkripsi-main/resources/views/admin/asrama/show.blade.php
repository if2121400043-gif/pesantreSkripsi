@extends('layouts.app')

@section('title', 'Manajemen Kamar: ' . $asrama->nama)

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm text-surface-500 mb-2">
            <a href="{{ route('admin.asrama.index') }}" class="hover:text-primary-600 transition-colors">Asrama</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-surface-900 font-medium">{{ $asrama->nama }}</span>
        </div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Kamar {{ $asrama->nama }}</h1>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.asrama.index') }}" class="btn-secondary flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali</span>
        </a>
        @if($activeTahun)
            <a href="{{ route('admin.penempatan-asrama.index', ['asrama_id' => $asrama->id, 'tahun_pelajaran_id' => $activeTahun->id]) }}" class="btn-secondary flex items-center gap-2 text-primary-600 hover:text-primary-700">
                <i data-lucide="bed" class="w-4 h-4"></i>
                <span>Atur Penempatan</span>
            </a>
        @endif
        <button onclick="openModal()" class="btn-primary flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Kamar</span>
        </button>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    {{-- Sidebar Detail Asrama --}}
    <div class="lg:col-span-1 space-y-6">
        <x-card title="Detail Asrama">
            <div class="space-y-4">
                <div>
                    <p class="text-xs text-surface-500 font-medium">Nama Gedung</p>
                    <p class="font-medium text-surface-900">{{ $asrama->nama }} ({{ $asrama->kode ?? '-' }})</p>
                </div>
                <div>
                    <p class="text-xs text-surface-500 font-medium">Peruntukan</p>
                    <p class="font-medium text-surface-900">{{ $asrama->jenis_kelamin === 'L' ? 'Putra' : ($asrama->jenis_kelamin === 'P' ? 'Putri' : 'Campuran') }}</p>
                </div>
                <div>
                    <p class="text-xs text-surface-500 font-medium">Kapasitas Gedung</p>
                    <div class="w-full bg-surface-100 rounded-full h-2 mt-2">
                        @php
                            $totalKapasitasKamar = $kamars->sum('kapasitas');
                            $percentage = $asrama->kapasitas > 0 ? min(($totalKapasitasKamar / $asrama->kapasitas) * 100, 100) : 0;
                            $colorClass = $percentage > 90 ? 'bg-danger-500' : ($percentage > 75 ? 'bg-warning-500' : 'bg-primary-500');
                        @endphp
                        <div class="{{ $colorClass }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                    <p class="text-xs text-surface-500 mt-1">{{ $totalKapasitasKamar }} dari {{ $asrama->kapasitas }} dialokasikan ke kamar</p>
                </div>
                <div class="pt-4 border-t border-surface-100">
                    <p class="text-sm text-surface-600">{{ $asrama->keterangan ?: 'Tidak ada deskripsi/keterangan.' }}</p>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Daftar Kamar --}}
    <div class="lg:col-span-3">
        <x-card :padding="false">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-surface-50 text-surface-600 border-b border-surface-100">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Nama Kamar</th>
                            <th class="px-6 py-4 font-semibold">Lantai</th>
                            <th class="px-6 py-4 font-semibold">Kapasitas</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100 text-surface-700">
                        @forelse($kamars as $kamar)
                        <tr class="hover:bg-surface-50/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-surface-900 flex items-center gap-3">
                                <i data-lucide="door-closed" class="w-5 h-5 text-surface-400"></i>
                                {{ $kamar->nama }}
                            </td>
                            <td class="px-6 py-4">{{ $kamar->lantai ?? '-' }}</td>
                            <td class="px-6 py-4 font-medium">{{ $kamar->terisi_count ?? 0 }} / {{ $kamar->kapasitas }} <span class="text-surface-500 font-normal">Santri</span></td>
                            <td class="px-6 py-4">
                                @if($kamar->is_active)
                                    <x-badge variant="success" dot>Aktif</x-badge>
                                @else
                                    <x-badge variant="danger" dot>Renovasi / Nonaktif</x-badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="editKamar({{ json_encode($kamar) }})" class="text-primary-600 hover:text-primary-700 p-2 rounded-lg hover:bg-primary-50 transition-colors" title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>
                                <form action="{{ route('admin.asrama.kamar.destroy', [$asrama, $kamar]) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kamar ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-danger-500 hover:text-danger-600 p-2 rounded-lg hover:bg-danger-50 transition-colors" title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-surface-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i data-lucide="door-open" class="w-12 h-12 text-surface-300 mb-3"></i>
                                    <p class="font-medium text-surface-900 mb-1">Belum Ada Kamar</p>
                                    <p class="text-sm">Asrama ini belum memiliki kamar yang didaftarkan.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</div>

{{-- Modal Tambah / Edit Kamar --}}
<div id="modal-kamar" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-surface-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                <div class="px-6 py-4 border-b border-surface-100 flex justify-between items-center bg-surface-50">
                    <h3 class="text-lg font-bold text-surface-900 font-heading" id="modal-title">Tambah Kamar</h3>
                    <button type="button" onclick="closeModal()" class="text-surface-400 hover:text-surface-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <form id="form-kamar" action="{{ route('admin.asrama.kamar.store', $asrama) }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="form-method" value="POST">
                    
                    <div class="px-6 py-4 space-y-4">
                        <x-form-input name="nama" id="kamar_nama" label="Nama / Nomor Kamar" required placeholder="Contoh: Kamar A-1" />
                        
                        <div class="grid grid-cols-2 gap-4">
                            <x-form-input name="lantai" id="kamar_lantai" label="Lantai" placeholder="Contoh: Lantai 1" />
                            <x-form-input type="number" name="kapasitas" id="kamar_kapasitas" label="Kapasitas (Orang)" required min="1" placeholder="Misal: 4" />
                        </div>

                        <div class="flex items-center gap-2 mt-4">
                            <input type="checkbox" name="is_active" id="kamar_is_active" value="1" checked class="rounded border-surface-300 text-primary-600 focus:ring-primary-500 w-4 h-4">
                            <label for="kamar_is_active" class="text-sm font-medium text-surface-700">Kamar Siap Huni (Aktif)</label>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-surface-50 border-t border-surface-100 flex justify-end gap-3">
                        <button type="button" onclick="closeModal()" class="btn-secondary">Batal</button>
                        <button type="submit" class="btn-primary">Simpan Kamar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const modal = document.getElementById('modal-kamar');
    const form = document.getElementById('form-kamar');
    const title = document.getElementById('modal-title');
    const methodInput = document.getElementById('form-method');
    const baseUrl = "{{ route('admin.asrama.kamar.store', $asrama) }}";

    function openModal() {
        form.reset();
        form.action = baseUrl;
        methodInput.value = "POST";
        title.innerText = "Tambah Kamar Baru";
        document.getElementById('kamar_is_active').checked = true;
        
        modal.classList.remove('hidden');
    }

    function editKamar(data) {
        // Build correct update URL (removing /store implicitly handled)
        form.action = `/admin/asrama/{{ $asrama->id }}/kamar/${data.id}`;
        methodInput.value = "PUT";
        title.innerText = "Edit Kamar";
        
        document.getElementById('kamar_nama').value = data.nama;
        document.getElementById('kamar_lantai').value = data.lantai || '';
        document.getElementById('kamar_kapasitas').value = data.kapasitas;
        document.getElementById('kamar_is_active').checked = data.is_active;
        
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }
</script>
@endpush
