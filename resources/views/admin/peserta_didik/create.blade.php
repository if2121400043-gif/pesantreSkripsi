@extends('layouts.app')

@section('title', 'Pendaftaran Santri Baru — PP Nurul Furqon')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Styling Select2 agar pas dengan Design System Tailwind */
    .select2-container--default .select2-selection--single {
        height: 2.75rem !important;
        border-radius: 0.75rem !important;
        border-color: #cbd5e1 !important;
        display: flex !important;
        align-items: center !important;
        padding-left: 0.5rem !important;
        background-color: #ffffff !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #0f172a !important;
        font-size: 0.875rem !important;
        font-weight: 500 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 2.75rem !important;
        right: 0.75rem !important;
    }
    .select2-dropdown {
        border-radius: 0.75rem !important;
        border-color: #cbd5e1 !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1) !important;
        overflow: hidden !important;
        z-index: 50 !important;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border-radius: 0.5rem !important;
        border-color: #cbd5e1 !important;
        padding: 0.5rem 0.75rem !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #047857 !important;
        color: #ffffff !important;
    }
</style>
@endpush

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2 text-xs text-surface-500 mb-1.5">
            <a href="{{ route('admin.peserta-didik.index') }}" class="hover:text-primary-600 transition-colors font-medium">Data Santri</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            <span class="text-surface-900 font-bold">Registrasi Akademik</span>
        </div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Daftarkan Santri / Siswa Baru</h1>
    </div>
    <a href="{{ route('admin.peserta-didik.index') }}" class="btn-secondary flex items-center gap-2 text-xs font-bold py-2 px-4 rounded-xl">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Kembali ke Daftar Santri</span>
    </a>
</div>
@endsection

@section('content')
<form action="{{ route('admin.peserta-didik.store') }}" method="POST" class="max-w-3xl mx-auto space-y-6">
    @csrf

    @if($errors->any())
        <div class="bg-danger-50 text-danger-800 p-4 rounded-2xl border border-danger-200 shadow-sm">
            <div class="flex gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-danger-600 flex-shrink-0 mt-0.5"></i>
                <ul class="list-disc pl-5 space-y-1 text-xs font-medium">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- STEP 1: PILIH IDENTITAS INDUK (ORANG) --}}
    <div class="bg-white rounded-3xl p-6 border border-surface-200 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-primary-50 rounded-bl-full -z-10"></div>
        
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-2xl bg-primary-100 text-primary-700 flex items-center justify-center font-bold shrink-0">
                <i data-lucide="user-check" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-surface-900">1. Pilih Identitas Induk (Orang)</h3>
                <p class="text-xs text-surface-500">Cari dari daftar orang yang terdaftar di database tetapi belum menjadi santri.</p>
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <label for="orang_id" class="block text-xs font-bold text-surface-700">
                    Cari Nama / NIUP / NIK Santri <span class="text-danger-500">*</span>
                </label>

                <a href="{{ route('admin.orang.create') }}" class="inline-flex items-center gap-1 text-xs font-bold text-primary-700 hover:text-primary-800 hover:underline">
                    <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                    <span>+ Buat Identitas Baru</span>
                </a>
            </div>

            <div class="relative">
                <select name="orang_id" id="orang_id" required class="select2-orang w-full">
                    <option value="" disabled selected>Ketik nama, NIUP, atau NIK santri...</option>
                    @foreach($calonPeserta as $orang)
                        <option value="{{ $orang->id }}" {{ (old('orang_id') == $orang->id || $selectedOrangId == $orang->id) ? 'selected' : '' }}>
                            {{ $orang->nama_lengkap }} — (NIUP: {{ $orang->niup ?? 'Belum ada' }}) [{{ $orang->jenis_kelamin == 'L' ? 'Putra' : 'Putri' }}]
                        </option>
                    @endforeach
                </select>
            </div>

            <p class="text-[0.7rem] text-surface-450 flex items-center gap-1">
                <i data-lucide="info" class="w-3.5 h-3.5 text-primary-600"></i>
                Setiap santri wajib memiliki record Identitas Induk (NIUP) sebelum didaftarkan secara akademik.
            </p>
        </div>
    </div>

    {{-- STEP 2: DATA AKADEMIK --}}
    <div class="bg-white rounded-3xl p-6 border border-surface-200 shadow-sm relative overflow-hidden">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-2xl bg-success-100 text-success-700 flex items-center justify-center font-bold shrink-0">
                <i data-lucide="graduation-cap" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-surface-900">2. Data Akademik Santri</h3>
                <p class="text-xs text-surface-500">Lengkapi nomor induk lokal/nasional dan status keaktifan akademik.</p>
            </div>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1">Nomor Induk Siswa (NIS Lokal)</label>
                    <input type="text" name="nis" value="{{ old('nis') }}" placeholder="Kosongkan jika belum ada" class="w-full rounded-xl border border-surface-300 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1">NISN (Nasional)</label>
                    <input type="text" name="nisn" value="{{ old('nisn') }}" placeholder="10 Digit Angka" maxlength="10" class="w-full rounded-xl border border-surface-300 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1">Tanggal Masuk / Diterima <span class="text-danger-500">*</span></label>
                    <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required class="w-full rounded-xl border border-surface-300 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1">Status Akademik <span class="text-danger-500">*</span></label>
                    <select name="status" required class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-sm font-semibold text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        <option value="AKTIF" selected>AKTIF (Belajar)</option>
                        <option value="MUTASI_KELUAR">MUTASI (Pindahan dari sini)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-surface-700 mb-1">Catatan Tambahan</label>
                <textarea name="catatan" rows="3" placeholder="Tambahkan catatan khusus riwayat santri (opsional)..." class="w-full rounded-xl border border-surface-300 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">{{ old('catatan') }}</textarea>
            </div>
        </div>
        
        <div class="mt-6 pt-5 border-t border-surface-100 flex items-center justify-end gap-3">
            <a href="{{ route('admin.peserta-didik.index') }}" class="btn-secondary text-xs font-bold py-2.5 px-5 rounded-xl">Batal</a>
            <button type="submit" class="btn-primary text-xs font-bold py-2.5 px-6 rounded-xl flex items-center gap-2 shadow-md shadow-primary-700/20">
                <i data-lucide="user-check" class="w-4 h-4"></i>
                <span>Daftarkan Santri Baru</span>
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-orang').select2({
            placeholder: 'Ketik nama, NIUP, atau NIK santri...',
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush
