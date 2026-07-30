@extends('layouts.app')

@section('title', 'Perizinan Keluar Santri')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Perizinan Keluar Pesantren</h1>
        <p class="text-sm text-surface-500 mt-1">Sistem perizinan pulang, keluar sementara, dan sakit.</p>
    </div>
    <button onclick="openModal()" class="btn-primary flex items-center gap-2">
        <i data-lucide="door-open" class="w-4 h-4"></i>
        <span>Buat Surat Izin</span>
    </button>
</div>
@endsection

@section('content')
<x-card :padding="false">
    {{-- Search & Filter --}}
    <div class="p-4 border-b border-surface-100 bg-surface-50">
        <form action="{{ route('admin.perizinan.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama Santri atau NIUP..." 
                       class="w-full pl-10 pr-4 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors">
            </div>
            <div class="sm:w-48">
                <select name="status" class="w-full px-4 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="MENUNGGU" {{ request('status') == 'MENUNGGU' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                    <option value="DISETUJUI" {{ request('status') == 'DISETUJUI' ? 'selected' : '' }}>Sedang Berizin (Keluar)</option>
                    <option value="SELESAI" {{ request('status') == 'SELESAI' ? 'selected' : '' }}>Selesai (Sudah Kembali)</option>
                    <option value="DITOLAK" {{ request('status') == 'DITOLAK' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <button type="submit" class="btn-secondary px-4 py-2 hidden sm:block">Filter</button>
            @if(request()->anyFilled(['search', 'status']))
                <a href="{{ route('admin.perizinan.index') }}" class="btn-secondary px-4 py-2 text-danger-600 border-danger-200 hover:bg-danger-50 flex items-center justify-center">
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
                    <th class="px-6 py-4 font-semibold">Tgl / Waktu Keluar</th>
                    <th class="px-6 py-4 font-semibold">Nama Santri</th>
                    <th class="px-6 py-4 font-semibold">Jenis Izin & Alasan</th>
                    <th class="px-6 py-4 font-semibold">Batas Kembali</th>
                    <th class="px-6 py-4 font-semibold text-center">Status</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100 text-surface-700">
                @forelse($perizinans as $izin)
                <tr class="hover:bg-surface-50/50 transition-colors">
                    <td class="px-6 py-4 text-surface-500">
                        <div class="font-bold text-surface-900">{{ \Carbon\Carbon::parse($izin->waktu_keluar)->format('d/m/Y') }}</div>
                        <div class="text-xs">{{ \Carbon\Carbon::parse($izin->waktu_keluar)->format('H:i') }} WIB</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-surface-900">{{ $izin->pesertaDidik->orang->nama_lengkap }}</div>
                        <div class="text-xs text-primary-600 font-mono mt-0.5">{{ $izin->pesertaDidik->orang->niup }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-surface-900">{{ str_replace('_', ' ', $izin->jenis) }}</div>
                        @if($izin->alasan)
                            <div class="text-xs text-surface-500 mt-1 max-w-[200px] truncate" title="{{ $izin->alasan }}">Alasan: {{ $izin->alasan }}</div>
                        @endif
                        @if($izin->dijemput_oleh)
                            <div class="text-xs text-surface-500 mt-0.5">Penjemput: {{ $izin->dijemput_oleh }} ({{ $izin->hubungan_penjemput }})</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-surface-500">
                        @if($izin->waktu_kembali_rencana)
                            <div class="{{ \Carbon\Carbon::parse($izin->waktu_kembali_rencana)->isPast() && $izin->status === 'DISETUJUI' ? 'text-danger-600 font-bold' : '' }}">
                                {{ \Carbon\Carbon::parse($izin->waktu_kembali_rencana)->format('d/m/Y H:i') }}
                            </div>
                        @else
                            -
                        @endif
                        
                        @if($izin->waktu_kembali_aktual)
                            <div class="text-xs text-success-600 font-bold mt-1">
                                Kembali: {{ \Carbon\Carbon::parse($izin->waktu_kembali_aktual)->format('d/m/Y H:i') }}
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($izin->status === 'MENUNGGU')
                            <x-badge variant="warning" dot>Menunggu Persetujuan</x-badge>
                        @elseif($izin->status === 'DISETUJUI')
                            <x-badge variant="primary" dot>Sedang Berizin</x-badge>
                        @elseif($izin->status === 'SELESAI')
                            <x-badge variant="success" dot>Selesai (Kembali)</x-badge>
                        @else
                            <x-badge variant="danger" dot>Ditolak</x-badge>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if($izin->status === 'MENUNGGU')
                                <form action="{{ route('admin.perizinan.update', $izin) }}" method="POST" class="inline-block">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn-primary py-1 px-2 text-xs" title="Setujui Izin">
                                        <i data-lucide="check" class="w-3 h-3"></i> Setujui
                                    </button>
                                </form>
                                <form action="{{ route('admin.perizinan.update', $izin) }}" method="POST" class="inline-block">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn-secondary text-danger-600 py-1 px-2 text-xs" title="Tolak">
                                        <i data-lucide="x" class="w-3 h-3"></i>
                                    </button>
                                </form>
                            @elseif($izin->status === 'DISETUJUI')
                                <form action="{{ route('admin.perizinan.update', $izin) }}" method="POST" class="inline-block">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="action" value="return">
                                    <button type="submit" class="btn-secondary text-success-600 border-success-200 hover:bg-success-50 py-1 px-2 text-xs" onclick="return confirm('Catat santri ini sudah kembali ke asrama?');">
                                        <i data-lucide="log-in" class="w-3 h-3 mr-1"></i> Santri Kembali
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.perizinan.destroy', $izin) }}" method="POST" onsubmit="return confirm('Hapus riwayat izin ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-surface-400 hover:text-danger-600 transition-colors" title="Hapus Riwayat">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-surface-500">
                        <div class="flex flex-col items-center justify-center">
                            <i data-lucide="door-open" class="w-12 h-12 text-surface-300 mb-3"></i>
                            <p class="font-medium text-surface-900 mb-1">Belum Ada Permohonan Izin</p>
                            <p class="text-sm">Data perizinan santri akan tampil di sini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($perizinans->hasPages())
    <div class="p-4 border-t border-surface-100">
        {{ $perizinans->links() }}
    </div>
    @endif
</x-card>

{{-- Modal Pengajuan Izin --}}
<div id="modal-izin" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-surface-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-visible rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="px-6 py-4 border-b border-surface-100 flex justify-between items-center bg-surface-50">
                    <h3 class="text-lg font-bold text-surface-900 font-heading flex items-center gap-2">
                        <i data-lucide="door-open" class="w-5 h-5 text-primary-500"></i> Buat Surat Izin
                    </h3>
                    <button type="button" onclick="closeModal()" class="text-surface-400 hover:text-surface-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <form action="{{ route('admin.perizinan.store') }}" method="POST">
                    @csrf
                    
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Cari & Pilih Santri <span class="text-danger-500">*</span></label>
                            <div class="relative">
                                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400"></i>
                                <input type="text" id="search_santri" placeholder="Ketik nama atau NIUP..." class="w-full pl-10 pr-4 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            </div>
                            <div id="search_results" class="mt-1 bg-white border border-surface-200 rounded-lg shadow-lg max-h-48 overflow-y-auto hidden"></div>
                            
                            <input type="hidden" name="peserta_didik_id" id="peserta_didik_id" required>
                            
                            <div id="selected_santri" class="mt-2 hidden flex justify-between items-center bg-success-50 border border-success-200 p-2 rounded-lg text-sm">
                                <span class="font-bold text-success-800" id="selected_santri_name"></span>
                                <button type="button" onclick="clearSantri()" class="text-success-600 hover:text-danger-600"><i data-lucide="x-circle" class="w-4 h-4"></i></button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Jenis Izin <span class="text-danger-500">*</span></label>
                            <select name="jenis" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                <option value="PULANG">Pulang Kampung (Libur/Cuti)</option>
                                <option value="KELUAR_SEMENTARA">Keluar Pesantren Sementara (Belanja, dll)</option>
                                <option value="SAKIT">Izin Berobat / Sakit</option>
                                <option value="KEPERLUAN_KHUSUS">Keperluan Keluarga / Khusus</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Rencana Keluar <span class="text-danger-500">*</span></label>
                                <input type="datetime-local" name="waktu_keluar" required value="{{ date('Y-m-d\TH:i') }}" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Rencana Kembali (Opsional)</label>
                                <input type="datetime-local" name="waktu_kembali_rencana" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Nama Penjemput (Jika Ada)</label>
                                <input type="text" name="dijemput_oleh" placeholder="Nama wali/kerabat" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Hubungan dengan Santri</label>
                                <input type="text" name="hubungan_penjemput" placeholder="Ayah, Paman, Kakak" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Alasan Izin <span class="text-danger-500">*</span></label>
                            <textarea name="alasan" rows="2" required placeholder="Jelaskan secara singkat..." class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500"></textarea>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-surface-50 border-t border-surface-100 flex justify-end gap-3">
                        <button type="button" onclick="closeModal()" class="btn-secondary">Batal</button>
                        <button type="submit" class="btn-primary">Ajukan Izin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openModal() {
        document.getElementById('modal-izin').classList.remove('hidden');
    }
    function closeModal() {
        document.getElementById('modal-izin').classList.add('hidden');
    }

    // Reuse the exact same search logic from Pelanggaran
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
