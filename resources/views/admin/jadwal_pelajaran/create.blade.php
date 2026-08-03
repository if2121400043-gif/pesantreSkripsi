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
    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-950 p-4 rounded-2xl border border-emerald-300 shadow-sm flex items-start gap-3">
            <div class="w-7 h-7 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold shrink-0 mt-0.5" style="background-color: #047857 !important; color: #ffffff !important;">
                <i data-lucide="check-circle" class="w-4 h-4 text-white" style="color: #ffffff !important;"></i>
            </div>
            <div class="text-xs font-bold leading-relaxed pt-1">
                {{ session('success') }}
            </div>
        </div>
    @endif

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
                
                {{-- Header Target Kelas dengan Tombol Ubah Kelas --}}
                <div class="p-4 sm:p-5 bg-emerald-50/80 border border-emerald-200 rounded-2xl space-y-4">
                    <div class="flex items-center justify-between gap-4">
                        
                        {{-- Icon + Info Block dengan Margin & Gap Jelas --}}
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-700 text-white flex items-center justify-center font-bold shrink-0 shadow-sm" style="background-color: #047857 !important; color: #ffffff !important;">
                                <i data-lucide="school" class="w-6 h-6"></i>
                            </div>
                            <div class="min-w-0 space-y-1">
                                <div class="text-[0.7rem] font-black text-emerald-800 uppercase tracking-wider block">Target Kelas & Rombel</div>
                                <div class="text-base font-black text-emerald-950 font-heading truncate leading-tight block" id="text_nama_kelas">
                                    @if($rombel)
                                        {{ $rombel->lembaga->singkatan ?? $rombel->lembaga->nama }} — {{ str_starts_with(strtoupper($rombel->nama ?? ''), 'KELAS') ? strtoupper($rombel->nama) : 'KELAS ' . strtoupper($rombel->nama) }}
                                    @else
                                        Silakan Pilih Kelas
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Ubah Kelas --}}
                        <button type="button" id="btn_toggle_ubah_kelas" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white hover:bg-emerald-100 text-emerald-800 border border-emerald-300 font-extrabold text-xs transition-all shadow-2xs shrink-0">
                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-emerald-700"></i>
                            <span>Ubah Kelas</span>
                        </button>
                    </div>

                    {{-- Container Select Rombel (Dengan padding-top & margin-top yang lega dari garis border) --}}
                    <div id="wrapper_select_rombel" class="pt-4 border-t border-emerald-200/90 space-y-2 {{ $rombel ? 'hidden' : '' }}">
                        <label class="block text-xs font-extrabold text-emerald-950 mb-1">
                            Pilih Kelas / Rombel Baru <span class="text-rose-500">*</span>
                        </label>
                        <select name="rombel_id" id="rombel_id" required class="select2-search w-full">
                            <option value="" disabled {{ !$rombelId ? 'selected' : '' }}>Ketik nama kelas...</option>
                            @foreach($rombels as $r)
                                <option value="{{ $r->id }}" {{ old('rombel_id', $rombelId) == $r->id ? 'selected' : '' }} data-lembaga-id="{{ $r->lembaga_id }}" data-nama="{{ $r->lembaga->singkatan ?? $r->lembaga->nama }} — {{ str_starts_with(strtoupper($r->nama ?? ''), 'KELAS') ? strtoupper($r->nama) : 'KELAS ' . strtoupper($r->nama) }}">
                                    {{ $r->lembaga->singkatan ?? $r->lembaga->nama }} | {{ str_starts_with(strtoupper($r->nama ?? ''), 'KELAS') ? strtoupper($r->nama) : 'KELAS ' . strtoupper($r->nama) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Input Hari --}}
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1.5 flex items-center justify-between">
                        <span>Hari Pelaksanaan <span class="text-rose-500">*</span></span>
                        <span class="text-[0.68rem] text-emerald-700 font-bold">Mengatur Peta Jam Terisi</span>
                    </label>
                    <select name="hari" id="select_hari" required class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-bold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all">
                        @foreach(['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'AHAD'] as $h)
                            <option value="{{ $h }}" {{ old('hari', $hari ?? 'SENIN') === $h ? 'selected' : '' }}>Hari {{ ucfirst(strtolower($h)) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Input Jam Mulai & Jam Selesai dengan Quick Duration Buttons --}}
                <div class="space-y-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-surface-700 mb-1.5">Jam Mulai <span class="text-rose-500">*</span></label>
                            <input type="time" name="jam_mulai" id="jam_mulai" value="{{ old('jam_mulai', $lastJamMulai ?? '07:30') }}" required class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-bold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-surface-700 mb-1.5">Jam Selesai <span class="text-rose-500">*</span></label>
                            <input type="time" name="jam_selesai" id="jam_selesai" value="{{ old('jam_selesai', $lastJamSelesai ?? '08:15') }}" required class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-bold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all">
                        </div>
                    </div>

                    {{-- Quick Duration Buttons (+45 min, +90 min, +60 min) --}}
                    <div class="pt-1">
                        <span class="text-[0.68rem] text-surface-500 font-bold block mb-1.5 flex items-center gap-1">
                            <i data-lucide="zap" class="w-3.5 h-3.5 text-amber-500"></i>
                            <span>Hitung Jam Selesai Otomatis (Durasi Cepat):</span>
                        </span>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" class="btn-quick-duration px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-800 hover:text-white border border-emerald-200 text-[0.72rem] font-black transition-all shadow-2xs flex items-center gap-1.5 cursor-pointer" data-minutes="45">
                                <i data-lucide="clock-4" class="w-3.5 h-3.5"></i>
                                <span>+45 Menit (1 Sesi)</span>
                            </button>
                            <button type="button" class="btn-quick-duration px-3 py-1.5 rounded-xl bg-teal-50 hover:bg-teal-600 text-teal-800 hover:text-white border border-teal-200 text-[0.72rem] font-black transition-all shadow-2xs flex items-center gap-1.5 cursor-pointer" data-minutes="90">
                                <i data-lucide="clock-8" class="w-3.5 h-3.5"></i>
                                <span>+90 Menit (2 Sesi)</span>
                            </button>
                            <button type="button" class="btn-quick-duration px-3 py-1.5 rounded-xl bg-sky-50 hover:bg-sky-600 text-sky-800 hover:text-white border border-sky-200 text-[0.72rem] font-black transition-all shadow-2xs flex items-center gap-1.5 cursor-pointer" data-minutes="60">
                                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                <span>+60 Menit (1 Jam Penuh)</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Select Mata Pelajaran (Select2 Real-Time Search) --}}
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1.5">Mata Pelajaran <span class="text-rose-500">*</span></label>
                    <select name="mata_pelajaran_id" id="mata_pelajaran_id" required class="select2-search w-full">
                        <option value="" disabled selected>Cari & ketik nama mapel...</option>
                        @foreach($mapels as $m)
                            <option value="{{ $m->id }}" data-lembaga-id="{{ $m->lembaga_id }}" {{ old('mata_pelajaran_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->nama ?? $m->nama_mapel }} {{ $m->kode ? '('.$m->kode.')' : '' }} @if($m->lembaga) — ({{ $m->lembaga->singkatan ?? $m->lembaga->nama }}) @endif
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

        // Function to strictly filter Mata Pelajaran options by selected Rombel's Lembaga
        function filterMapelByLembaga() {
            const selectedRombel = $('#rombel_id').find('option:selected');
            const lembagaId = selectedRombel.data('lembaga-id');
            const mapelSelect = $('#mata_pelajaran_id');

            if (lembagaId) {
                mapelSelect.find('option').each(function() {
                    const optLembaga = $(this).data('lembaga-id');
                    // Show only if optLembaga matches
                    if (!optLembaga || optLembaga == lembagaId) {
                        $(this).prop('disabled', false);
                    } else {
                        $(this).prop('disabled', true);
                    }
                });
            } else {
                mapelSelect.find('option').prop('disabled', false);
            }

            // Re-initialize select2 to reflect disabled states
            mapelSelect.select2({
                placeholder: 'Cari & ketik nama mapel...',
                allowClear: true,
                width: '100%'
            });
        }

        // Quick Duration Handler (+45m, +90m, +60m)
        $('.btn-quick-duration').on('click', function(e) {
            e.preventDefault();
            const minutesToAdd = parseInt($(this).data('minutes'));
            const jamMulaiVal = $('#jam_mulai').val();

            if (!jamMulaiVal) {
                alert('Silakan pilih Jam Mulai terlebih dahulu.');
                return;
            }

            const parts = jamMulaiVal.split(':');
            let hours = parseInt(parts[0]);
            let minutes = parseInt(parts[1]);

            let totalMinutes = (hours * 60) + minutes + minutesToAdd;
            let endHours = Math.floor(totalMinutes / 60) % 24;
            let endMinutes = totalMinutes % 60;

            const formattedEnd = String(endHours).padStart(2, '0') + ':' + String(endMinutes).padStart(2, '0');
            
            const jamSelesaiInput = $('#jam_selesai');
            jamSelesaiInput.val(formattedEnd);

            // Visual feedback highlight
            jamSelesaiInput.addClass('ring-2 ring-emerald-500 bg-emerald-50');
            setTimeout(function() {
                jamSelesaiInput.removeClass('ring-2 ring-emerald-500 bg-emerald-50');
            }, 600);
        });

        // Event listeners
        $('#btn_toggle_ubah_kelas').on('click', function(e) {
            e.preventDefault();
            $('#wrapper_select_rombel').toggleClass('hidden');
        });

        $('#select_hari').on('change', loadOccupiedSchedules);
        $('#rombel_id').on('change', function() {
            const selectedOpt = $(this).find('option:selected');
            const namaKelas = selectedOpt.data('nama');
            if (namaKelas) {
                $('#text_nama_kelas').text(namaKelas);
            }
            filterMapelByLembaga();
            loadOccupiedSchedules();
        });

        // Initial filter & load
        filterMapelByLembaga();
        loadOccupiedSchedules();
    });
</script>
@endpush
