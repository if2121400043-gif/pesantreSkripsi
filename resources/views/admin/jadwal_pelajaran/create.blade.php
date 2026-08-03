@extends('layouts.app')

@section('title', 'Tambah Jadwal Pelajaran Baru — PP Nurul Furqon')

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
            <a href="{{ route('admin.jadwal-pelajaran.index', ['rombel_id' => $rombelId, 'tahun_pelajaran_id' => $tahunId]) }}" class="hover:text-primary-600 transition-colors font-medium">Jadwal Pelajaran</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            <span class="text-surface-900 font-bold">Tambah Jadwal Sesi</span>
        </div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Tambah Jadwal Pelajaran</h1>
    </div>
    <a href="{{ route('admin.jadwal-pelajaran.index', ['rombel_id' => $rombelId, 'tahun_pelajaran_id' => $tahunId]) }}" class="btn-secondary flex items-center gap-2 text-xs font-bold py-2.5 px-4 rounded-xl">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Kembali ke Jadwal</span>
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

    <form action="{{ route('admin.jadwal-pelajaran.store') }}" method="POST">
        @csrf

        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-surface-200 shadow-sm space-y-6">
            
            {{-- Header Info Kelas --}}
            <div class="flex items-center gap-3.5 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl">
                <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold shrink-0 shadow-sm" style="background-color: #047857 !important; color: #ffffff !important;">
                    <i data-lucide="school" class="w-5 h-5"></i>
                </div>
                <div>
                    <div class="text-[0.68rem] text-emerald-800 font-extrabold uppercase tracking-wider">Target Kelas & Rombel</div>
                    <div class="text-sm font-extrabold text-emerald-950 font-heading">
                        @if($rombel)
                            {{ $rombel->lembaga->singkatan ?? $rombel->lembaga->nama }} — {{ $rombel->tingkat ? $rombel->tingkat . '-' : '' }}{{ $rombel->nama }}
                        @else
                            Silakan Pilih Kelas
                        @endif
                    </div>
                </div>
            </div>

            {{-- Input Hidden atau Select Kelas --}}
            @if($rombel)
                <input type="hidden" name="rombel_id" value="{{ $rombel->id }}">
            @else
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1.5">Pilih Kelas / Rombel <span class="text-danger-500">*</span></label>
                    <select name="rombel_id" required class="select2-search w-full">
                        <option value="" disabled selected>Ketik nama kelas...</option>
                        @foreach($rombels as $r)
                            <option value="{{ $r->id }}" {{ old('rombel_id') == $r->id ? 'selected' : '' }}>
                                {{ $r->lembaga->singkatan ?? $r->lembaga->nama }} | {{ $r->tingkat ? $r->tingkat . '-' : '' }}{{ $r->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Input Hari --}}
            <div>
                <label class="block text-xs font-bold text-surface-700 mb-1.5">Hari Pelaksanaan <span class="text-danger-500">*</span></label>
                <select name="hari" required class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-semibold focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    @foreach(['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'AHAD'] as $h)
                        <option value="{{ $h }}" {{ old('hari') === $h ? 'selected' : '' }}>{{ $h }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Input Jam Mulai & Jam Selesai --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1.5">Jam Mulai <span class="text-danger-500">*</span></label>
                    <input type="time" name="jam_mulai" value="{{ old('jam_mulai', '07:00') }}" required class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-semibold focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1.5">Jam Selesai <span class="text-danger-500">*</span></label>
                    <input type="time" name="jam_selesai" value="{{ old('jam_selesai', '08:30') }}" required class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-semibold focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                </div>
            </div>

            {{-- Select Mata Pelajaran (Select2 Real-Time Search) --}}
            <div>
                <label class="block text-xs font-bold text-surface-700 mb-1.5">Mata Pelajaran <span class="text-danger-500">*</span></label>
                <select name="mata_pelajaran_id" required class="select2-search w-full">
                    <option value="" disabled selected>Cari & ketik nama mapel...</option>
                    @foreach($mapels as $m)
                        <option value="{{ $m->id }}" {{ old('mata_pelajaran_id') == $m->id ? 'selected' : '' }}>{{ $m->nama ?? $m->nama_mapel }} {{ $m->kode ? '('.$m->kode.')' : '' }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Select Guru Pengampu (Select2 Real-Time Search) --}}
            <div>
                <label class="block text-xs font-bold text-surface-700 mb-1.5">Guru Pengampu / Ustadz (Opsional)</label>
                <select name="pegawai_id" class="select2-search w-full">
                    <option value="" selected>Belum Ditentukan (Kosongkan jika belum ada guru)</option>
                    @foreach($gurus as $g)
                        <option value="{{ $g->id }}" {{ old('pegawai_id') == $g->id ? 'selected' : '' }}>{{ $g->orang->nama_lengkap }} — (NIUP: {{ $g->orang->niup }})</option>
                    @endforeach
                </select>
            </div>

            {{-- Submit & Cancel Buttons --}}
            <div class="pt-4 border-t border-surface-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.jadwal-pelajaran.index', ['rombel_id' => $rombelId, 'tahun_pelajaran_id' => $tahunId]) }}" class="btn-secondary text-xs font-bold py-2.5 px-5 rounded-xl">
                    Batal
                </a>
                <button type="submit" class="btn-primary text-xs font-bold py-2.5 px-6 rounded-xl shadow-md flex items-center gap-2" style="color: #ffffff !important; background-color: #047857 !important;">
                    <i data-lucide="save" class="w-4 h-4 text-white" style="color: #ffffff !important;"></i>
                    <span style="color: #ffffff !important;">Simpan Jadwal Sesi</span>
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
            placeholder: 'Ketik untuk mencari...',
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush
