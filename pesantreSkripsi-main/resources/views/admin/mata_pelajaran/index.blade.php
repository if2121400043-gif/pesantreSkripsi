@extends('layouts.app')

@section('title', 'Mata Pelajaran')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Mata Pelajaran</h1>
        <p class="text-sm text-surface-500 mt-1">Kelola daftar mata pelajaran untuk setiap lembaga pendidikan.</p>
    </div>
    <button onclick="openModal()" class="btn-primary flex items-center gap-2">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Tambah Mapel</span>
    </button>
</div>
@endsection

@section('content')
<x-card :padding="false">
    {{-- Search & Filter Bar --}}
    <div class="p-4 border-b border-surface-100 bg-surface-50">
        <form action="{{ route('admin.mata-pelajaran.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kode atau Nama Mapel..." 
                       class="w-full pl-10 pr-4 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors">
            </div>
            <div class="sm:w-64">
                <select name="lembaga_id" class="w-full px-4 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" onchange="this.form.submit()">
                    <option value="">Semua Lembaga</option>
                    @foreach($lembagas as $lembaga)
                        <option value="{{ $lembaga->id }}" {{ request('lembaga_id') == $lembaga->id ? 'selected' : '' }}>
                            {{ $lembaga->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-secondary px-4 py-2 hidden sm:block">Cari</button>
            @if(request()->anyFilled(['search', 'lembaga_id']))
                <a href="{{ route('admin.mata-pelajaran.index') }}" class="btn-secondary px-4 py-2 text-danger-600 border-danger-200 hover:bg-danger-50 flex items-center justify-center">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </a>
            @endif
        </form>
    </div>

    {{-- Data Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-surface-50/50 text-surface-600 border-b border-surface-100">
                <tr>
                    <th class="px-6 py-4 font-semibold">Nama Mapel</th>
                    <th class="px-6 py-4 font-semibold">Lembaga</th>
                    <th class="px-6 py-4 font-semibold">Kelompok/Kategori</th>
                    <th class="px-6 py-4 font-semibold text-center">Status</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100 text-surface-700">
                @forelse($mapels as $mapel)
                <tr class="hover:bg-surface-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-surface-900">{{ $mapel->nama_mapel }}</div>
                        <div class="text-xs text-primary-600 font-mono mt-0.5">{{ $mapel->kode_mapel ?? 'Tanpa Kode' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <x-badge variant="surface">{{ $mapel->lembaga->singkatan ?? $mapel->lembaga->nama }}</x-badge>
                    </td>
                    <td class="px-6 py-4">
                        {{ $mapel->kelompok_mapel ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($mapel->is_active)
                            <x-badge variant="success" dot>Aktif</x-badge>
                        @else
                            <x-badge variant="danger" dot>Nonaktif</x-badge>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="editMapel({{ json_encode($mapel) }})" class="inline-flex text-primary-600 hover:text-primary-700 p-2 rounded-lg hover:bg-primary-50 transition-colors" title="Edit Mapel">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                        </button>
                        <form action="{{ route('admin.mata-pelajaran.destroy', $mapel) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus mata pelajaran ini? Data nilai yang terkait mungkin ikut terhapus atau bermasalah.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-danger-500 hover:text-danger-600 p-2 rounded-lg hover:bg-danger-50 transition-colors" title="Hapus Mapel">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-surface-500">
                        <div class="flex flex-col items-center justify-center">
                            <i data-lucide="file-text" class="w-12 h-12 text-surface-300 mb-3"></i>
                            <p class="font-medium text-surface-900 mb-1">Belum Ada Mata Pelajaran</p>
                            <p class="text-sm">Silakan tambahkan mata pelajaran untuk kurikulum lembaga Anda.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($mapels->hasPages())
    <div class="p-4 border-t border-surface-100">
        {{ $mapels->links() }}
    </div>
    @endif
</x-card>

{{-- Modal Tambah / Edit Mapel --}}
<div id="modal-mapel" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-surface-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-visible rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="px-6 py-4 border-b border-surface-100 flex justify-between items-center bg-surface-50">
                    <h3 class="text-lg font-bold text-surface-900 font-heading" id="modal-title">Tambah Mata Pelajaran</h3>
                    <button type="button" onclick="closeModal()" class="text-surface-400 hover:text-surface-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <form id="form-mapel" action="{{ route('admin.mata-pelajaran.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="form-method" value="POST">
                    
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Lembaga Pendidikan <span class="text-danger-500">*</span></label>
                            <select name="lembaga_id" id="lembaga_id" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                @foreach($lembagas as $lembaga)
                                    <option value="{{ $lembaga->id }}">{{ $lembaga->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Kode Mapel</label>
                                <input type="text" name="kode_mapel" id="kode_mapel" placeholder="Opsional (Mis: PAI01)" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Kelompok Mapel</label>
                                <input type="text" name="kelompok_mapel" id="kelompok_mapel" placeholder="Misal: Muatan Lokal, Umum" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Nama Mata Pelajaran <span class="text-danger-500">*</span></label>
                            <input type="text" name="nama_mapel" id="nama_mapel" required placeholder="Contoh: Fiqih, Matematika, Nahwu" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>

                        <div class="flex items-center gap-2 mt-4 pt-2">
                            <input type="checkbox" name="is_active" id="is_active" value="1" checked class="rounded border-surface-300 text-primary-600 focus:ring-primary-500 w-4 h-4">
                            <label for="is_active" class="text-sm font-medium text-surface-700">Mapel Aktif (Dapat digunakan untuk penilaian)</label>
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
    const modal = document.getElementById('modal-mapel');
    const form = document.getElementById('form-mapel');
    const title = document.getElementById('modal-title');
    const methodInput = document.getElementById('form-method');

    function openModal() {
        form.reset();
        form.action = "{{ route('admin.mata-pelajaran.store') }}";
        methodInput.value = "POST";
        title.innerText = "Tambah Mata Pelajaran";
        document.getElementById('is_active').checked = true;
        modal.classList.remove('hidden');
    }

    function editMapel(data) {
        form.action = `/admin/mata-pelajaran/${data.id}`;
        methodInput.value = "PUT";
        title.innerText = "Edit Mapel: " + data.nama_mapel;
        
        document.getElementById('lembaga_id').value = data.lembaga_id;
        document.getElementById('kode_mapel').value = data.kode_mapel || '';
        document.getElementById('nama_mapel').value = data.nama_mapel;
        document.getElementById('kelompok_mapel').value = data.kelompok_mapel || '';
        document.getElementById('is_active').checked = data.is_active;
        
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }
</script>
@endpush
