@extends('layouts.app')

@section('title', 'Manajemen Lembaga')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Manajemen Lembaga</h1>
        <p class="text-sm text-surface-500 mt-1">Kelola data institusi/sekolah di bawah naungan pesantren.</p>
    </div>
    <button onclick="openModal('modal-tambah')" class="btn-primary flex items-center gap-2">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Tambah Lembaga</span>
    </button>
</div>
@endsection

@section('content')
<x-card :padding="false">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-surface-50 text-surface-600 border-b border-surface-100">
                <tr>
                    <th class="px-6 py-4 font-semibold">Nama Lembaga</th>
                    <th class="px-6 py-4 font-semibold">Jenjang / Tipe</th>
                    <th class="px-6 py-4 font-semibold">NPSN</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100 text-surface-700">
                @forelse($lembagas as $lembaga)
                <tr class="hover:bg-surface-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-medium text-surface-900">{{ $lembaga->nama }}</div>
                        <div class="text-xs text-surface-400 mt-1">{{ $lembaga->singkatan }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <x-badge :variant="$lembaga->tipe === 'FORMAL' ? 'info' : 'warning'">
                            {{ $lembaga->jenjang }}
                        </x-badge>
                    </td>
                    <td class="px-6 py-4">{{ $lembaga->npsn ?? '-' }}</td>
                    <td class="px-6 py-4">
                        @if($lembaga->is_active)
                            <x-badge variant="success" dot>Aktif</x-badge>
                        @else
                            <x-badge variant="danger" dot>Nonaktif</x-badge>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="editLembaga({{ json_encode($lembaga) }})" class="text-primary-600 hover:text-primary-700 p-2 rounded-lg hover:bg-primary-50 transition-colors" title="Edit">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                        </button>
                        <form action="{{ route('admin.lembaga.destroy', $lembaga) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lembaga ini?');">
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
                    <td colspan="5" class="px-6 py-8 text-center text-surface-500">
                        <div class="flex flex-col items-center justify-center">
                            <i data-lucide="building" class="w-12 h-12 text-surface-300 mb-3"></i>
                            <p>Belum ada data lembaga.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>

{{-- Modal Tambah / Edit --}}
<div id="modal-lembaga" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-surface-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="px-6 py-4 border-b border-surface-100 flex justify-between items-center bg-surface-50">
                    <h3 class="text-lg font-bold text-surface-900 font-heading" id="modal-title">Tambah Lembaga</h3>
                    <button type="button" onclick="closeModal()" class="text-surface-400 hover:text-surface-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <form id="form-lembaga" action="{{ route('admin.lembaga.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="form-method" value="POST">
                    
                    <div class="px-6 py-4 space-y-4">
                        <x-form-input name="nama" id="lembaga_nama" label="Nama Lembaga" required placeholder="Contoh: Madrasah Tsanawiyah Nurul Furqon" />
                        
                        <div class="grid grid-cols-2 gap-4">
                            <x-form-input name="singkatan" id="lembaga_singkatan" label="Singkatan" placeholder="Contoh: MTs" />
                            <x-form-input name="npsn" id="lembaga_npsn" label="NPSN" placeholder="Opsional" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="lembaga_jenjang" class="block text-sm font-medium text-surface-700 mb-1">Jenjang <span class="text-danger-500">*</span></label>
                                <select name="jenjang" id="lembaga_jenjang" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm text-surface-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20" required>
                                    <option value="PAUD">PAUD/TK</option>
                                    <option value="SD">SD/MI</option>
                                    <option value="SMP" selected>SMP/MTs</option>
                                    <option value="SMA">SMA/MA/SMK</option>
                                    <option value="MADIN">MADIN</option>
                                    <option value="TAHFIDZ">TAHFIDZ</option>
                                    <option value="PERGURUAN_TINGGI">Perguruan Tinggi</option>
                                    <option value="NON_FORMAL">Non Formal</option>
                                    <option value="LAINNYA">Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label for="lembaga_tipe" class="block text-sm font-medium text-surface-700 mb-1">Tipe <span class="text-danger-500">*</span></label>
                                <select name="tipe" id="lembaga_tipe" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm text-surface-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20" required>
                                    <option value="FORMAL" selected>Formal</option>
                                    <option value="NON_FORMAL">Non Formal</option>
                                    <option value="PONDOK">Pondok Pesantren</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 mt-4">
                            <input type="checkbox" name="is_active" id="lembaga_is_active" value="1" checked class="rounded border-surface-300 text-primary-600 focus:ring-primary-500 w-4 h-4">
                            <label for="lembaga_is_active" class="text-sm font-medium text-surface-700">Lembaga Aktif</label>
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
    const modal = document.getElementById('modal-lembaga');
    const form = document.getElementById('form-lembaga');
    const title = document.getElementById('modal-title');
    const methodInput = document.getElementById('form-method');

    function openModal() {
        // Reset form for create
        form.reset();
        form.action = "{{ route('admin.lembaga.store') }}";
        methodInput.value = "POST";
        title.innerText = "Tambah Lembaga";
        document.getElementById('lembaga_is_active').checked = true;
        
        modal.classList.remove('hidden');
    }

    function editLembaga(data) {
        // Fill form for edit
        form.action = `/admin/lembaga/${data.id}`;
        methodInput.value = "PUT";
        title.innerText = "Edit Lembaga";
        
        document.getElementById('lembaga_nama').value = data.nama;
        document.getElementById('lembaga_singkatan').value = data.singkatan || '';
        document.getElementById('lembaga_npsn').value = data.npsn || '';
        document.getElementById('lembaga_jenjang').value = data.jenjang;
        document.getElementById('lembaga_tipe').value = data.tipe;
        document.getElementById('lembaga_is_active').checked = data.is_active;
        
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }
</script>
@endpush
