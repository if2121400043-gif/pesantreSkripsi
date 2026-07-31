@extends('layouts.guru')

@section('title', 'Jadwal Mengajar — Portal Guru')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="rounded-3xl p-6 md:p-8 shadow-lg relative overflow-hidden text-white" style="background: linear-gradient(135deg, #1e1b4b, #312e81) !important; color: #ffffff !important;">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-xs font-semibold backdrop-blur-sm border border-white/20 mb-3" style="color: #ffffff !important;">
                    <i data-lucide="calendar-days" class="w-3.5 h-3.5 text-warning-400"></i>
                    Jadwal Mengajar Mingguan
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold font-heading" style="color: #ffffff !important;">
                    Jadwal Mengajar
                </h1>
                <p class="text-xs md:text-sm mt-1 max-w-xl" style="color: #c7d2fe !important;">
                    Daftar lengkap seluruh jadwal mengajar Anda dari hari Senin hingga Ahad.
                </p>
            </div>
            
            <a href="{{ route('guru.jadwal-mengajar.cetak') }}" target="_blank" class="shrink-0 inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-white text-indigo-900 font-bold text-sm shadow-lg hover:bg-indigo-50 hover:shadow-xl transition-all duration-300">
                <i data-lucide="printer" class="w-5 h-5"></i>
                Cetak Jadwal
            </a>
        </div>
    </div>

    {{-- Nama Guru --}}
    <div class="bg-white rounded-2xl p-4 border border-surface-200 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
            <i data-lucide="user" class="w-5 h-5"></i>
        </div>
        <div>
            <div class="text-xs text-surface-500 font-medium">Nama Guru / Pengajar</div>
            <div class="text-base font-bold text-surface-900">{{ $pegawai->orang->nama_lengkap ?? auth()->user()->name }}</div>
        </div>
    </div>

    {{-- Jadwal Per Hari --}}
    @php $nomor = 1; @endphp
    @foreach($hariOrder as $hari)
        @if(isset($semuaJadwal[$hari]) && $semuaJadwal[$hari]->count() > 0)
            <div class="bg-white rounded-3xl border border-surface-200 shadow-sm overflow-hidden">
                {{-- Header Hari --}}
                <div class="px-5 py-3.5 border-b border-surface-200 flex items-center gap-3" style="background: linear-gradient(135deg, #eef2ff, #e0e7ff) !important;">
                    <div class="w-9 h-9 rounded-xl bg-primary-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                        {{ substr($hari, 0, 2) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-surface-900 text-sm">{{ $hari }}</h3>
                        <p class="text-xs text-surface-500 font-medium">{{ $semuaJadwal[$hari]->count() }} Sesi Mengajar</p>
                    </div>
                </div>

                {{-- Tabel Jadwal --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-surface-50 text-surface-500 text-xs uppercase tracking-wider">
                                <th class="px-5 py-3 text-center font-semibold w-14">No</th>
                                <th class="px-5 py-3 text-left font-semibold">Jam Pelajaran</th>
                                <th class="px-5 py-3 text-left font-semibold">Mata Pelajaran</th>
                                <th class="px-5 py-3 text-left font-semibold">Kelas / Rombel</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-100">
                            @foreach($semuaJadwal[$hari] as $jadwal)
                                <tr class="hover:bg-surface-50 transition-colors">
                                    <td class="px-5 py-3.5 text-center font-bold text-surface-400">{{ $nomor++ }}</td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center gap-1.5 font-bold text-primary-700 bg-primary-50 px-2.5 py-1 rounded-lg border border-primary-100 text-xs">
                                            <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                            {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} – {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 font-semibold text-surface-900">{{ $jadwal->mataPelajaran->nama ?? '-' }}</td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-info-700 bg-info-50 px-2.5 py-1 rounded-lg border border-info-100">
                                            <i data-lucide="users" class="w-3 h-3"></i>
                                            {{ $jadwal->rombel->nama ?? '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endforeach

    {{-- Empty State --}}
    @if($semuaJadwal->isEmpty())
        <div class="bg-white rounded-3xl border-2 border-dashed border-surface-200 p-12 text-center">
            <div class="w-16 h-16 bg-primary-100 text-primary-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i data-lucide="calendar-x" class="w-8 h-8"></i>
            </div>
            <h3 class="font-bold text-surface-900 text-lg mb-1">Belum Ada Jadwal Mengajar</h3>
            <p class="text-sm text-surface-500">Jadwal mengajar Anda belum diatur oleh admin. Silakan hubungi administrator.</p>
        </div>
    @endif

</div>
@endsection
