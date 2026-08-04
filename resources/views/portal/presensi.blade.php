@extends('layouts.portal')

@section('title', 'Rekap Presensi & Kehadiran — PP Nurul Furqon')

@section('content')
<div class="space-y-6">

    {{-- Hero Summary Header --}}
    <div class="rounded-3xl p-6 sm:p-7 shadow-lg text-white" style="background: linear-gradient(135deg, #0f766e, #0f524c) !important;">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-white/15 text-[0.7rem] font-bold backdrop-blur-sm border border-white/20 mb-2">
                    <i data-lucide="calendar-check" class="w-3.5 h-3.5 text-teal-300"></i>
                    Rekap Kehadiran
                </span>
                <h1 class="text-xl sm:text-2xl font-extrabold font-heading">
                    Presensi & Kehadiran Santri
                </h1>
                <p class="text-xs text-teal-100 mt-1">
                    Santri: <strong class="text-white">{{ $activeAnak->orang->nama_lengkap ?? '-' }}</strong> ({{ $activeAnak->orang->niup ?? '-' }})
                </p>
            </div>

            <div class="bg-white/15 backdrop-blur-md rounded-2xl p-4 border border-white/20 text-right w-full sm:w-auto">
                <span class="text-[0.65rem] uppercase font-bold text-teal-200 block">Persentase Kehadiran</span>
                <span class="text-3xl font-black text-white">{{ $kehadiranPersen }}%</span>
            </div>
        </div>
    </div>

    {{-- Stats Quick Summary Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white rounded-2xl p-4 border border-emerald-200 shadow-2xs text-center">
            <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider block">HADIR</span>
            <span class="text-2xl sm:text-3xl font-black text-emerald-800 mt-1 block">{{ $kehadiranStats['HADIR'] }}</span>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-sky-200 shadow-2xs text-center">
            <span class="text-xs font-bold text-sky-700 uppercase tracking-wider block">IZIN</span>
            <span class="text-2xl sm:text-3xl font-black text-sky-800 mt-1 block">{{ $kehadiranStats['IZIN'] }}</span>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-amber-200 shadow-2xs text-center">
            <span class="text-xs font-bold text-amber-700 uppercase tracking-wider block">SAKIT</span>
            <span class="text-2xl sm:text-3xl font-black text-amber-800 mt-1 block">{{ $kehadiranStats['SAKIT'] }}</span>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-rose-200 shadow-2xs text-center">
            <span class="text-xs font-bold text-rose-700 uppercase tracking-wider block">ALPHA</span>
            <span class="text-2xl sm:text-3xl font-black text-rose-800 mt-1 block">{{ $kehadiranStats['ALPA'] }}</span>
        </div>
    </div>

    {{-- Attendance Table Card --}}
    <div class="bg-white rounded-3xl border border-surface-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-surface-100 flex justify-between items-center">
            <h2 class="text-base font-bold text-surface-900 flex items-center gap-2">
                <i data-lucide="list" class="w-5 h-5 text-teal-700"></i>
                Riwayat Absensi Harian Kelas
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-surface-50 border-b border-surface-200 text-xs text-surface-600 uppercase tracking-wider">
                        <th class="text-left py-3.5 px-5 font-bold">Tanggal</th>
                        <th class="text-left py-3.5 px-5 font-bold">Kelas / Rombel</th>
                        <th class="text-center py-3.5 px-5 font-bold">Status</th>
                        <th class="text-left py-3.5 px-5 font-bold">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100">
                    @forelse($presensis as $presensi)
                    <tr class="hover:bg-surface-50/50 transition-colors">
                        <td class="py-4 px-5 font-bold text-surface-900">
                            {{ \Carbon\Carbon::parse($presensi->attendance_date)->translatedFormat('l, d M Y') }}
                        </td>
                        <td class="py-4 px-5 text-surface-700 font-medium">
                            {{ $presensi->rombel->nama ?? '-' }}
                        </td>
                        <td class="py-4 px-5 text-center">
                            @if($presensi->status === 'HADIR')
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-extrabold rounded-full border border-emerald-200">HADIR</span>
                            @elseif($presensi->status === 'SAKIT')
                                <span class="px-3 py-1 bg-amber-100 text-amber-800 text-xs font-extrabold rounded-full border border-amber-200">SAKIT</span>
                            @elseif($presensi->status === 'IZIN')
                                <span class="px-3 py-1 bg-sky-100 text-sky-800 text-xs font-extrabold rounded-full border border-sky-200">IZIN</span>
                            @else
                                <span class="px-3 py-1 bg-rose-100 text-rose-800 text-xs font-extrabold rounded-full border border-rose-200">ALPHA</span>
                            @endif
                        </td>
                        <td class="py-4 px-5 text-surface-600 text-xs">
                            {{ $presensi->keterangan ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-12 text-center text-surface-400">
                            <i data-lucide="calendar-x" class="w-12 h-12 mx-auto mb-3 text-teal-500"></i>
                            <p class="font-bold text-surface-800 text-base">Belum Ada Catatan Kehadiran</p>
                            <p class="text-xs mt-1 text-surface-500">Presensi harian kelas akan ditampilkan secara sistematis di sini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($presensis->hasPages())
            <div class="p-4 border-t border-surface-100">
                {{ $presensis->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
