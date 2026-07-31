@extends('layouts.app')

@section('title', 'Tambah Relasi Keluarga — PP Nurul Furqon')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
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
        font-size: 0.8125rem !important;
        font-weight: 600 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 2.75rem !important;
        right: 0.75rem !important;
    }
    .select2-dropdown {
        border-radius: 0.75rem !important;
        border-color: #cbd5e1 !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15) !important;
        overflow: hidden !important;
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
            <a href="{{ route('admin.keluarga.index') }}" class="hover:text-primary-600 transition-colors font-medium">Relasi Keluarga</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            <span class="text-surface-900 font-bold">Tambah Relasi</span>
        </div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Tambah Relasi Keluarga & Wali</h1>
    </div>
    <a href="{{ route('admin.keluarga.index') }}" class="btn-secondary flex items-center gap-2 text-xs font-bold py-2.5 px-4 rounded-xl">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Kembali ke Daftar</span>
    </a>
</div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6 pb-12">

    {{-- Notifikasi Error Global --}}
    @if($errors->any())
        <div class="bg-danger-50 text-danger-800 p-4 rounded-2xl border border-danger-200 shadow-sm">
            <div class="flex gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-danger-600 flex-shrink-0 mt-0.5"></i>
                <div>
                    <h3 class="font-bold text-xs mb-1">Terdapat kesalahan pengisian:</h3>
                    <ul class="list-disc pl-5 space-y-1 text-xs font-medium">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.keluarga.store') }}" method="POST">
        @csrf

        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-surface-200 shadow-sm space-y-6">
            
            {{-- Target Santri / Anak --}}
            <div>
                <label class="block text-xs font-bold text-surface-700 mb-1.5">Pilih Identitas Santri / Anak <span class="text-danger-500">*</span></label>
                <select name="orang_id" id="orang_id" required class="select2-search w-full">
                    <option value="" disabled selected>Ketik nama atau NIUP santri/anak...</option>
                    @foreach($semuaOrang as $o)
                        <option value="{{ $o->id }}" {{ old('orang_id') == $o->id ? 'selected' : '' }}>{{ $o->nama_lengkap }} — (NIUP: {{ $o->niup }})</option>
                    @endforeach
                </select>
            </div>

            {{-- Mode Wali (Existing vs Input Wali Baru) --}}
            <div class="space-y-4">
                <label class="block text-xs font-bold text-surface-700">Pilih Opsi Data Wali <span class="text-danger-500">*</span></label>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="flex items-center gap-3 p-4 rounded-2xl border border-surface-200 bg-surface-50 cursor-pointer hover:border-primary-500 transition-colors">
                        <input type="radio" name="mode_wali" value="existing" checked onchange="toggleModeWali()" class="text-primary-600 focus:ring-primary-500 w-4 h-4">
                        <div>
                            <span class="font-bold text-surface-900 text-xs block">Pilih Wali dari Data Master</span>
                            <span class="text-[0.68rem] text-surface-500">Wali sudah terdaftar di database pesantren.</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-4 rounded-2xl border border-surface-200 bg-surface-50 cursor-pointer hover:border-primary-500 transition-colors">
                        <input type="radio" name="mode_wali" value="new" onchange="toggleModeWali()" class="text-primary-600 focus:ring-primary-500 w-4 h-4">
                        <div>
                            <span class="font-bold text-surface-900 text-xs block">+ Input Wali Baru</span>
                            <span class="text-[0.68rem] text-surface-500">Buatkan identitas & akun portal wali baru secara otomatis.</span>
                        </div>
                    </label>
                </div>

                {{-- Mode Existing --}}
                <div id="mode_existing">
                    <label class="block text-xs font-bold text-surface-700 mb-1.5">Pilih Identitas Wali <span class="text-danger-500">*</span></label>
                    <select name="keluarga_id" id="keluarga_id" class="select2-search w-full">
                        <option value="" disabled selected>Ketik nama atau NIUP wali...</option>
                        @foreach($semuaOrang as $o)
                            <option value="{{ $o->id }}" {{ old('keluarga_id') == $o->id ? 'selected' : '' }}>{{ $o->nama_lengkap }} — (NIUP: {{ $o->niup }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Mode New Wali --}}
                <div id="mode_new" class="hidden space-y-4 bg-emerald-50/70 p-5 rounded-3xl border border-emerald-200">
                    <div class="flex items-center gap-2 text-emerald-900 font-extrabold text-xs mb-2">
                        <i data-lucide="user-plus" class="w-4 h-4 text-emerald-700"></i>
                        <span>Input Identitas Wali Baru</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-surface-700 mb-1">Nama Lengkap Wali <span class="text-danger-500">*</span></label>
                        <input type="text" name="nama_wali" id="nama_wali" value="{{ old('nama_wali') }}" placeholder="Nama lengkap wali..." class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-surface-700 mb-1">Nomor WA / HP Aktif <span class="text-danger-500">*</span></label>
                            <input type="text" name="telepon_wali" id="telepon_wali" value="{{ old('telepon_wali') }}" placeholder="08xxxxxxxxxx" class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            <p class="text-[0.68rem] text-emerald-800 font-semibold mt-1"><i data-lucide="info" class="w-3 h-3 inline"></i> Nomor ini akan menjadi Username & Password login portal.</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-surface-700 mb-1">Email Wali (Opsional)</label>
                            <input type="email" name="email_wali" id="email_wali" value="{{ old('email_wali') }}" placeholder="wali@example.com" class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-surface-700 mb-1">Alamat Lengkap (Opsional)</label>
                        <textarea name="alamat_wali" rows="2" placeholder="Alamat domisili..." class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">{{ old('alamat_wali') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Hubungan --}}
            <div>
                <label class="block text-xs font-bold text-surface-700 mb-1.5">Status Hubungan <span class="text-danger-500">*</span></label>
                <select name="hubungan" id="hubungan" required class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-semibold focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    <option value="AYAH" {{ old('hubungan') == 'AYAH' ? 'selected' : '' }}>Ayah Kandung</option>
                    <option value="IBU" {{ old('hubungan') == 'IBU' ? 'selected' : '' }}>Ibu Kandung</option>
                    <option value="WALI" {{ old('hubungan') == 'WALI' ? 'selected' : '' }}>Wali (Selain Orang Tua)</option>
                    <option value="KAKAK" {{ old('hubungan') == 'KAKAK' ? 'selected' : '' }}>Kakak</option>
                    <option value="ADIK" {{ old('hubungan') == 'ADIK' ? 'selected' : '' }}>Adik</option>
                    <option value="PAMAN" {{ old('hubungan') == 'PAMAN' ? 'selected' : '' }}>Paman (Pakde/Om)</option>
                    <option value="BIBI" {{ old('hubungan') == 'BIBI' ? 'selected' : '' }}>Bibi (Bude/Tante)</option>
                    <option value="KAKEK" {{ old('hubungan') == 'KAKEK' ? 'selected' : '' }}>Kakek</option>
                    <option value="NENEK" {{ old('hubungan') == 'NENEK' ? 'selected' : '' }}>Nenek</option>
                    <option value="LAINNYA" {{ old('hubungan') == 'LAINNYA' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            {{-- Box Hak Akses & Syariat --}}
            <div class="bg-surface-50 p-5 border border-surface-200 rounded-3xl space-y-4">
                <p class="font-bold text-surface-900 text-xs border-b border-surface-200 pb-2.5">Hak Akses & Aturan Syariat Pesantren</p>
                
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="is_mahrom" id="is_mahrom" value="1" {{ old('is_mahrom') ? 'checked' : '' }} class="mt-0.5 rounded border-surface-300 text-primary-600 focus:ring-primary-500 w-4 h-4">
                    <div>
                        <span class="font-bold text-surface-900 text-xs">Mahram secara Syariat</span>
                        <p class="text-[0.65rem] text-surface-500">Mempengaruhi aturan perizinan pertemuan santri putri dengan wali laki-laki.</p>
                    </div>
                </label>
                
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_wali_utama" id="is_wali_utama" value="1" {{ old('is_wali_utama') ? 'checked' : '' }} class="rounded border-surface-300 text-primary-600 focus:ring-primary-500 w-4 h-4">
                    <span class="font-bold text-surface-900 text-xs">Jadikan Wali Utama (Penanggung Jawab Keuangan & Akademik)</span>
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-3 border-t border-surface-200">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="boleh_jemput" id="boleh_jemput" value="1" {{ old('boleh_jemput', '1') == '1' ? 'checked' : '' }} class="rounded border-surface-300 text-primary-600 focus:ring-primary-500 w-4 h-4">
                        <span class="text-xs font-bold text-surface-900">Boleh Menjemput</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="boleh_kunjungi" id="boleh_kunjungi" value="1" {{ old('boleh_kunjungi', '1') == '1' ? 'checked' : '' }} class="rounded border-surface-300 text-primary-600 focus:ring-primary-500 w-4 h-4">
                        <span class="text-xs font-bold text-surface-900">Boleh Kunjungan</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="boleh_komunikasi" id="boleh_komunikasi" value="1" {{ old('boleh_komunikasi', '1') == '1' ? 'checked' : '' }} class="rounded border-surface-300 text-primary-600 focus:ring-primary-500 w-4 h-4">
                        <span class="text-xs font-bold text-surface-900">Boleh Komunikasi</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-surface-700 mb-1.5">Catatan Tambahan</label>
                <input type="text" name="catatan" id="catatan" value="{{ old('catatan') }}" placeholder="Catatan khusus perizinan atau hubungan..." class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
            </div>

            {{-- Submit & Cancel Buttons --}}
            <div class="pt-4 border-t border-surface-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.keluarga.index') }}" class="btn-secondary text-xs font-bold py-2.5 px-5 rounded-xl">
                    Batal
                </a>
                <button type="submit" class="btn-primary text-xs font-bold py-2.5 px-6 rounded-xl shadow-md flex items-center gap-2" style="color: #ffffff !important; background-color: #047857 !important;">
                    <i data-lucide="save" class="w-4 h-4 text-white" style="color: #ffffff !important;"></i>
                    <span style="color: #ffffff !important;">Simpan Relasi Keluarga</span>
                </button>
            </div>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-search').select2({
            placeholder: 'Ketik nama atau NIUP...',
            allowClear: true,
            width: '100%'
        });
    });

    function toggleModeWali() {
        const mode = document.querySelector('input[name="mode_wali"]:checked');
        const isNew = mode && mode.value === 'new';
        
        document.getElementById('mode_existing').style.display = isNew ? 'none' : 'block';
        document.getElementById('mode_new').style.display = isNew ? 'block' : 'none';
        
        document.getElementById('keluarga_id').required = !isNew;
        document.getElementById('nama_wali').required = isNew;
        document.getElementById('telepon_wali').required = isNew;
    }
</script>
@endpush
