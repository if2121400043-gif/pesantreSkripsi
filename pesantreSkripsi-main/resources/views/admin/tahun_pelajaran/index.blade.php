@extends('layouts.app')

@section('title', 'Tahun Pelajaran')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Tahun Pelajaran</h1>
        <p class="text-sm text-surface-500 mt-1">Kelola data tahun ajaran aktif untuk keseluruhan pesantren.</p>
    </div>
    <button onclick="openModal()" class="btn-primary flex items-center gap-2">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Tambah Tahun</span>
    </button>
</div>
@endsection

@section('content')
<x-card :padding="false">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-surface-50 text-surface-600 border-b border-surface-100">
                <tr>
                    <th class="px-6 py-4 font-semibold">Tahun Pelajaran</th>
                    <th class="px-6 py-4 font-semibold">Tanggal Mulai</th>
                    <th class="px-6 py-4 font-semibold">Tanggal Selesai</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100 text-surface-700">
                @forelse($tahunPelajaran as $ta)
                <tr class="hover:bg-surface-50/50 transition-colors {{ $ta->is_active ? 'bg-primary-50/30' : '' }}">
                    <td class="px-6 py-4 font-medium text-surface-900">
                        {{ $ta->nama }}
                    </td>
                    <td class="px-6 py-4">{{ $ta->tanggal_mulai->format('d M Y') }}</td>
                    <td class="px-6 py-4">{{ $ta->tanggal_selesai->format('d M Y') }}</td>
                    <td class="px-6 py-4">
                        @if($ta->is_active)
                            <x-badge variant="success" dot>Aktif Sekarang</x-badge>
                        @else
                            <x-badge variant="surface">Tidak Aktif</x-badge>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="editTA({{ json_encode($ta) }})" class="text-primary-600 hover:text-primary-700 p-2 rounded-lg hover:bg-primary-50 transition-colors" title="Edit">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                        </button>
                        <form action="{{ route('admin.tahun-pelajaran.destroy', $ta) }}" method="POST" class="inline-block" onsubmit="return confirm('Menghapus tahun pelajaran dapat mempengaruhi data akademik. Lanjutkan?');">
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
                            <i data-lucide="calendar" class="w-12 h-12 text-surface-300 mb-3"></i>
                            <p>Belum ada data tahun pelajaran.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>

{{-- Modal Tambah / Edit --}}
<div id="modal-ta" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-surface-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="px-6 py-4 border-b border-surface-100 flex justify-between items-center bg-surface-50">
                    <h3 class="text-lg font-bold text-surface-900 font-heading" id="modal-title">Tambah Tahun Pelajaran</h3>
                    <button type="button" onclick="closeModal()" class="text-surface-400 hover:text-surface-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <form id="form-ta" action="{{ route('admin.tahun-pelajaran.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="form-method" value="POST">
                    
                    <div class="px-6 py-4 space-y-4">
                        <x-form-input name="nama" id="ta_nama" label="Nama Tahun Pelajaran" required placeholder="Contoh: 2026/2027" />
                        
                        <div class="grid grid-cols-2 gap-4">
                            <x-form-input type="date" name="tanggal_mulai" id="ta_mulai" label="Tanggal Mulai" required />
                            <x-form-input type="date" name="tanggal_selesai" id="ta_selesai" label="Tanggal Selesai" required />
                        </div>

                        <div class="flex items-center gap-2 mt-4 p-3 bg-warning-50 text-warning-700 rounded-lg border border-warning-200">
                            <input type="checkbox" name="is_active" id="ta_is_active" value="1" class="rounded border-warning-300 text-warning-600 focus:ring-warning-500 w-4 h-4">
                            <label for="ta_is_active" class="text-sm font-medium">Jadikan Tahun Pelajaran Aktif (Menggantikan yang lama)</label>
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
    const modal = document.getElementById('modal-ta');
    const form = document.getElementById('form-ta');
    const title = document.getElementById('modal-title');
    const methodInput = document.getElementById('form-method');

    function openModal() {
        form.reset();
        form.action = "{{ route('admin.tahun-pelajaran.store') }}";
        methodInput.value = "POST";
        title.innerText = "Tambah Tahun Pelajaran";
        document.getElementById('ta_is_active').checked = false;
        
        modal.classList.remove('hidden');
    }

    function editTA(data) {
        form.action = `/admin/tahun-pelajaran/${data.id}`;
        methodInput.value = "PUT";
        title.innerText = "Edit Tahun Pelajaran";
        
        document.getElementById('ta_nama').value = data.nama;
        // Format dates for input type="date" (YYYY-MM-DD)
        document.getElementById('ta_mulai').value = data.tanggal_mulai.split('T')[0];
        document.getElementById('ta_selesai').value = data.tanggal_selesai.split('T')[0];
        document.getElementById('ta_is_active').checked = data.is_active;
        
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }
</script>
@endpush
