@extends('layouts.app')

@section('title', 'Komponen Biaya')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Komponen Biaya</h1>
        <p class="text-sm text-surface-500 mt-1">Master data jenis tagihan atau biaya yang berlaku di pesantren.</p>
    </div>
    <button onclick="openModal()" class="btn-primary flex items-center gap-2">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Tambah Biaya</span>
    </button>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    {{-- Summary Cards --}}
    <div class="bg-white p-4 rounded-xl border border-surface-200 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
            <i data-lucide="calendar-clock" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-sm text-surface-500 font-medium">Biaya Bulanan</p>
            <h3 class="text-xl font-bold text-surface-900">{{ $komponenBiayas->where('jenis', 'BULANAN')->count() }} Item</h3>
        </div>
    </div>
    <div class="bg-white p-4 rounded-xl border border-surface-200 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
            <i data-lucide="calendar" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-sm text-surface-500 font-medium">Biaya Tahunan</p>
            <h3 class="text-xl font-bold text-surface-900">{{ $komponenBiayas->where('jenis', 'TAHUNAN')->count() }} Item</h3>
        </div>
    </div>
    <div class="bg-white p-4 rounded-xl border border-surface-200 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center">
            <i data-lucide="zap" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-sm text-surface-500 font-medium">Biaya Sekali Bayar</p>
            <h3 class="text-xl font-bold text-surface-900">{{ $komponenBiayas->where('jenis', 'SEKALI')->count() }} Item</h3>
        </div>
    </div>
</div>

<x-card :padding="false">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-surface-50/50 text-surface-600 border-b border-surface-100">
                <tr>
                    <th class="px-6 py-4 font-semibold">Nama Biaya</th>
                    <th class="px-6 py-4 font-semibold">Pesantren/Cabang</th>
                    <th class="px-6 py-4 font-semibold text-center">Jenis</th>
                    <th class="px-6 py-4 font-semibold text-right">Nominal (Rp)</th>
                    <th class="px-6 py-4 font-semibold text-center">Status</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100 text-surface-700">
                @forelse($komponenBiayas as $biaya)
                <tr class="hover:bg-surface-50/50 transition-colors">
                    <td class="px-6 py-4 font-bold text-surface-900">
                        {{ $biaya->nama }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $biaya->pesantren->nama ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex px-2.5 py-1 rounded-md text-xs font-bold
                            {{ $biaya->jenis == 'BULANAN' ? 'bg-blue-100 text-blue-700' : 
                               ($biaya->jenis == 'TAHUNAN' ? 'bg-emerald-100 text-emerald-700' : 'bg-purple-100 text-purple-700') }}">
                            {{ $biaya->jenis }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right font-mono font-bold text-primary-700">
                        {{ number_format($biaya->nominal, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($biaya->is_active)
                            <x-badge variant="success" dot>Aktif</x-badge>
                        @else
                            <x-badge variant="danger" dot>Nonaktif</x-badge>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="editBiaya({{ json_encode($biaya) }})" class="inline-flex text-primary-600 hover:text-primary-700 p-2 rounded-lg hover:bg-primary-50 transition-colors" title="Edit">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                        </button>
                        <form action="{{ route('bendahara.komponen-biaya.destroy', $biaya) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus komponen biaya ini?');">
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
                    <td colspan="6" class="px-6 py-12 text-center text-surface-500">
                        <div class="flex flex-col items-center justify-center">
                            <i data-lucide="wallet" class="w-12 h-12 text-surface-300 mb-3"></i>
                            <p class="font-medium text-surface-900 mb-1">Belum Ada Komponen Biaya</p>
                            <p class="text-sm">Silakan buat master data biaya untuk mulai menagih santri.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>

{{-- Modal Tambah / Edit --}}
<div id="modal-biaya" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-surface-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-visible rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                <div class="px-6 py-4 border-b border-surface-100 flex justify-between items-center bg-surface-50">
                    <h3 class="text-lg font-bold text-surface-900 font-heading" id="modal-title">Tambah Komponen Biaya</h3>
                    <button type="button" onclick="closeModal()" class="text-surface-400 hover:text-surface-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <form id="form-biaya" action="{{ route('bendahara.komponen-biaya.store') }}" method="POST">
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
                            <label class="block text-sm font-medium text-surface-700 mb-1">Nama Biaya <span class="text-danger-500">*</span></label>
                            <input type="text" name="nama" id="nama" required placeholder="Misal: SPP Bulanan, Uang Pangkal" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Jenis Siklus <span class="text-danger-500">*</span></label>
                                <select name="jenis" id="jenis" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                    <option value="BULANAN">Bulanan</option>
                                    <option value="TAHUNAN">Tahunan (Daftar Ulang)</option>
                                    <option value="SEKALI">Sekali Bayar (Masuk)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Nominal (Rp) <span class="text-danger-500">*</span></label>
                                <input type="number" name="nominal" id="nominal" required min="0" step="1000" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            </div>
                        </div>

                        <div class="flex items-center gap-2 mt-4 pt-2">
                            <input type="checkbox" name="is_active" id="is_active" value="1" checked class="rounded border-surface-300 text-primary-600 focus:ring-primary-500 w-4 h-4">
                            <label for="is_active" class="text-sm font-medium text-surface-700">Komponen Biaya Aktif</label>
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
    const modal = document.getElementById('modal-biaya');
    const form = document.getElementById('form-biaya');
    const title = document.getElementById('modal-title');
    const methodInput = document.getElementById('form-method');

    function openModal() {
        form.reset();
        form.action = "{{ route('bendahara.komponen-biaya.store') }}";
        methodInput.value = "POST";
        title.innerText = "Tambah Komponen Biaya";
        document.getElementById('is_active').checked = true;
        modal.classList.remove('hidden');
    }

    function editBiaya(data) {
        form.action = `/bendahara/komponen-biaya/${data.id}`;
        methodInput.value = "PUT";
        title.innerText = "Edit Komponen Biaya";
        
        document.getElementById('pesantren_id').value = data.pesantren_id;
        document.getElementById('nama').value = data.nama;
        document.getElementById('jenis').value = data.jenis;
        document.getElementById('nominal').value = Math.floor(data.nominal);
        document.getElementById('is_active').checked = data.is_active;
        
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }
</script>
@endpush
