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
            <a href="{{ route('admin.jadwal-pelajaran.index', ['rombel_id' => $rombelId, 'tahun_pelajaran_id' => $tahunId]) }}" class="hover:text-emerald-700 transition-colors font-medium">Jadwal Pelajaran</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            <span class="text-surface-900 font-bold">Tambah Sesi Baru</span>
        </div>
        <h1 class="text-2xl font-extrabold text-surface-900 font-heading">Tambah Jadwal Pelajaran</h1>
    </div>
    <a href="{{ route('admin.jadwal-pelajaran.index', ['rombel_id' => $rombelId, 'tahun_pelajaran_id' => $tahunId]) }}" class="btn-secondary flex items-center gap-2 text-xs font-bold py-2.5 px-4 rounded-xl">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Kembali ke Jadwal</span>
    </a>
</div>
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pb-12">

    {{-- Notifikasi Error Global / Flash --}}
    @if(session('error'))
        <div class="bg-rose-50 text-rose-900 p-4 rounded-2xl border border-rose-200 shadow-sm flex items-start gap-3">
            <i data-lucide="alert-octagon" class="w-5 h-5 text-rose-600 shrink-0 mt-0.5"></i>
            <div class="text-xs font-bold">
                {{ session('error') }}
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 text-rose-800 p-4 rounded-2xl border border-rose-200 shadow-sm">
            <div class="flex gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0 mt-0.5"></i>
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

    <form action="{{ route('admin.jadwal-pelajaran.store') }}" method="POST" id="jadwal-form">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            {{-- Kolom Kiri: Form Input Sesi Jadwal (7/12) --}}
            <div class="lg:col-span-7 bg-white rounded-3xl p-6 sm:p-7 border border-surface-200 shadow-sm space-y-5">
                
                {{-- Header Target Kelas --}}
                <div class="flex items-center gap-3.5 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl">
                    <div class="w-10 h-10 rounded-xl bg-emerald-700 text-white flex items-center justify-center font-bold shrink-0 shadow-sm" style="background-color: #047857 !important; color: #ffffff !important;">
                        <i data-lucide="school" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <div class="text-[0.68rem] text-emerald-800 font-extrabold uppercase tracking-wider">Target Kelas & Rombel</div>
                        <div class="text-sm font-extrabold text-emerald-950 font-heading">
                            @if($rombel)
                                {{ $rombel->lembaga->singkatan ?? $rombel->lembaga->nama }} — {{ str_starts_with(strtoupper($rombel->nama ?? ''), 'KELAS') ? strtoupper($rombel->nama) : 'KELAS ' . strtoupper($rombel->nama) }}
                            @else
                                Pilih Kelas Rombel
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Input Hidden atau Select Kelas --}}
                @if($rombel)
                    <input type="hidden" name="rombel_id" id="rombel_id" value="{{ $rombel->id }}">
                @else
                    <div>
                        <label class="block text-xs font-bold text-surface-700 mb-1.5">Pilih Kelas / Rombel <span class="text-rose-500">*</span></label>
                        <select name="rombel_id" id="rombel_id" required class="select2-search w-full">
                            <option value="" disabled selected>Ketik nama kelas...</option>
                            @foreach($rombels as $r)
                                <option value="{{ $r->id }}" {{ old('rombel_id', $rombelId) == $r->id ? 'selected' : '' }}>
                                    {{ $r->lembaga->singkatan ?? $r->lembaga->nama }} | {{ str_starts_with(strtoupper($r->nama ?? ''), 'KELAS') ? strtoupper($r->nama) : 'KELAS ' . strtoupper($r->nama) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Input Hari --}}
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1.5 flex items-center justify-between">
                        <span>Hari Pelaksanaan <span class="text-rose-500">*</span></span>
                        <span class="text-[0.68rem] text-emerald-700 font-bold">Mengatur Peta Jam Terisi</span>
                    </label>
                    <select name="hari" id="select_hari" required class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-bold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all">
                        @foreach(['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'AHAD'] as $h)
                            <option value="{{ $h }}" {{ old('hari', 'SENIN') === $h ? 'selected' : '' }}>Hari {{ ucfirst(strtolower($h)) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Input Jam Mulai & Jam Selesai --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-surface-700 mb-1.5">Jam Mulai <span class="text-rose-500">*</span></label>
                        <input type="time" name="jam_mulai" id="jam_mulai" value="{{ old('jam_mulai', '07:30') }}" required class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-bold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-surface-700 mb-1.5">Jam Selesai <span class="text-rose-500">*</span></label>
                        <input type="time" name="jam_selesai" id="jam_selesai" value="{{ old('jam_selesai', '08:15') }}" required class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-bold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                    </div>
                </div>

                {{-- Select Mata Pelajaran (Select2 Real-Time Search) --}}
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1.5">Mata Pelajaran <span class="text-rose-500">*</span></label>
                    <select name="mata_pelajaran_id" required class="select2-search w-full">
                        <option value="" disabled selected>Cari & ketik nama mapel...</option>
                        @foreach($mapels as $m)
                            <option value="{{ $m->id }}" {{ old('mata_pelajaran_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->nama ?? $m->nama_mapel }} {{ $m->kode ? '('.$m->kode.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Select Guru Pengampu (Select2 Real-Time Search) --}}
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1.5">Guru Pengampu / Ustadz (Opsional)</label>
                    <select name="pegawai_id" class="select2-search w-full">
                        <option value="" selected>Belum Ditentukan (Kosongkan jika belum ada guru)</option>
                        @foreach($gurus as $g)
                            <option value="{{ $g->id }}" {{ old('pegawai_id') == $g->id ? 'selected' : '' }}>
                                {{ $g->orang->nama_lengkap }} — (NIUP: {{ $g->orang->niup }})
                            </option>
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
                        <span style="color: #ffffff !important;">Simpan Sesi Jadwal</span>
                    </button>
                </div>

            </div>

            {{-- Kolom Kanan: Live Occupied Schedule Guidance Panel (5/12) --}}
            <div class="lg:col-span-5 space-y-4 sticky top-20">
                <div class="bg-white rounded-3xl p-6 border border-surface-200 shadow-md">
                    <div class="flex items-center justify-between gap-2 pb-3 mb-4 border-b border-surface-100">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold shrink-0">
                                <i data-lucide="clock" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold text-surface-900 font-heading">Jadwal Terisi Di Kelas Ini</h3>
                                <p class="text-[0.68rem] text-surface-500">Peta jam terpakai untuk cegah bentrok.</p>
                            </div>
                        </div>
                        <span id="label_hari_aktif" class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-900 text-[0.7rem] font-black uppercase tracking-wider">
                            Hari Senin
                        </span>
                    </div>

                    {{-- Dynamic Occupied Schedules Container --}}
                    <div id="occupied_container" class="space-y-3 min-h-[160px]">
                        <div class="text-center py-8 text-surface-400 text-xs">
                            <i data-lucide="loader-2" class="w-5 h-5 animate-spin mx-auto mb-2 text-emerald-600"></i>
                            <span>Memuat peta jam terisi...</span>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-surface-100 flex items-center gap-2 text-[0.68rem] text-surface-450">
                        <i data-lucide="info" class="w-3.5 h-3.5 text-emerald-600 shrink-0"></i>
                        <span>Sistem otomatis menolak jika jam yang diinput menabrak jam yang terisi di atas.</span>
                    </div>
                </div>
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

        // Function to fetch occupied schedules
        function loadOccupiedSchedules() {
            const rombelId = $('#rombel_id').val();
            const hari = $('#select_hari').val();
            const container = $('#occupied_container');
            const labelHari = $('#label_hari_aktif');

            if (labelHari && hari) {
                labelHari.text('Hari ' + hari.charAt(0) + hari.slice(1).toLowerCase());
            }

            if (!rombelId || !hari) {
                container.html(`
                    <div class="text-center py-6 text-surface-400 text-xs">
                        <i data-lucide="alert-circle" class="w-5 h-5 mx-auto mb-1 text-surface-300"></i>
                        <span>Pilih Kelas dan Hari terlebih dahulu.</span>
                    </div>
                `);
                if (window.lucide) lucide.createIcons();
                return;
            }

            container.html(`
                <div class="text-center py-6 text-surface-400 text-xs">
                    <i data-lucide="loader-2" class="w-5 h-5 animate-spin mx-auto mb-2 text-emerald-600"></i>
                    <span>Memeriksa jam terisi...</span>
                </div>
            `);
            if (window.lucide) lucide.createIcons();

            $.getJSON('/admin/jadwal-pelajaran/occupied', { rombel_id: rombelId, hari: hari }, function(data) {
                if (!data || data.length === 0) {
                    container.html(`
                        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-center">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto mb-2">
                                <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                            </div>
                            <h4 class="font-extrabold text-emerald-900 text-xs mb-0.5">Belum Ada Jam Terisi</h4>
                            <p class="text-[0.68rem] text-emerald-700">Hari ini kelas masih kosong. Bebas menginput jam berapa saja.</p>
                        </div>
                    `);
                } else {
                    let html = '<div class="space-y-2.5">';
                    data.forEach(function(item) {
                        html += `
                            <div class="p-3.5 bg-rose-50/60 border border-rose-200 rounded-2xl flex items-start gap-3">
                                <div class="w-2.5 h-2.5 rounded-full bg-rose-500 shrink-0 mt-1"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2 mb-0.5">
                                        <span class="font-mono font-extrabold text-rose-900 text-xs">${item.jam_mulai} - ${item.jam_selesai}</span>
                                        <span class="text-[0.62rem] px-2 py-0.5 rounded-md bg-rose-100 text-rose-800 font-bold border border-rose-200">Terisi</span>
                                    </div>
                                    <h5 class="text-xs font-bold text-surface-900 truncate">${item.mapel}</h5>
                                    <p class="text-[0.65rem] text-surface-500 truncate"><i data-lucide="user" class="w-3 h-3 inline"></i> ${item.guru}</p>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    container.html(html);
                }
                if (window.lucide) lucide.createIcons();
            }).fail(function() {
                container.html(`
                    <div class="p-3 bg-surface-100 rounded-2xl text-center text-xs text-surface-500">
                        Gagal memuat jadwal terisi.
                    </div>
                `);
            });
        }

        // Event listeners
        $('#select_hari').on('change', loadOccupiedSchedules);
        $('#rombel_id').on('change', loadOccupiedSchedules);

        // Initial load
        loadOccupiedSchedules();
    });
</script>
@endpush
