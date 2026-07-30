@extends('layouts.app')

@section('title', 'Catatan Pelanggaran Santri')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Catatan Pelanggaran</h1>
        <p class="text-sm text-surface-500 mt-1">Kelola data pelanggaran dan takzir (hukuman) santri.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.jenis-pelanggaran.index') }}" class="btn-secondary flex items-center gap-2">
            <i data-lucide="settings" class="w-4 h-4"></i>
            <span>Master Aturan</span>
        </a>
        <button onclick="openModal()" class="btn-danger flex items-center gap-2">
            <i data-lucide="alert-triangle" class="w-4 h-4 text-white"></i>
            <span class="text-white">Input Pelanggaran</span>
        </button>
    </div>
</div>
@endsection

@section('content')
<x-card :padding="false">
    {{-- Search & Filter --}}
    <div class="p-4 border-b border-surface-100 bg-surface-50">
        <form action="{{ route('admin.pelanggaran.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama Santri atau NIUP..." 
                       class="w-full pl-10 pr-4 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors">
            </div>
            <div class="sm:w-64">
                <select name="tahun_pelajaran_id" class="w-full px-4 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" onchange="this.form.submit()">
                    <option value="">Semua Tahun Ajaran</option>
                    @foreach($tahuns as $tahun)
                        <option value="{{ $tahun->id }}" {{ request('tahun_pelajaran_id') == $tahun->id ? 'selected' : '' }}>
                            {{ $tahun->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-secondary px-4 py-2 hidden sm:block">Filter</button>
            @if(request()->anyFilled(['search', 'tahun_pelajaran_id']))
                <a href="{{ route('admin.pelanggaran.index') }}" class="btn-secondary px-4 py-2 text-danger-600 border-danger-200 hover:bg-danger-50 flex items-center justify-center">
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
                    <th class="px-6 py-4 font-semibold">Tgl Pelanggaran</th>
                    <th class="px-6 py-4 font-semibold">Nama Santri</th>
                    <th class="px-6 py-4 font-semibold">Bentuk Pelanggaran</th>
                    <th class="px-6 py-4 font-semibold">Tindakan / Takzir</th>
                    <th class="px-6 py-4 font-semibold text-center">Poin</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100 text-surface-700">
                @forelse($pelanggarans as $p)
                <tr class="hover:bg-surface-50/50 transition-colors">
                    <td class="px-6 py-4 text-surface-500">
                        {{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-surface-900">{{ $p->pesertaDidik->orang->nama_lengkap }}</div>
                        <div class="text-xs text-primary-600 font-mono mt-0.5">{{ $p->pesertaDidik->orang->niup }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-surface-900">{{ $p->jenisPelanggaran->nama }}</div>
                        <div class="text-[0.65rem] font-bold uppercase tracking-wider {{ 
                            $p->jenisPelanggaran->kategori === 'BERAT' ? 'text-danger-500' : 
                            ($p->jenisPelanggaran->kategori === 'SEDANG' ? 'text-warning-500' : 'text-surface-500') 
                        }} mt-0.5">
                            Kategori: {{ $p->jenisPelanggaran->kategori }}
                        </div>
                        @if($p->keterangan)
                            <div class="text-xs text-surface-500 mt-1 max-w-xs truncate" title="{{ $p->keterangan }}">Catatan: {{ $p->keterangan }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-surface-600">{{ $p->tindakan ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="font-mono font-bold text-danger-600">+{{ $p->jenisPelanggaran->poin }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <form action="{{ route('admin.pelanggaran.destroy', $p) }}" method="POST" onsubmit="return confirm('Hapus catatan pelanggaran ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-danger-500 hover:text-danger-700 p-2 rounded-lg hover:bg-danger-50 transition-colors" title="Hapus">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-surface-500">
                        <div class="flex flex-col items-center justify-center">
                            <i data-lucide="shield-check" class="w-12 h-12 text-success-300 mb-3"></i>
                            <p class="font-medium text-surface-900 mb-1">Bersih!</p>
                            <p class="text-sm">Belum ada catatan pelanggaran santri.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($pelanggarans->hasPages())
    <div class="p-4 border-t border-surface-100">
        {{ $pelanggarans->links() }}
    </div>
    @endif
</x-card>

{{-- Modal Input Pelanggaran --}}
<div id="modal-pelanggaran" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-surface-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-visible rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="px-6 py-4 border-b border-surface-100 flex justify-between items-center bg-danger-50">
                    <h3 class="text-lg font-bold text-danger-900 font-heading flex items-center gap-2">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-danger-500"></i> Input Pelanggaran
                    </h3>
                    <button type="button" onclick="closeModal()" class="text-surface-400 hover:text-surface-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <form action="{{ route('admin.pelanggaran.store') }}" method="POST">
                    @csrf
                    
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Cari & Pilih Santri <span class="text-danger-500">*</span></label>
                            {{-- For real app, use Select2 with AJAX. For MVP, we'll use a text input to simulate --}}
                            <div class="relative">
                                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400"></i>
                                <input type="text" id="search_santri" placeholder="Ketik nama atau NIUP (Min 3 huruf)..." class="w-full pl-10 pr-4 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            </div>
                            <div id="search_results" class="mt-1 bg-white border border-surface-200 rounded-lg shadow-lg max-h-48 overflow-y-auto hidden"></div>
                            
                            {{-- Hidden field to store selected ID --}}
                            <input type="hidden" name="peserta_didik_id" id="peserta_didik_id" required>
                            
                            <div id="selected_santri" class="mt-2 hidden flex justify-between items-center bg-success-50 border border-success-200 p-2 rounded-lg text-sm">
                                <span class="font-bold text-success-800" id="selected_santri_name"></span>
                                <button type="button" onclick="clearSantri()" class="text-success-600 hover:text-danger-600"><i data-lucide="x-circle" class="w-4 h-4"></i></button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Tahun Ajaran <span class="text-danger-500">*</span></label>
                                <select name="tahun_pelajaran_id" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                    @foreach($tahuns as $tahun)
                                        <option value="{{ $tahun->id }}" {{ $tahun->is_active ? 'selected' : '' }}>{{ $tahun->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Tgl Pelanggaran <span class="text-danger-500">*</span></label>
                                <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Aturan yang Dilanggar <span class="text-danger-500">*</span></label>
                            <select name="jenis_pelanggaran_id" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                <option value="" disabled selected>Pilih pelanggaran...</option>
                                @foreach($jenisList as $jenis)
                                    <option value="{{ $jenis->id }}">[{{ $jenis->kategori }}] {{ $jenis->nama }} (+{{ $jenis->poin }} poin)</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Tindakan / Takzir yang Diberikan</label>
                            <input type="text" name="tindakan" placeholder="Misal: Membaca Yasin 3x, Push up 50x" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Catatan Tambahan (Opsional)</label>
                            <textarea name="keterangan" rows="2" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500"></textarea>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-surface-50 border-t border-surface-100 flex justify-end gap-3">
                        <button type="button" onclick="closeModal()" class="btn-secondary">Batal</button>
                        <button type="submit" class="btn-primary bg-danger-600 hover:bg-danger-700 text-white">Simpan Pelanggaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openModal() {
        document.getElementById('modal-pelanggaran').classList.remove('hidden');
    }
    function closeModal() {
        document.getElementById('modal-pelanggaran').classList.add('hidden');
    }

    // Simple Vanilla JS AJAX Search for Santri
    const searchInput = document.getElementById('search_santri');
    const searchResults = document.getElementById('search_results');
    const hiddenId = document.getElementById('peserta_didik_id');
    const selectedSantri = document.getElementById('selected_santri');
    const selectedSantriName = document.getElementById('selected_santri_name');
    let timeout = null;

    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        const query = this.value;
        
        if (query.length < 3) {
            searchResults.classList.add('hidden');
            return;
        }
        
        timeout = setTimeout(() => {
            fetch(`/admin/api/search-santri?q=${query}`)
                .then(res => res.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    if (data.length === 0) {
                        searchResults.innerHTML = '<div class="p-3 text-sm text-surface-500">Santri tidak ditemukan</div>';
                    } else {
                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'p-3 text-sm hover:bg-surface-50 cursor-pointer border-b border-surface-100 last:border-0';
                            div.textContent = item.text;
                            div.onclick = function() {
                                hiddenId.value = item.id;
                                selectedSantriName.textContent = item.text;
                                selectedSantri.classList.remove('hidden');
                                searchInput.parentElement.classList.add('hidden');
                                searchResults.classList.add('hidden');
                            };
                            searchResults.appendChild(div);
                        });
                    }
                    searchResults.classList.remove('hidden');
                });
        }, 500);
    });

    function clearSantri() {
        hiddenId.value = '';
        searchInput.value = '';
        selectedSantri.classList.add('hidden');
        searchInput.parentElement.classList.remove('hidden');
        searchInput.focus();
    }
</script>
@endpush
@endsection
