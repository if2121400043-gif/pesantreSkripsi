@extends('layouts.guru')

@section('title', 'Isi Presensi Kelas — PP Nurul Furqon')

@push('styles')
<style>
    .radio-status-btn input:checked + .status-badge-hadir {
        background-color: #059669 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(5, 150, 105, 0.3) !important;
    }
    .radio-status-btn input:checked + .status-badge-sakit {
        background-color: #0284c7 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(2, 132, 199, 0.3) !important;
    }
    .radio-status-btn input:checked + .status-badge-izin {
        background-color: #d97706 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(217, 119, 6, 0.3) !important;
    }
    .radio-status-btn input:checked + .status-badge-alpha {
        background-color: #e11d48 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(225, 29, 72, 0.3) !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-6 pb-12">

    {{-- Header Bar Responsif --}}
    <div class="bg-white p-5 sm:p-6 rounded-3xl border border-surface-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1">
            <div class="flex flex-wrap items-center gap-2 text-xs font-bold text-emerald-800">
                <span class="px-2.5 py-1 rounded-lg bg-emerald-100 border border-emerald-200">
                    {{ str_starts_with(strtolower($jadwal->rombel->nama ?? ''), 'kelas') ? strtoupper($jadwal->rombel->nama) : 'KELAS ' . strtoupper($jadwal->rombel->nama ?? '-') }}
                </span>
                <span>•</span>
                <span class="text-surface-700 font-extrabold">{{ $jadwal->mataPelajaran->nama ?? '-' }}</span>
                <span>•</span>
                <span class="text-emerald-700">Hari {{ ucfirst(strtolower($jadwal->hari ?? '')) }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-surface-900 font-heading">Form Presensi Santri</h1>
        </div>
        <a href="{{ route('guru.presensi.index', ['hari' => $jadwal->hari]) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-surface-100 text-surface-800 font-bold text-xs rounded-xl hover:bg-surface-200 transition-all shrink-0">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Jadwal</span>
        </a>
    </div>

    {{-- Filter Tanggal & Ringkasan Stats --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-center">
        
        {{-- Card Tanggal Pertemuan --}}
        <div class="lg:col-span-6 bg-white p-4 sm:p-5 rounded-3xl border border-surface-200 shadow-sm">
            <form method="GET" action="{{ route('guru.presensi.create', $jadwal->id) }}" class="flex flex-col sm:flex-row sm:items-end gap-3">
                <div class="flex-1">
                    <x-date-picker 
                        name="tanggal" 
                        label="Tanggal Pertemuan Presensi" 
                        :value="$tanggal" 
                        :autoSubmit="true" 
                    />
                </div>
                <div class="text-[0.7rem] text-surface-500 font-medium">
                    *Mengganti tanggal otomatis memuat presensi hari tersebut.
                </div>
            </form>
        </div>

        {{-- Quick Set Action & Stats --}}
        <div class="lg:col-span-6 bg-white p-4 sm:p-5 rounded-3xl border border-surface-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
            <div>
                <div class="text-[0.68rem] text-surface-500 font-bold uppercase tracking-wider">Aksi Cepat Absensi</div>
                <div class="text-xs font-bold text-surface-800">Setel default untuk seluruh santri</div>
            </div>
            <button type="button" id="btn_set_semua_hadir" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl transition-all shadow-md shadow-emerald-600/20 cursor-pointer" style="background-color: #047857 !important; color: #ffffff !important;">
                <i data-lucide="check-check" class="w-4 h-4"></i>
                <span>Set Semua Hadir</span>
            </button>
        </div>

    </div>

    {{-- Form Utama Presensi --}}
    <form method="POST" action="{{ route('guru.presensi.store', $jadwal->id) }}" id="form_presensi">
        @csrf
        <input type="hidden" name="tanggal" value="{{ $tanggal }}">

        {{-- VIEW DESKTOP: TABLE VIEW (Tampil pada Layar Sedang & Besar `hidden md:block`) --}}
        <div class="hidden md:block bg-white rounded-3xl border border-surface-200 shadow-sm overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-surface-50/80 text-surface-700 uppercase font-black text-[11px] tracking-wider border-b border-surface-200">
                        <tr>
                            <th class="px-5 py-4 w-12 text-center">No</th>
                            <th class="px-5 py-4">Nama Santri & NISN</th>
                            <th class="px-5 py-4 text-center w-72">Status Kehadiran</th>
                            <th class="px-5 py-4">Keterangan (Opsional)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200 bg-white">
                        @forelse($jadwal->rombel->riwayatPeserta as $index => $riwayat)
                            @php
                                $peserta = $riwayat->pesertaDidik;
                                $oldStatus = old("presensi.{$peserta->id}.status", $existingPresensi[$peserta->id]->status ?? 'HADIR');
                                $oldKeterangan = old("presensi.{$peserta->id}.keterangan", $existingPresensi[$peserta->id]->keterangan ?? '');
                            @endphp
                            <tr class="hover:bg-emerald-50/30 transition-colors">
                                <td class="px-5 py-4 text-center font-bold text-surface-500">{{ $index + 1 }}</td>
                                <td class="px-5 py-4">
                                    <div class="font-extrabold text-surface-900 text-sm">{{ $peserta->orang->nama ?? '-' }}</div>
                                    <div class="text-xs font-semibold text-surface-500">NISN: {{ $peserta->nisn ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-1.5 bg-surface-100/80 p-1.5 rounded-2xl w-full">
                                        <label class="radio-status-btn flex-1 cursor-pointer">
                                            <input type="radio" name="presensi[{{ $peserta->id }}][status]" value="HADIR" class="peer sr-only status-radio-hadir" {{ $oldStatus === 'HADIR' ? 'checked' : '' }}>
                                            <div class="status-badge-hadir min-h-[38px] px-2.5 py-2 rounded-xl text-xs font-black text-center transition-all text-surface-600 hover:bg-surface-200 flex items-center justify-center gap-1">
                                                <span>Hadir</span>
                                            </div>
                                        </label>
                                        <label class="radio-status-btn flex-1 cursor-pointer">
                                            <input type="radio" name="presensi[{{ $peserta->id }}][status]" value="SAKIT" class="peer sr-only" {{ $oldStatus === 'SAKIT' ? 'checked' : '' }}>
                                            <div class="status-badge-sakit min-h-[38px] px-2.5 py-2 rounded-xl text-xs font-black text-center transition-all text-surface-600 hover:bg-surface-200 flex items-center justify-center gap-1">
                                                <span>Sakit</span>
                                            </div>
                                        </label>
                                        <label class="radio-status-btn flex-1 cursor-pointer">
                                            <input type="radio" name="presensi[{{ $peserta->id }}][status]" value="IZIN" class="peer sr-only" {{ $oldStatus === 'IZIN' ? 'checked' : '' }}>
                                            <div class="status-badge-izin min-h-[38px] px-2.5 py-2 rounded-xl text-xs font-black text-center transition-all text-surface-600 hover:bg-surface-200 flex items-center justify-center gap-1">
                                                <span>Izin</span>
                                            </div>
                                        </label>
                                        <label class="radio-status-btn flex-1 cursor-pointer">
                                            <input type="radio" name="presensi[{{ $peserta->id }}][status]" value="ALPHA" class="peer sr-only" {{ $oldStatus === 'ALPA' || $oldStatus === 'ALPHA' ? 'checked' : '' }}>
                                            <div class="status-badge-alpha min-h-[38px] px-2.5 py-2 rounded-xl text-xs font-black text-center transition-all text-surface-600 hover:bg-surface-200 flex items-center justify-center gap-1">
                                                <span>Alpha</span>
                                            </div>
                                        </label>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <input type="text" name="presensi[{{ $peserta->id }}][keterangan]" value="{{ $oldKeterangan }}" class="w-full rounded-xl border border-surface-300 px-3.5 py-2 text-xs font-medium focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600" placeholder="Alasan izin / sakit / alfa...">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-surface-500">
                                    <i data-lucide="users" class="w-8 h-8 mx-auto mb-3 text-surface-300"></i>
                                    Belum ada santri yang terdaftar aktif di kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- VIEW MOBILE: CARDS VIEW (Tampil pada Layar HP / Tablet `block md:hidden`) --}}
        <div class="block md:hidden space-y-4 mb-6">
            @forelse($jadwal->rombel->riwayatPeserta as $index => $riwayat)
                @php
                    $peserta = $riwayat->pesertaDidik;
                    $oldStatus = old("presensi.{$peserta->id}.status", $existingPresensi[$peserta->id]->status ?? 'HADIR');
                    $oldKeterangan = old("presensi.{$peserta->id}.keterangan", $existingPresensi[$peserta->id]->keterangan ?? '');
                @endphp
                <div class="bg-white rounded-3xl p-5 border border-surface-200 shadow-sm space-y-4">
                    
                    {{-- Header Nama Santri --}}
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-xs flex items-center justify-center shrink-0">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <h3 class="font-extrabold text-surface-900 text-sm leading-tight">{{ $peserta->orang->nama ?? '-' }}</h3>
                                <p class="text-[0.68rem] text-surface-500 font-semibold">NISN: {{ $peserta->nisn ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Radio Status Pill Mobile --}}
                    <div>
                        <label class="block text-[0.68rem] font-bold text-surface-500 uppercase tracking-wider mb-1.5">Status Kehadiran</label>
                        <div class="grid grid-cols-4 gap-1.5 bg-surface-100 p-1.5 rounded-2xl">
                            <label class="radio-status-btn cursor-pointer">
                                <input type="radio" name="presensi[{{ $peserta->id }}][status]" value="HADIR" class="peer sr-only status-radio-hadir" {{ $oldStatus === 'HADIR' ? 'checked' : '' }}>
                                <div class="status-badge-hadir min-h-[42px] px-2 py-2 rounded-xl text-xs font-black text-center transition-all text-surface-700 flex flex-col items-center justify-center">
                                    <span>Hadir</span>
                                </div>
                            </label>
                            <label class="radio-status-btn cursor-pointer">
                                <input type="radio" name="presensi[{{ $peserta->id }}][status]" value="SAKIT" class="peer sr-only" {{ $oldStatus === 'SAKIT' ? 'checked' : '' }}>
                                <div class="status-badge-sakit min-h-[42px] px-2 py-2 rounded-xl text-xs font-black text-center transition-all text-surface-700 flex flex-col items-center justify-center">
                                    <span>Sakit</span>
                                </div>
                            </label>
                            <label class="radio-status-btn cursor-pointer">
                                <input type="radio" name="presensi[{{ $peserta->id }}][status]" value="IZIN" class="peer sr-only" {{ $oldStatus === 'IZIN' ? 'checked' : '' }}>
                                <div class="status-badge-izin min-h-[42px] px-2 py-2 rounded-xl text-xs font-black text-center transition-all text-surface-700 flex flex-col items-center justify-center">
                                    <span>Izin</span>
                                </div>
                            </label>
                            <label class="radio-status-btn cursor-pointer">
                                <input type="radio" name="presensi[{{ $peserta->id }}][status]" value="ALPHA" class="peer sr-only" {{ $oldStatus === 'ALPA' || $oldStatus === 'ALPHA' ? 'checked' : '' }}>
                                <div class="status-badge-alpha min-h-[42px] px-2 py-2 rounded-xl text-xs font-black text-center transition-all text-surface-700 flex flex-col items-center justify-center">
                                    <span>Alpha</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Input Keterangan Mobile --}}
                    <div>
                        <input type="text" name="presensi[{{ $peserta->id }}][keterangan]" value="{{ $oldKeterangan }}" class="w-full rounded-xl border border-surface-300 px-3.5 py-2.5 text-xs font-medium focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600" placeholder="Keterangan (opsional)...">
                    </div>

                </div>
            @empty
                <div class="bg-white rounded-3xl p-8 border border-surface-200 shadow-sm text-center text-surface-500 text-xs">
                    <i data-lucide="users" class="w-8 h-8 mx-auto mb-3 text-surface-300"></i>
                    Belum ada santri yang terdaftar aktif di kelas ini.
                </div>
            @endforelse
        </div>

        {{-- Floating/Sticky Bottom Save Bar --}}
        @if($jadwal->rombel->riwayatPeserta->count() > 0)
            <div class="sticky bottom-4 z-20 bg-white/95 backdrop-blur-md p-4 rounded-3xl border border-surface-200 shadow-xl flex items-center justify-between gap-4">
                <div class="text-xs font-bold text-surface-700 hidden sm:block">
                    Total {{ $jadwal->rombel->riwayatPeserta->count() }} Santri Terdaftar
                </div>
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-xs sm:text-sm rounded-2xl transition-all shadow-lg shadow-emerald-700/25 cursor-pointer" style="background-color: #047857 !important; color: #ffffff !important;">
                    <i data-lucide="save" class="w-5 h-5 text-white" style="color: #ffffff !important;"></i>
                    <span>Simpan Presensi Kelas</span>
                </button>
            </div>
        @endif

    </form>

</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Quick Set All Hadir
        $('#btn_set_semua_hadir').on('click', function(e) {
            e.preventDefault();
            $('.status-radio-hadir').prop('checked', true).trigger('change');
            
            // Visual notification highlight
            $(this).addClass('scale-95');
            setTimeout(() => {
                $(this).removeClass('scale-95');
            }, 200);
        });
    });
</script>
@endpush
