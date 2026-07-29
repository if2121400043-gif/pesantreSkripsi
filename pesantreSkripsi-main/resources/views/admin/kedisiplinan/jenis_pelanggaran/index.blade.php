@extends('layouts.app')

@section('title', 'Master Jenis Pelanggaran')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm text-surface-500 mb-2">
            <a href="{{ route('admin.pelanggaran.index') }}" class="hover:text-primary-600 transition-colors">Catatan Pelanggaran</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-surface-900 font-medium">Master Jenis</span>
        </div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Master Jenis Pelanggaran</h1>
    </div>
    <button onclick="openModal()" class="btn-primary flex items-center gap-2">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Tambah Aturan</span>
    </button>
</div>
@endsection

@section('content')
<x-card :padding="false">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-surface-50/50 text-surface-600 border-b border-surface-100">
                <tr>
                    <th class="px-6 py-4 font-semibold">Bentuk Pelanggaran / Aturan</th>
                    <th class="px-6 py-4 font-semibold text-center">Kategori</th>
                    <th class="px-6 py-4 font-semibold text-center">Poin Hukuman</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100 text-surface-700">
                @forelse($jenisPelanggarans as $jenis)
                <tr class="hover:bg-surface-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-surface-900">{{ $jenis->nama }}</div>
                        <div class="text-xs text-surface-500">{{ $jenis->pesantren->nama ?? 'Semua Cabang' }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($jenis->kategori === 'BERAT')
                            <span class="inline-flex px-2 py-1 rounded-md text-xs font-bold bg-danger-100 text-danger-700">BERAT</span>
                        @elseif($jenis->kategori === 'SEDANG')
                            <span class="inline-flex px-2 py-1 rounded-md text-xs font-bold bg-warning-100 text-warning-700">SEDANG</span>
                        @else
                            <span class="inline-flex px-2 py-1 rounded-md text-xs font-bold bg-surface-200 text-surface-700">RINGAN</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="font-mono font-bold text-danger-600">+{{ $jenis->poin }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="editJenis({{ json_encode($jenis) }})" class="inline-flex text-primary-600 hover:text-primary-700 p-2 rounded-lg hover:bg-primary-50 transition-colors" title="Edit">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                        </button>
                        <form action="{{ route('admin.jenis-pelanggaran.destroy', $jenis) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus aturan/jenis pelanggaran ini?');">
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
                    <td colspan="4" class="px-6 py-12 text-center text-surface-500">
                        <div class="flex flex-col items-center justify-center">
                            <i data-lucide="clipboard-list" class="w-12 h-12 text-surface-300 mb-3"></i>
                            <p class="font-medium text-surface-900 mb-1">Belum Ada Aturan Pelanggaran</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>

{{-- Modal --}}
<div id="modal-jenis" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-surface-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-visible rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                <div class="px-6 py-4 border-b border-surface-100 flex justify-between items-center bg-surface-50">
                    <h3 class="text-lg font-bold text-surface-900 font-heading" id="modal-title">Tambah Aturan</h3>
                    <button type="button" onclick="closeModal()" class="text-surface-400 hover:text-surface-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <form id="form-jenis" action="{{ route('admin.jenis-pelanggaran.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="form-method" value="POST">
                    
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Pesantren <span class="text-danger-500">*</span></label>
                            <select name="pesantren_id" id="pesantren_id" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                @foreach($pesantrens as $pesantren)
                                    <option value="{{ $pesantren->id }}">{{ $pesantren->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Bentuk Pelanggaran <span class="text-danger-500">*</span></label>
                            <input type="text" name="nama" id="nama" required placeholder="Contoh: Merokok, Membolos" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Kategori <span class="text-danger-500">*</span></label>
                                <select name="kategori" id="kategori" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                    <option value="RINGAN">Ringan</option>
                                    <option value="SEDANG">Sedang</option>
                                    <option value="BERAT">Berat</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Poin Tambahan <span class="text-danger-500">*</span></label>
                                <input type="number" name="poin" id="poin" required min="0" placeholder="Misal: 10" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            </div>
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
    const modal = document.getElementById('modal-jenis');
    const form = document.getElementById('form-jenis');
    const title = document.getElementById('modal-title');
    const methodInput = document.getElementById('form-method');

    function openModal() {
        form.reset();
        form.action = "{{ route('admin.jenis-pelanggaran.store') }}";
        methodInput.value = "POST";
        title.innerText = "Tambah Aturan";
        modal.classList.remove('hidden');
    }

    function editJenis(data) {
        form.action = `/admin/jenis-pelanggaran/${data.id}`;
        methodInput.value = "PUT";
        title.innerText = "Edit Aturan";
        
        document.getElementById('pesantren_id').value = data.pesantren_id;
        document.getElementById('nama').value = data.nama;
        document.getElementById('kategori').value = data.kategori;
        document.getElementById('poin').value = data.poin;
        
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }
</script>
@endpush
