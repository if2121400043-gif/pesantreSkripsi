@extends('layouts.panitia-psb')

@section('title', 'Gelombang PSB')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Gelombang Pendaftaran</h1>
        <p class="text-sm text-surface-500 mt-1">Kelola pembukaan dan penutupan Pendaftaran Santri Baru (PSB).</p>
    </div>
    <button onclick="openModal()" class="btn-primary flex items-center gap-2">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Buka Gelombang Baru</span>
    </button>
</div>
@endsection

@section('content')
<x-card :padding="false">
    <div class="p-4 border-b border-surface-100 bg-surface-50">
        <form action="{{ route('panitia-psb.gelombang.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
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
            @if(request()->filled('tahun_pelajaran_id'))
                <a href="{{ route('panitia-psb.gelombang.index') }}" class="btn-secondary px-4 py-2 text-danger-600 border-danger-200 hover:bg-danger-50 flex items-center justify-center">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-surface-50/50 text-surface-600 border-b border-surface-100">
                <tr>
                    <th class="px-6 py-4 font-semibold">Nama Gelombang</th>
                    <th class="px-6 py-4 font-semibold">Periode Pendaftaran</th>
                    <th class="px-6 py-4 font-semibold text-center">Kuota & Pendaftar</th>
                    <th class="px-6 py-4 font-semibold text-right">Biaya Daftar</th>
                    <th class="px-6 py-4 font-semibold text-center">Status</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100 text-surface-700">
                @forelse($gelombangs as $gelombang)
                <tr class="hover:bg-surface-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-surface-900">{{ $gelombang->nama }}</div>
                        <div class="text-xs text-surface-500 mt-0.5">T.A {{ $gelombang->tahunPelajaran->nama }} | {{ $gelombang->pesantren->nama ?? '' }}</div>
                    </td>
                    <td class="px-6 py-4 text-xs">
                        <div class="flex items-center gap-1.5 text-success-600 font-medium">
                            <i data-lucide="play-circle" class="w-3 h-3"></i> {{ \Carbon\Carbon::parse($gelombang->tanggal_buka)->format('d M Y') }}
                        </div>
                        <div class="flex items-center gap-1.5 text-danger-600 font-medium mt-1">
                            <i data-lucide="stop-circle" class="w-3 h-3"></i> {{ \Carbon\Carbon::parse($gelombang->tanggal_tutup)->format('d M Y') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="font-bold text-surface-900">{{ $gelombang->calon_santri_count }} / {{ $gelombang->kuota }}</div>
                        <div class="w-24 h-1.5 bg-surface-200 rounded-full mx-auto mt-1.5 overflow-hidden">
                            @php
                                $percent = $gelombang->kuota > 0 ? min(($gelombang->calon_santri_count / $gelombang->kuota) * 100, 100) : 0;
                            @endphp
                            <div class="{{ $percent >= 100 ? 'bg-danger-500' : 'bg-primary-500' }} h-full" style="width: {{ $percent }}%"></div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right font-mono font-medium text-surface-600">
                        {{ $gelombang->biaya_pendaftaran > 0 ? 'Rp ' . number_format($gelombang->biaya_pendaftaran, 0, ',', '.') : 'Gratis' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $now = now();
                            $buka = \Carbon\Carbon::parse($gelombang->tanggal_buka);
                            $tutup = \Carbon\Carbon::parse($gelombang->tanggal_tutup);
                        @endphp
                        
                        @if(!$gelombang->is_active)
                            <x-badge variant="danger" dot>Ditutup Manual</x-badge>
                        @elseif($now->lt($buka))
                            <x-badge variant="warning" dot>Segera Buka</x-badge>
                        @elseif($now->gt($tutup->endOfDay()))
                            <x-badge variant="surface" dot>Telah Berakhir</x-badge>
                        @else
                            <x-badge variant="success" dot>Sedang Berjalan</x-badge>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="editGelombang({{ json_encode($gelombang) }})" class="inline-flex text-primary-600 hover:text-primary-700 p-2 rounded-lg hover:bg-primary-50 transition-colors" title="Edit">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                        </button>
                        @if($gelombang->calon_santri_count == 0)
                            <form action="{{ route('panitia-psb.gelombang.destroy', $gelombang) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus gelombang ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-danger-500 hover:text-danger-600 p-2 rounded-lg hover:bg-danger-50 transition-colors" title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-surface-500">
                        <div class="flex flex-col items-center justify-center">
                            <i data-lucide="door-open" class="w-12 h-12 text-surface-300 mb-3"></i>
                            <p class="font-medium text-surface-900 mb-1">Belum Ada Gelombang Pendaftaran</p>
                            <p class="text-sm">Silakan buat gelombang untuk mulai menerima pendaftaran santri baru.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>

{{-- Modal --}}
<div id="modal-gelombang" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-surface-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-visible rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-xl">
                <div class="px-6 py-4 border-b border-surface-100 flex justify-between items-center bg-surface-50">
                    <h3 class="text-lg font-bold text-surface-900 font-heading" id="modal-title">Buka Gelombang Pendaftaran</h3>
                    <button type="button" onclick="closeModal()" class="text-surface-400 hover:text-surface-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <form id="form-gelombang" action="{{ route('panitia-psb.gelombang.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="form-method" value="POST">
                    
                    <div class="px-6 py-4 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Pesantren <span class="text-danger-500">*</span></label>
                                <select name="pesantren_id" id="pesantren_id" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                    @foreach($pesantrens as $pesantren)
                                        <option value="{{ $pesantren->id }}">{{ $pesantren->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Tahun Ajaran <span class="text-danger-500">*</span></label>
                                <select name="tahun_pelajaran_id" id="tahun_pelajaran_id" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                    @foreach($tahuns as $tahun)
                                        <option value="{{ $tahun->id }}">{{ $tahun->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Nama Gelombang <span class="text-danger-500">*</span></label>
                            <input type="text" name="nama" id="nama" required placeholder="Contoh: Gelombang 1 Jalur Prestasi" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Tanggal Buka <span class="text-danger-500">*</span></label>
                                <input type="date" name="tanggal_buka" id="tanggal_buka" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Tanggal Tutup <span class="text-danger-500">*</span></label>
                                <input type="date" name="tanggal_tutup" id="tanggal_tutup" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Mulai Seleksi</label>
                                <input type="date" name="tanggal_seleksi_awal" id="tanggal_seleksi_awal" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Selesai Seleksi</label>
                                <input type="date" name="tanggal_seleksi_akhir" id="tanggal_seleksi_akhir" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Mulai Daftar Ulang</label>
                                <input type="date" name="tanggal_daftar_ulang_awal" id="tanggal_daftar_ulang_awal" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Selesai Daftar Ulang</label>
                                <input type="date" name="tanggal_daftar_ulang_akhir" id="tanggal_daftar_ulang_akhir" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Kuota Maksimal <span class="text-danger-500">*</span></label>
                                <input type="number" name="kuota" id="kuota" required min="1" placeholder="Misal: 100" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Biaya Pendaftaran (Rp) <span class="text-danger-500">*</span></label>
                                <input type="number" name="biaya_pendaftaran" id="biaya_pendaftaran" required min="0" step="5000" placeholder="Ketik 0 jika gratis" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            </div>
                        </div>

                        <div class="flex items-center gap-2 mt-4 pt-2">
                            <input type="checkbox" name="is_active" id="is_active" value="1" checked class="rounded border-surface-300 text-primary-600 focus:ring-primary-500 w-4 h-4">
                            <label for="is_active" class="text-sm font-medium text-surface-700">Gelombang Aktif dan Terlihat oleh Pendaftar</label>
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
    const modal = document.getElementById('modal-gelombang');
    const form = document.getElementById('form-gelombang');
    const title = document.getElementById('modal-title');
    const methodInput = document.getElementById('form-method');

    function openModal() {
        form.reset();
        form.action = "{{ route('panitia-psb.gelombang.store') }}";
        methodInput.value = "POST";
        title.innerText = "Buka Gelombang Pendaftaran";
        document.getElementById('is_active').checked = true;
        modal.classList.remove('hidden');
    }

    function editGelombang(data) {
        form.action = `/panitia-psb/gelombang/${data.id}`;
        methodInput.value = "PUT";
        title.innerText = "Edit Gelombang: " + data.nama;
        
        document.getElementById('pesantren_id').value = data.pesantren_id;
        document.getElementById('tahun_pelajaran_id').value = data.tahun_pelajaran_id;
        document.getElementById('nama').value = data.nama;
        document.getElementById('tanggal_buka').value = data.tanggal_buka ? data.tanggal_buka.substring(0, 10) : '';
        document.getElementById('tanggal_tutup').value = data.tanggal_tutup ? data.tanggal_tutup.substring(0, 10) : '';
        document.getElementById('tanggal_seleksi_awal').value = data.tanggal_seleksi_awal ? data.tanggal_seleksi_awal.substring(0, 10) : '';
        document.getElementById('tanggal_seleksi_akhir').value = data.tanggal_seleksi_akhir ? data.tanggal_seleksi_akhir.substring(0, 10) : '';
        document.getElementById('tanggal_daftar_ulang_awal').value = data.tanggal_daftar_ulang_awal ? data.tanggal_daftar_ulang_awal.substring(0, 10) : '';
        document.getElementById('tanggal_daftar_ulang_akhir').value = data.tanggal_daftar_ulang_akhir ? data.tanggal_daftar_ulang_akhir.substring(0, 10) : '';
        document.getElementById('kuota').value = data.kuota;
        document.getElementById('biaya_pendaftaran').value = Math.floor(data.biaya_pendaftaran);
        document.getElementById('is_active').checked = data.is_active;
        
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }
</script>
@endpush
