@extends('layouts.guru')

@section('title', 'Dashboard Guru — PP Nurul Furqon')

@section('content')
<div class="space-y-6">

    {{-- Welcome Banner Header --}}
    <div class="rounded-3xl p-6 md:p-8 shadow-lg relative overflow-hidden text-white" style="background: linear-gradient(135deg, #1e1b4b, #312e81) !important; color: #ffffff !important;">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-xs font-semibold backdrop-blur-sm border border-white/20 mb-3" style="color: #ffffff !important;">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-warning-400"></i>
                    Portal Operasional Guru & Pengajar
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold font-heading" style="color: #ffffff !important;">
                    Ahlan wa Sahlan, {{ auth()->user()->orang->nama_lengkap ?? auth()->user()->name }}
                </h1>
                <p class="text-xs md:text-sm mt-1 max-w-xl" style="color: #c7d2fe !important;">
                    Pilih menu aksi cepat di bawah ini untuk pencatatan presensi, input nilai rapor, atau laporan kedisiplinan santri hari ini.
                </p>
            </div>
            
            <div class="flex items-center gap-3 shrink-0 p-3 rounded-2xl border border-white/20" style="background-color: rgba(255, 255, 255, 0.15) !important;">
                <div class="w-10 h-10 rounded-xl bg-warning-400/20 text-warning-300 flex items-center justify-center font-bold">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                </div>
                <div>
                    <div class="text-[0.65rem] uppercase tracking-wider font-semibold" style="color: #c7d2fe !important;">Hari Ini</div>
                    <div class="text-sm font-bold" style="color: #ffffff !important;">{{ $hariIni }}, {{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Bar --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-surface-200 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
                <i data-lucide="calendar-clock" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-xs text-surface-500 font-medium">Jadwal Hari Ini</div>
                <div class="text-lg font-bold text-surface-900">{{ $jadwalHariIni->count() }} Kelas</div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-surface-200 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-info-50 text-info-600 flex items-center justify-center shrink-0">
                <i data-lucide="book-open" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-xs text-surface-500 font-medium">Kelas Diampu</div>
                <div class="text-lg font-bold text-surface-900">{{ $totalKelasDiajar }} Rombel</div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-surface-200 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-success-50 text-success-600 flex items-center justify-center shrink-0">
                <i data-lucide="award" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-xs text-surface-500 font-medium">Amanah Wali Kelas</div>
                <div class="text-lg font-bold text-surface-900">
                    {{ $waliKelas->count() > 0 ? $waliKelas->first()->nama : 'Bukan Wali Kelas' }}
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN ACTION CARDS GRID (Aksi Utama Guru) --}}
    <div>
        <h2 class="text-base font-bold text-surface-900 mb-3 flex items-center gap-2">
            <i data-lucide="grid" class="w-5 h-5 text-primary-600"></i>
            Menu Utama Guru & Pengajar
        </h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            {{-- Card 1: Input Presensi --}}
            <a href="{{ route('guru.presensi.index') }}" class="group bg-white rounded-3xl p-5 border border-surface-200 shadow-sm hover:shadow-xl hover:border-primary-400 hover:-translate-y-1 transition-all duration-300 relative overflow-hidden flex flex-col justify-between">
                <div class="absolute -right-8 -top-8 w-28 h-28 bg-primary-100/50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center shadow-md shadow-primary-500/20 mb-4 group-hover:rotate-6 transition-transform">
                        <i data-lucide="clipboard-check" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-lg font-bold text-surface-900 group-hover:text-primary-600 transition-colors">Presensi Santri</h3>
                    <p class="text-xs text-surface-500 mt-1 leading-relaxed">Catat kehadiran santri harian pada kelas dan jadwal pelajaran yang diampu.</p>
                </div>
                <div class="mt-6 pt-3 border-t border-surface-100 flex items-center justify-between text-xs font-bold text-primary-600">
                    <span>Isi Absen Kelas</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            {{-- Card 2: Input Nilai --}}
            <a href="{{ route('guru.penilaian.index') }}" class="group bg-white rounded-3xl p-5 border border-surface-200 shadow-sm hover:shadow-xl hover:border-success-400 hover:-translate-y-1 transition-all duration-300 relative overflow-hidden flex flex-col justify-between">
                <div class="absolute -right-8 -top-8 w-28 h-28 bg-success-100/50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-success-500 to-emerald-700 text-white flex items-center justify-center shadow-md shadow-success-500/20 mb-4 group-hover:rotate-6 transition-transform">
                        <i data-lucide="award" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-lg font-bold text-surface-900 group-hover:text-success-600 transition-colors">Input Nilai Rapor</h3>
                    <p class="text-xs text-surface-500 mt-1 leading-relaxed">Entry nilai tugas, harian, UTS, dan UAS untuk santri pada mata pelajaran diampu.</p>
                </div>
                <div class="mt-6 pt-3 border-t border-surface-100 flex items-center justify-between text-xs font-bold text-success-600">
                    <span>Input Nilai Mapel</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            {{-- Card 3: Kedisiplinan & Pelanggaran --}}
            <a href="{{ route('guru.kedisiplinan.index') }}" class="group bg-white rounded-3xl p-5 border border-surface-200 shadow-sm hover:shadow-xl hover:border-danger-400 hover:-translate-y-1 transition-all duration-300 relative overflow-hidden flex flex-col justify-between">
                <div class="absolute -right-8 -top-8 w-28 h-28 bg-danger-100/50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-rose-500 to-danger-700 text-white flex items-center justify-center shadow-md shadow-rose-500/20 mb-4 group-hover:rotate-6 transition-transform">
                        <i data-lucide="shield-alert" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-lg font-bold text-surface-900 group-hover:text-rose-600 transition-colors">Lapor Kedisiplinan</h3>
                    <p class="text-xs text-surface-500 mt-1 leading-relaxed">Catat poin pelanggaran atau catatan apresiasi prestasi santri secara fleksibel.</p>
                </div>
                <div class="mt-6 pt-3 border-t border-surface-100 flex items-center justify-between text-xs font-bold text-rose-600">
                    <span>Catat Pelanggaran/Prestasi</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            {{-- Card 4: Jadwal Mengajar & Cetak --}}
            <a href="{{ route('guru.jadwal-mengajar') }}" class="group bg-white rounded-3xl p-5 border border-surface-200 shadow-sm hover:shadow-xl hover:border-amber-400 hover:-translate-y-1 transition-all duration-300 relative overflow-hidden flex flex-col justify-between">
                <div class="absolute -right-8 -top-8 w-28 h-28 bg-amber-100/50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="w-14 h-14 rounded-2xl text-white flex items-center justify-center shadow-md mb-4 group-hover:rotate-6 transition-transform" style="background: linear-gradient(135deg, #f59e0b, #d97706) !important; color: #ffffff !important;">
                        <i data-lucide="calendar-days" class="w-7 h-7" style="color: #ffffff !important;"></i>
                    </div>
                    <h3 class="text-lg font-bold text-surface-900 group-hover:text-amber-600 transition-colors">Jadwal Mengajar</h3>
                    <p class="text-xs text-surface-500 mt-1 leading-relaxed">Lihat jadwal mengajar mingguan lengkap dan cetak dalam format dokumen resmi.</p>
                </div>
                <div class="mt-6 pt-3 border-t border-surface-100 flex items-center justify-between text-xs font-bold text-amber-600">
                    <span>Lihat & Cetak Jadwal</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

        </div>
    </div>

    {{-- Jadwal Mengajar Hari Ini Section --}}
    <div class="bg-white rounded-3xl p-6 border border-surface-200 shadow-sm">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-surface-900 text-base flex items-center gap-2">
                <i data-lucide="clock" class="w-5 h-5 text-primary-600"></i>
                Jadwal Mengajar Hari Ini ({{ $hariIni }})
            </h3>
            <span class="text-xs text-surface-500 font-medium">{{ $jadwalHariIni->count() }} Kelas</span>
        </div>

        @if($jadwalHariIni->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface-50 text-surface-500 text-xs uppercase tracking-wider">
                            <th class="px-4 py-3 text-center font-semibold w-14">No</th>
                            <th class="px-4 py-3 text-left font-semibold">Jam</th>
                            <th class="px-4 py-3 text-left font-semibold">Mata Pelajaran</th>
                            <th class="px-4 py-3 text-left font-semibold">Kelas</th>
                            <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @foreach($jadwalHariIni as $index => $jadwal)
                            <tr class="hover:bg-surface-50 transition-colors">
                                <td class="px-4 py-3 text-center font-bold text-surface-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1.5 font-bold text-primary-700 bg-primary-50 px-2.5 py-1 rounded-lg border border-primary-100 text-xs">
                                        <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                        {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} – {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-semibold text-surface-900">{{ $jadwal->mataPelajaran->nama ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-info-700 bg-info-50 px-2.5 py-1 rounded-lg border border-info-100">
                                        <i data-lucide="users" class="w-3 h-3"></i>
                                        {{ $jadwal->rombel->nama ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('guru.presensi.create', $jadwal->id) }}" class="btn-primary text-xs py-1.5 px-3 rounded-xl flex items-center gap-1">
                                            <i data-lucide="check-square" class="w-3.5 h-3.5"></i> Absen
                                        </a>
                                        <a href="{{ route('guru.penilaian.create', $jadwal->id) }}" class="btn-secondary text-xs py-1.5 px-3 rounded-xl flex items-center gap-1">
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Nilai
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-10 text-center border-2 border-dashed border-surface-200 rounded-2xl bg-surface-50">
                <div class="w-12 h-12 bg-primary-100 text-primary-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="coffee" class="w-6 h-6"></i>
                </div>
                <h4 class="font-bold text-surface-900 text-sm">Tidak Ada Jadwal Mengajar Hari Ini</h4>
                <p class="text-xs text-surface-500 mt-1">Alhamdulillah, tidak ada jam tatap muka kelas pada hari {{ $hariIni }}.</p>
            </div>
        @endif
    </div>

</div>
@endsection
