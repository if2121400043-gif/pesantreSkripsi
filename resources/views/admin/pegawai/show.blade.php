@extends('layouts.app')

@section('title', 'Profil & Beban Tugas: ' . $pegawai->orang->nama_lengkap)

@section('content')
<div class="space-y-6">

    {{-- Hero Profile Cover Header Banner --}}
    <div class="rounded-3xl p-6 md:p-8 shadow-lg relative overflow-hidden text-white" style="background: linear-gradient(135deg, #047857, #065f46) !important; color: #ffffff !important;">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            
            <div class="flex items-center gap-4">
                {{-- Large Avatar Badge --}}
                <div class="w-20 h-20 rounded-2xl font-black text-2xl flex items-center justify-center shrink-0 shadow-xl border-2 border-white/30" style="background: #ffffff !important; color: #047857 !important;">
                    {{ substr($pegawai->orang->nama_lengkap, 0, 1) }}
                </div>
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-xs font-semibold backdrop-blur-sm border border-white/20 mb-2" style="color: #ffffff !important;">
                        <i data-lucide="badge-check" class="w-3.5 h-3.5 text-warning-300"></i>
                        {{ str_replace('_', ' ', $pegawai->jenis_pegawai) }}
                    </div>
                    <h1 class="text-2xl md:text-3xl font-extrabold font-heading" style="color: #ffffff !important;">
                        {{ $pegawai->orang->nama_lengkap }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-3 text-xs mt-1.5" style="color: #a7f3d0 !important;">
                        <span>NIUP: <strong class="font-mono text-white">{{ $pegawai->orang->niup }}</strong></span>
                        <span>•</span>
                        <span>NIP: <strong class="font-mono text-white">{{ $pegawai->nip ?? '-' }}</strong></span>
                        <span>•</span>
                        @if($pegawai->is_active)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold shadow-sm border" style="background-color: #ffffff !important; color: #047857 !important; border-color: #a7f3d0 !important;">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Aktif Bekerja
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold shadow-sm border" style="background-color: #ffffff !important; color: #e11d48 !important; border-color: #fecdd3 !important;">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                Nonaktif
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Header Actions --}}
            <div class="flex items-center gap-3 shrink-0 self-end md:self-auto">
                <a href="{{ route('admin.pegawai.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl font-bold text-xs shadow-lg transition-all border border-white/30 hover:bg-white/20" style="background: rgba(255,255,255,0.1) !important; color: #ffffff !important;">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Kembali</span>
                </a>
                <a href="{{ route('admin.pegawai.edit', $pegawai) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl font-extrabold text-xs shadow-xl transition-all hover:scale-105" style="background-color: #fbbf24 !important; color: #1e1b4b !important;">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                    <span>Edit SDM</span>
                </a>
            </div>

        </div>
    </div>

    {{-- TINGKAT 1: BEBAN TUGAS & RINCIAN JADWAL (FULL WIDTH - LEBAR PENUH 100%) --}}
    @if(in_array($pegawai->jenis_pegawai, ['GURU', 'USTADZ', 'PENGASUH']))
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-surface-200 shadow-sm space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-surface-100">
                <h3 class="font-extrabold text-surface-900 text-lg flex items-center gap-2">
                    <i data-lucide="book-open" class="w-6 h-6 text-emerald-700"></i>
                    Beban Tugas Mengajar & Pengasuhan
                </h3>
                <span class="text-xs font-extrabold px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200">
                    {{ $totalSesiMingguan }} Sesi / Minggu
                </span>
            </div>

            {{-- 3 Stat Metrics --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-5 rounded-2xl bg-emerald-50/80 border border-emerald-200">
                    <div class="text-[0.68rem] font-bold text-emerald-800 uppercase tracking-wider">Total Beban Mengajar</div>
                    <div class="text-2xl font-extrabold text-emerald-900 mt-1">{{ $totalSesiMingguan }} <span class="text-xs font-semibold text-emerald-700">Sesi/Minggu</span></div>
                </div>

                <div class="p-5 rounded-2xl bg-blue-50/80 border border-blue-200">
                    <div class="text-[0.68rem] font-bold text-blue-800 uppercase tracking-wider">Mata Pelajaran Diampu</div>
                    <div class="text-2xl font-extrabold text-blue-900 mt-1">{{ $mapelDiampu->count() }} <span class="text-xs font-semibold text-blue-700">Mapel</span></div>
                </div>

                <div class="p-5 rounded-2xl bg-amber-50/80 border border-amber-200">
                    <div class="text-[0.68rem] font-bold text-amber-800 uppercase tracking-wider">Amanah Wali Kelas</div>
                    <div class="text-base font-extrabold text-amber-900 mt-1">
                        @if($pegawai->waliKelas->count() > 0)
                            👑 {{ $pegawai->waliKelas->map(fn($w) => $w->nama)->implode(', ') }}
                        @else
                            <span class="text-surface-500 font-normal text-xs">Bukan Wali Kelas</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Daftar Mata Pelajaran --}}
            <div>
                <h4 class="text-xs font-bold text-surface-500 uppercase tracking-wider mb-2">Mata Pelajaran yang Diajarkan</h4>
                @if($mapelDiampu->count() > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($mapelDiampu as $mapelName)
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 font-extrabold text-xs border border-emerald-200 shadow-2xs">
                                <i data-lucide="book" class="w-3.5 h-3.5 text-emerald-600"></i>
                                {{ $mapelName }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-surface-400 italic">Belum ada mata pelajaran yang diampu di jadwal pelajaran.</p>
                @endif
            </div>

            {{-- Tabel Jadwal Mengajar Mingguan (membentang 100% penuh) --}}
            <div>
                <h4 class="text-xs font-bold text-surface-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4 text-emerald-700"></i>
                    Rincian Jadwal Mengajar Mingguan (Senin – Ahad)
                </h4>

                @if(!$jadwalGrouped->isEmpty())
                    <div class="overflow-x-auto rounded-2xl border border-surface-200">
                        <table class="w-full text-xs" style="table-layout: fixed;">
                            <thead>
                                <tr class="bg-surface-100/80 text-surface-600 uppercase tracking-wider font-bold text-[0.68rem] border-b border-surface-200">
                                    <th class="px-4 py-3 text-center" style="width: 6%;">No</th>
                                    <th class="px-4 py-3 text-center" style="width: 14%;">Hari</th>
                                    <th class="px-4 py-3 text-center" style="width: 22%;">Jam Pelajaran</th>
                                    <th class="px-4 py-3 text-left" style="width: 38%;">Mata Pelajaran</th>
                                    <th class="px-4 py-3 text-center" style="width: 20%;">Kelas / Rombel</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-100 text-surface-800">
                                @php $nomor = 1; @endphp
                                @foreach($hariOrder as $hari)
                                    @if(isset($jadwalGrouped[$hari]) && $jadwalGrouped[$hari]->count() > 0)
                                        @foreach($jadwalGrouped[$hari] as $index => $jadwal)
                                            <tr class="hover:bg-surface-50 transition-colors">
                                                <td class="px-4 py-3 text-center font-bold text-surface-400">{{ $nomor++ }}</td>
                                                @if($index === 0)
                                                    <td class="px-4 py-3 text-center font-bold text-surface-800 bg-surface-50/70" rowspan="{{ $jadwalGrouped[$hari]->count() }}" style="vertical-align: middle;">
                                                        <span class="inline-block px-3 py-1 rounded-xl text-white font-extrabold text-[0.7rem] shadow-2xs" style="color: #ffffff !important; background-color: #047857 !important;">{{ $hari }}</span>
                                                    </td>
                                                @endif
                                                <td class="px-4 py-3 text-center font-mono font-bold text-emerald-800">
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-xs">
                                                        <i data-lucide="clock" class="w-3.5 h-3.5 text-emerald-600"></i>
                                                        {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} – {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 font-extrabold text-surface-900 text-sm">{{ $jadwal->mataPelajaran->nama ?? '-' }}</td>
                                                <td class="px-4 py-3 text-center font-bold text-info-700">
                                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl bg-info-50 text-info-800 border border-info-200 text-xs">
                                                        <i data-lucide="users" class="w-3.5 h-3.5"></i>
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
                @else
                    <div class="p-8 text-center bg-surface-50 rounded-2xl border border-surface-200 border-dashed">
                        <i data-lucide="calendar-x" class="w-10 h-10 text-surface-400 mx-auto mb-2"></i>
                        <p class="text-xs text-surface-500 font-medium">Belum ada sesi jadwal pelajaran yang terpasang untuk guru ini.</p>
                    </div>
                @endif
            </div>
        </div>
    @else
        {{-- Staff / Operasional Workload Card --}}
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-surface-200 shadow-sm space-y-4">
            <h3 class="font-extrabold text-surface-900 text-lg flex items-center gap-2 pb-3 border-b border-surface-100">
                <i data-lucide="briefcase" class="w-6 h-6 text-blue-700"></i>
                Beban Tugas & Peran Operasional
            </h3>

            <div class="p-5 rounded-2xl bg-blue-50 border border-blue-200 flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-bold shrink-0 shadow-md">
                    <i data-lucide="briefcase" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-blue-800 uppercase tracking-wider">Jabatan & Peran Utama</div>
                    <div class="text-xl font-extrabold text-blue-900 mt-0.5">{{ $pegawai->jabatan ?? 'Staf Operasional Pesantren' }}</div>
                    <div class="text-xs text-blue-700 mt-1">Jenis SDM: <strong>{{ str_replace('_', ' ', $pegawai->jenis_pegawai) }}</strong></div>
                </div>
            </div>

            @if($pegawai->catatan)
                <div class="pt-2">
                    <h4 class="text-xs font-bold text-surface-500 uppercase tracking-wider mb-2">Deskripsi Uraian Tugas & Catatan SDM</h4>
                    <div class="p-4 rounded-2xl bg-surface-50 border border-surface-200 text-xs text-surface-800 leading-relaxed whitespace-pre-wrap font-medium">
                        {{ $pegawai->catatan }}
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- TINGKAT 2: 2 KOLOM SEIMBANG (GRID 50% : 50%) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- KOLOM KIRI: STATUS & INFORMASI KEPEGAWAIAN --}}
        <div class="bg-white rounded-3xl p-6 border border-surface-200 shadow-sm space-y-5 flex flex-col justify-between">
            <div>
                <h3 class="font-extrabold text-surface-900 text-base flex items-center gap-2 pb-3 border-b border-surface-100">
                    <i data-lucide="shield-check" class="w-5 h-5 text-emerald-700"></i>
                    Informasi & Status Kepegawaian
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs pt-4">
                    <div>
                        <div class="text-surface-500 font-medium">Status Pekerjaan</div>
                        <div class="font-extrabold text-surface-900 text-sm mt-0.5">{{ $pegawai->status_kepegawaian }}</div>
                    </div>
                    <div>
                        <div class="text-surface-500 font-medium">Jabatan Utama</div>
                        <div class="font-extrabold text-surface-900 text-sm mt-0.5">{{ $pegawai->jabatan ?? 'Staf Operasional' }}</div>
                    </div>
                    <div>
                        <div class="text-surface-500 font-medium">NUPTK (Nasional)</div>
                        <div class="font-mono font-bold text-surface-900 mt-0.5">{{ $pegawai->nuptk ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-surface-500 font-medium">Tanggal Mulai Bekerja</div>
                        <div class="font-bold text-surface-900 mt-0.5">{{ $pegawai->tanggal_masuk ? $pegawai->tanggal_masuk->format('d M Y') : '-' }}</div>
                    </div>
                    <div class="sm:col-span-2">
                        <div class="text-surface-500 font-medium">Pendidikan Terakhir</div>
                        <div class="font-bold text-surface-900 mt-0.5">
                            {{ $pegawai->pendidikan_terakhir ?? '-' }} 
                            @if($pegawai->jurusan_pendidikan) 
                                ({{ $pegawai->jurusan_pendidikan }}) 
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-surface-100">
                <a href="{{ route('admin.orang.show', $pegawai->orang_id) }}" class="inline-flex items-center justify-between w-full p-3 rounded-2xl bg-surface-50 hover:bg-emerald-50 text-emerald-800 font-bold text-xs border border-surface-200 hover:border-emerald-300 transition-all">
                    <span class="flex items-center gap-2">
                        <i data-lucide="user" class="w-4 h-4 text-emerald-600"></i>
                        Biodata Induk Orang
                    </span>
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </a>
            </div>
        </div>

        {{-- KOLOM KANAN: RIWAYAT JABATAN & CATATAN --}}
        <div class="space-y-6">
            <div class="bg-white rounded-3xl p-6 border border-surface-200 shadow-sm space-y-4">
                <h3 class="font-extrabold text-surface-900 text-base flex items-center gap-2 pb-3 border-b border-surface-100">
                    <i data-lucide="history" class="w-5 h-5 text-indigo-700"></i>
                    Riwayat Jabatan & Kepengurusan
                </h3>

                @if($pegawai->riwayatJabatan->count() > 0)
                    <div class="relative border-l-2 border-surface-200 ml-3 py-2 space-y-5">
                        @foreach($pegawai->riwayatJabatan as $rj)
                            <div class="relative pl-6">
                                <div class="absolute -left-[9px] top-1.5 w-4 h-4 rounded-full bg-white border-2 border-emerald-600"></div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-bold text-surface-900 text-sm">{{ $rj->jabatan }}</h4>
                                        <span class="px-2 py-0.5 rounded text-[0.65rem] font-bold bg-blue-100 text-blue-800 uppercase">{{ $rj->jenis_pegawai }}</span>
                                        @if(is_null($rj->tanggal_selesai))
                                            <span class="px-2 py-0.5 rounded text-[0.65rem] font-bold bg-emerald-100 text-emerald-800">Aktif Saat Ini</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-surface-500 mt-1">
                                        Periode: {{ $rj->tanggal_mulai->format('d M Y') }} - 
                                        {{ $rj->tanggal_selesai ? $rj->tanggal_selesai->format('d M Y') : 'Sekarang' }}
                                    </p>
                                    @if($rj->keterangan)
                                        <p class="text-xs text-surface-600 mt-2 bg-surface-50 p-3 rounded-xl border border-surface-100 italic">
                                            {{ $rj->keterangan }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 bg-surface-50 rounded-2xl border border-surface-100 border-dashed">
                        <i data-lucide="award" class="w-8 h-8 text-surface-300 mx-auto mb-2"></i>
                        <p class="text-xs text-surface-500">Belum ada catatan riwayat perpindahan jabatan. Jabatan saat ini: <strong>{{ $pegawai->jabatan ?? '-' }}</strong></p>
                    </div>
                @endif
            </div>

            @if($pegawai->catatan)
                <div class="bg-white rounded-3xl p-6 border border-surface-200 shadow-sm space-y-3">
                    <h3 class="font-extrabold text-surface-900 text-base flex items-center gap-2 pb-2 border-b border-surface-100">
                        <i data-lucide="file-text" class="w-4 h-4 text-surface-500"></i>
                        Catatan SDM
                    </h3>
                    <p class="text-xs text-surface-700 whitespace-pre-wrap leading-relaxed">{{ $pegawai->catatan }}</p>
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
