@extends('layouts.app')

@section('title', 'Catat Kedisiplinan & Prestasi')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Styling for Select2 to match our theme */
    .select2-container--default .select2-selection--single {
        height: 2.75rem;
        border-radius: 0.5rem;
        border-color: #cbd5e1;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100%;
    }
    .select2-container--default .select2-selection--single:focus {
        border-color: #0ea5e9;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.2);
    }
</style>
@endpush

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Kedisiplinan & Prestasi</h1>
        <p class="text-sm text-surface-500 mt-1">Catat poin pelanggaran atau apresiasi prestasi santri.</p>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Form Pelanggaran --}}
    <x-card title="Lapor Pelanggaran">
        <form action="{{ route('guru.pelanggaran.store') }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Pilih Santri <span class="text-danger-500">*</span></label>
                    <select name="peserta_didik_id" class="select2-santri w-full" required>
                        <option value="">Cari nama atau NISN...</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Tanggal Kejadian <span class="text-danger-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-surface-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500/20" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Jenis Pelanggaran <span class="text-danger-500">*</span></label>
                    <select name="jenis_pelanggaran_id" class="w-full rounded-lg border-surface-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500/20" required>
                        <option value="">-- Pilih Jenis Pelanggaran --</option>
                        @foreach($jenisPelanggaran as $jp)
                            <option value="{{ $jp->id }}">{{ $jp->nama }} (Poin: {{ $jp->poin }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Keterangan / Kronologi <span class="text-danger-500">*</span></label>
                    <textarea name="keterangan" rows="3" class="w-full rounded-lg border-surface-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500/20" required placeholder="Jelaskan detail kejadian..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Tindakan / Sanksi (Opsional)</label>
                    <input type="text" name="tindakan" class="w-full rounded-lg border-surface-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500/20" placeholder="Misal: Diberi teguran lisan">
                </div>

                <div class="pt-4 border-t border-surface-200">
                    <button type="submit" class="w-full flex justify-center items-center gap-2 px-4 py-2.5 bg-danger-600 text-white font-bold rounded-xl hover:bg-danger-700 transition-colors shadow-md shadow-danger-500/20">
                        <i data-lucide="shield-alert" class="w-5 h-5"></i> Simpan Catatan Pelanggaran
                    </button>
                </div>
            </div>
        </form>

        {{-- Riwayat --}}
        @if($riwayatPelanggaran->count() > 0)
            <div class="mt-8 border-t border-surface-200 pt-6">
                <h4 class="text-sm font-bold text-surface-900 mb-3">Terakhir Anda Catat</h4>
                <div class="space-y-3">
                    @foreach($riwayatPelanggaran as $riwayat)
                        <div class="text-sm p-3 rounded-lg border border-surface-200 bg-surface-50 flex justify-between items-center">
                            <div>
                                <p class="font-bold text-surface-900">{{ $riwayat->pesertaDidik->orang->nama ?? '-' }}</p>
                                <p class="text-xs text-surface-500">{{ $riwayat->jenisPelanggaran->nama ?? '-' }}</p>
                            </div>
                            <span class="text-xs font-medium text-surface-400">{{ \Carbon\Carbon::parse($riwayat->tanggal)->format('d M Y') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </x-card>

    {{-- Form Prestasi --}}
    <x-card title="Catat Prestasi">
        <form action="{{ route('guru.prestasi.store') }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Pilih Santri <span class="text-danger-500">*</span></label>
                    <select name="peserta_didik_id" class="select2-santri w-full" required>
                        <option value="">Cari nama atau NISN...</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Tanggal <span class="text-danger-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-surface-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500/20" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Nama Lomba / Prestasi <span class="text-danger-500">*</span></label>
                    <input type="text" name="nama_prestasi" class="w-full rounded-lg border-surface-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500/20" required placeholder="Juara 1 MTQ...">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Tingkat <span class="text-danger-500">*</span></label>
                        <select name="tingkat" class="w-full rounded-lg border-surface-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500/20" required>
                            <option value="Internal">Internal (Sekolah)</option>
                            <option value="Kabupaten/Kota">Kabupaten/Kota</option>
                            <option value="Provinsi">Provinsi</option>
                            <option value="Nasional">Nasional</option>
                            <option value="Internasional">Internasional</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Penyelenggara <span class="text-danger-500">*</span></label>
                        <input type="text" name="penyelenggara" class="w-full rounded-lg border-surface-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500/20" required placeholder="Misal: Kemenag">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Keterangan Tambahan (Opsional)</label>
                    <textarea name="keterangan" rows="2" class="w-full rounded-lg border-surface-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500/20" placeholder="Detail prestasi..."></textarea>
                </div>

                <div class="pt-4 border-t border-surface-200">
                    <button type="submit" class="w-full flex justify-center items-center gap-2 px-4 py-2.5 bg-warning-500 text-white font-bold rounded-xl hover:bg-warning-600 transition-colors shadow-md shadow-warning-500/20">
                        <i data-lucide="trophy" class="w-5 h-5"></i> Simpan Catatan Prestasi
                    </button>
                </div>
            </div>
        </form>

        {{-- Riwayat --}}
        @if($riwayatPrestasi->count() > 0)
            <div class="mt-8 border-t border-surface-200 pt-6">
                <h4 class="text-sm font-bold text-surface-900 mb-3">Terakhir Anda Catat</h4>
                <div class="space-y-3">
                    @foreach($riwayatPrestasi as $riwayat)
                        <div class="text-sm p-3 rounded-lg border border-surface-200 bg-surface-50 flex justify-between items-center">
                            <div>
                                <p class="font-bold text-surface-900">{{ $riwayat->pesertaDidik->orang->nama ?? '-' }}</p>
                                <p class="text-xs text-surface-500">{{ $riwayat->nama_prestasi }}</p>
                            </div>
                            <span class="text-xs font-medium text-surface-400">{{ \Carbon\Carbon::parse($riwayat->tanggal)->format('d M Y') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </x-card>

</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-santri').select2({
            placeholder: 'Ketik nama atau NISN santri...',
            allowClear: true,
            ajax: {
                url: '{{ route("guru.api.search-santri") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term // search term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });
    });
</script>
@endpush
