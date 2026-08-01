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
            
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('guru.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl font-bold text-sm shadow-lg transition-all duration-300 border border-white/30 hover:bg-white/20" style="background: rgba(255,255,255,0.1) !important; color: #ffffff !important;">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Kembali
                </a>
                <a href="{{ route('guru.jadwal-mengajar.cetak') }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl font-bold text-sm shadow-lg transition-all duration-300" style="background: #fbbf24 !important; color: #1e1b4b !important;">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    Cetak Jadwal
                </a>
            </div>
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

    {{-- Jadwal Per Hari (Tabel Gabungan) --}}
    @if(!$semuaJadwal->isEmpty())
        <div class="bg-white rounded-3xl border border-surface-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-surface-200 flex items-center gap-3" style="background: linear-gradient(135deg, #eef2ff, #e0e7ff) !important;">
                <div class="w-9 h-9 rounded-xl bg-primary-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                    <i data-lucide="table" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-bold text-surface-900 text-sm">Jadwal Lengkap Mingguan</h3>
                    <p class="text-xs text-surface-500 font-medium">Senin – Ahad</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface-50 text-surface-500 text-xs uppercase tracking-wider">
                            <th class="px-4 py-3 text-center font-semibold" style="width: 50px;">No</th>
                            <th class="px-4 py-3 text-center font-semibold" style="width: 100px;">Hari</th>
                            <th class="px-4 py-3 text-center font-semibold" style="width: 150px;">Jam Pelajaran</th>
                            <th class="px-4 py-3 text-left font-semibold">Mata Pelajaran</th>
                            <th class="px-4 py-3 text-center font-semibold" style="width: 140px;">Kelas / Rombel</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @php $nomor = 1; @endphp
                        @foreach($hariOrder as $hari)
                            @if(isset($semuaJadwal[$hari]) && $semuaJadwal[$hari]->count() > 0)
                                @foreach($semuaJadwal[$hari] as $index => $jadwal)
                                    <tr class="hover:bg-surface-50 transition-colors">
                                        <td class="px-4 py-3 text-center font-bold text-surface-400">{{ $nomor++ }}</td>
                                        @if($index === 0)
                                            <td class="px-4 py-3 text-center font-bold text-surface-700" rowspan="{{ $semuaJadwal[$hari]->count() }}" style="background-color: #f8fafc; vertical-align: middle;">
                                                <div class="inline-flex flex-col items-center gap-1">
                                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-extrabold text-white" style="background-color: #312e81;">{{ substr($hari, 0, 2) }}</span>
                                                    <span class="text-[0.65rem] font-bold text-surface-600">{{ $hari }}</span>
                                                </div>
                                            </td>
                                        @endif
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex items-center gap-1.5 font-bold text-primary-700 bg-primary-50 px-2.5 py-1 rounded-lg border border-primary-100 text-xs">
                                                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                                {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} – {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 font-semibold text-surface-900">{{ $jadwal->mataPelajaran->nama ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-info-700 bg-info-50 px-2.5 py-1 rounded-lg border border-info-100">
                                                <i data-lucide="users" class="w-3 h-3"></i>
                                                {{ $jadwal->rombel->nama ?? '-' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        {{-- Empty State --}}
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
