@extends('layouts.guru')

@section('title', 'Isi Presensi Kelas — PP Nurul Furqon')

@push('styles')
<style>
    .radio-status-hadir:checked + div {
        background-color: #047857 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(4, 120, 87, 0.25) !important;
    }
    .radio-status-sakit:checked + div {
        background-color: #0284c7 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25) !important;
    }
    .radio-status-izin:checked + div {
        background-color: #d97706 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(217, 119, 6, 0.25) !important;
    }
    .radio-status-alpha:checked + div {
        background-color: #e11d48 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(225, 29, 72, 0.25) !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    {{-- Page Header Bar --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-3xl border border-surface-200 shadow-sm">
        <div>
            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold text-emerald-800 mb-1">
                <span class="px-2.5 py-0.5 rounded-md bg-emerald-50 border border-emerald-200 font-bold">
                    {{ str_starts_with(strtolower($jadwal->rombel->nama ?? ''), 'kelas') ? $jadwal->rombel->nama : 'Kelas ' . ($jadwal->rombel->nama ?? '-') }}
                </span>
                <span>•</span>
                <span class="font-extrabold text-surface-800">{{ $jadwal->mataPelajaran->nama ?? '-' }}</span>
                <span>•</span>
                <span class="px-2.5 py-0.5 rounded-md bg-sky-50 text-sky-800 border border-sky-200 font-bold flex items-center gap-1">
                    <i data-lucide="clock" class="w-3.5 h-3.5 text-sky-600"></i>
                    <span>Pukul {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }} WIB</span>
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-surface-900 font-heading">Daftar Hadir Santri</h1>
        </div>
        <a href="{{ route('guru.presensi.index', ['hari' => $jadwal->hari]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-surface-100 text-surface-700 font-bold text-xs rounded-xl hover:bg-surface-200 transition-colors shrink-0">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Jadwal
        </a>
    </div>

    {{-- Grid Card Waktu & Tanggal Realtime + Info Jam Pelajaran --}}
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
        
        {{-- Card Informasi Jam Pelajaran --}}
        <div class="md:col-span-7 bg-emerald-900 text-white p-5 rounded-3xl border border-emerald-800 shadow-sm flex items-center justify-between relative overflow-hidden" style="background-color: #064e3b !important; color: #ffffff !important;">
            <div class="space-y-1 relative z-10">
                <div class="text-[0.68rem] font-bold text-emerald-200 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Sesi Jam Pelajaran Berlangsung</span>
                </div>
                <h2 class="text-lg font-extrabold text-white leading-tight">
                    {{ $jadwal->mataPelajaran->nama ?? '-' }}
                </h2>
                <div class="flex flex-wrap items-center gap-3 text-xs font-semibold text-emerald-100 pt-1">
                    <span class="flex items-center gap-1">
                        <i data-lucide="users" class="w-3.5 h-3.5 text-emerald-300"></i>
                        <span>{{ str_starts_with(strtolower($jadwal->rombel->nama ?? ''), 'kelas') ? $jadwal->rombel->nama : 'Kelas ' . ($jadwal->rombel->nama ?? '-') }}</span>
                    </span>
                    <span>•</span>
                    <span class="flex items-center gap-1">
                        <i data-lucide="clock" class="w-3.5 h-3.5 text-emerald-300"></i>
                        <span>Pukul {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }} WIB</span>
                    </span>
                    <span>•</span>
                    <span class="flex items-center gap-1">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-emerald-300"></i>
                        <span>Hari {{ ucfirst(strtolower($jadwal->hari ?? '')) }}</span>
                    </span>
                </div>
            </div>
            <div class="hidden sm:flex w-12 h-12 rounded-2xl bg-white/10 text-emerald-200 items-center justify-center shrink-0">
                <i data-lucide="book-open" class="w-6 h-6"></i>
            </div>
        </div>

        {{-- Card Waktu & Tanggal Realtime --}}
        <div class="md:col-span-5 bg-white p-5 rounded-3xl border border-surface-200 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <div class="text-[0.68rem] font-bold text-surface-500 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-emerald-600"></i>
                    <span id="realtime_date">Memuat tanggal...</span>
                </div>
                <div class="text-2xl font-black text-surface-900 tracking-tight flex items-baseline gap-1.5" id="realtime_clock">
                    <span>00:00:00</span>
                    <span class="text-xs font-bold text-emerald-700">WIB</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 border border-emerald-100 flex items-center justify-center font-black text-xs shrink-0 tracking-wider">
                LIVE
            </div>
        </div>

    </div>

    <x-card>
        {{-- Filter Tanggal & Aksi Cepat --}}
        <div class="mb-6 flex flex-col md:flex-row items-stretch md:items-end justify-between gap-4 bg-surface-50 p-4 sm:p-5 rounded-2xl border border-surface-200">
            <div class="w-full md:w-auto">
                <x-date-picker 
                    name="tanggal" 
                    label="Tanggal Pertemuan Presensi" 
                    :value="$tanggal" 
                    :autoSubmit="true" 
                />
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button type="button" id="btn_set_semua_hadir" class="px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-xs rounded-xl transition-all shadow-sm flex items-center gap-2 cursor-pointer" style="background-color: #047857 !important; color: #ffffff !important;">
                    <i data-lucide="check-check" class="w-4 h-4 text-white" style="color: #ffffff !important;"></i>
                    <span>Set Semua Hadir</span>
                </button>
            </div>
        </div>

        <form method="POST" action="{{ route('guru.presensi.store', $jadwal->id) }}">
            @csrf
            <input type="hidden" name="tanggal" value="{{ $tanggal }}">

            <div class="overflow-x-auto rounded-2xl border border-surface-200 mb-6">
                <table class="w-full text-sm text-left min-w-[700px]">
                    <thead class="bg-surface-50 text-surface-700 uppercase font-bold text-[11px] tracking-wider border-b border-surface-200">
                        <tr>
                            <th class="px-4 py-3.5 w-12 text-center">No</th>
                            <th class="px-4 py-3.5">Nama Santri & NISN</th>
                            <th class="px-4 py-3.5 text-center w-72">Status Kehadiran</th>
                            <th class="px-4 py-3.5">Keterangan (Opsional)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200 bg-white">
                        @forelse($jadwal->rombel->riwayatPeserta as $index => $riwayat)
                            @php
                                $peserta = $riwayat->pesertaDidik;
                                $oldStatus = old("presensi.{$peserta->id}.status", $existingPresensi[$peserta->id]->status ?? 'HADIR');
                                $oldKeterangan = old("presensi.{$peserta->id}.keterangan", $existingPresensi[$peserta->id]->keterangan ?? '');
                            @endphp
                            <tr class="hover:bg-surface-50/60 transition-colors">
                                <td class="px-4 py-3.5 text-center font-bold text-surface-500">{{ $index + 1 }}</td>
                                <td class="px-4 py-3.5">
                                    <div class="font-extrabold text-surface-900 text-sm">{{ $peserta->orang->nama ?? '-' }}</div>
                                    <div class="text-xs font-medium text-surface-500">NISN: {{ $peserta->nisn ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-center gap-1 bg-surface-100 p-1 rounded-xl mx-auto w-full">
                                        <label class="flex-1 cursor-pointer text-center">
                                            <input type="radio" name="presensi[{{ $peserta->id }}][status]" value="HADIR" class="peer sr-only status-radio-hadir radio-status-hadir" {{ $oldStatus === 'HADIR' ? 'checked' : '' }}>
                                            <div class="py-1.5 px-2 rounded-lg text-xs font-bold text-surface-700 transition-all hover:bg-surface-200">
                                                Hadir
                                            </div>
                                        </label>
                                        <label class="flex-1 cursor-pointer text-center">
                                            <input type="radio" name="presensi[{{ $peserta->id }}][status]" value="SAKIT" class="peer sr-only radio-status-sakit" {{ $oldStatus === 'SAKIT' ? 'checked' : '' }}>
                                            <div class="py-1.5 px-2 rounded-lg text-xs font-bold text-surface-700 transition-all hover:bg-surface-200">
                                                Sakit
                                            </div>
                                        </label>
                                        <label class="flex-1 cursor-pointer text-center">
                                            <input type="radio" name="presensi[{{ $peserta->id }}][status]" value="IZIN" class="peer sr-only radio-status-izin" {{ $oldStatus === 'IZIN' ? 'checked' : '' }}>
                                            <div class="py-1.5 px-2 rounded-lg text-xs font-bold text-surface-700 transition-all hover:bg-surface-200">
                                                Izin
                                            </div>
                                        </label>
                                        <label class="flex-1 cursor-pointer text-center">
                                            <input type="radio" name="presensi[{{ $peserta->id }}][status]" value="ALPHA" class="peer sr-only radio-status-alpha" {{ $oldStatus === 'ALPA' || $oldStatus === 'ALPHA' ? 'checked' : '' }}>
                                            <div class="py-1.5 px-2 rounded-lg text-xs font-bold text-surface-700 transition-all hover:bg-surface-200">
                                                Alpha
                                            </div>
                                        </label>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <input type="text" name="presensi[{{ $peserta->id }}][keterangan]" value="{{ $oldKeterangan }}" class="w-full rounded-xl border-surface-300 text-xs font-medium px-3 py-2 focus:border-emerald-600 focus:ring focus:ring-emerald-500/20" placeholder="Alasan izin / sakit / alfa...">
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

            @if($jadwal->rombel->riwayatPeserta->count() > 0)
                <div class="flex justify-end gap-3 pt-2">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-xs rounded-xl transition-all shadow-md cursor-pointer" style="background-color: #047857 !important; color: #ffffff !important;">
                        <i data-lucide="save" class="w-4 h-4 text-white" style="color: #ffffff !important;"></i>
                        <span>Simpan Presensi Santri</span>
                    </button>
                </div>
            @endif
        </form>
    </x-card>

</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Quick Set All Hadir
        $('#btn_set_semua_hadir').on('click', function(e) {
            e.preventDefault();
            $('.status-radio-hadir').prop('checked', true);
        });

        // Realtime Live Clock & Date Script
        function updateRealtimeClock() {
            const now = new Date();
            
            // Format Jam HH:mm:ss
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            $('#realtime_clock span:first-child').text(`${hours}:${minutes}:${seconds}`);

            // Format Tanggal Bahasa Indonesia
            const days = ['Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            const dayName = days[now.getDay()];
            const dayDate = now.getDate();
            const monthName = months[now.getMonth()];
            const year = now.getFullYear();

            $('#realtime_date').text(`${dayName}, ${dayDate} ${monthName} ${year}`);
        }

        setInterval(updateRealtimeClock, 1000);
        updateRealtimeClock();
    });
</script>
@endpush
