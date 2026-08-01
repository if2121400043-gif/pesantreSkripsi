@extends('layouts.app')

@section('title', 'Profil Pegawai: ' . $pegawai->orang->nama_lengkap)

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm text-surface-500 mb-2">
            <a href="{{ route('admin.pegawai.index') }}" class="hover:text-primary-600 transition-colors">Data Pegawai</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-surface-900 font-medium">Profil Kepegawaian</span>
        </div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">{{ $pegawai->orang->nama_lengkap }}</h1>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.pegawai.index') }}" class="btn-secondary flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span class="hidden sm:inline">Kembali</span>
        </a>
        <a href="{{ route('admin.pegawai.edit', $pegawai) }}" class="btn-primary flex items-center gap-2">
            <i data-lucide="edit" class="w-4 h-4"></i>
            <span class="hidden sm:inline">Edit SDM</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    {{-- Kolom Kiri --}}
    <div class="lg:col-span-1 space-y-6">
        <x-card :padding="false">
            <div class="p-6 text-center border-b border-surface-100">
                <div class="w-24 h-24 rounded-full bg-primary-100 text-primary-700 mx-auto flex items-center justify-center text-3xl font-bold mb-4">
                    {{ substr($pegawai->orang->nama_lengkap, 0, 1) }}
                </div>
                <h2 class="text-lg font-bold text-surface-900">{{ $pegawai->orang->nama_lengkap }}</h2>
                <p class="text-surface-500 text-sm mt-1">NIUP: <span class="font-mono text-primary-600 font-medium">{{ $pegawai->orang->niup }}</span></p>
                
                <div class="mt-4">
                    <x-badge variant="info" class="px-3 py-1 text-sm mb-2 block w-max mx-auto">
                        {{ str_replace('_', ' ', $pegawai->jenis_pegawai) }}
                    </x-badge>
                    @if($pegawai->is_active)
                        <span class="text-success-600 text-sm font-semibold flex items-center justify-center gap-1">
                            <i data-lucide="check-circle" class="w-4 h-4"></i> Aktif Bekerja
                        </span>
                    @else
                        <span class="text-danger-600 text-sm font-semibold flex items-center justify-center gap-1">
                            <i data-lucide="x-circle" class="w-4 h-4"></i> Tidak Aktif
                        </span>
                    @endif
                </div>
            </div>
            
            <div class="p-6 space-y-4">
                <div>
                    <p class="text-xs text-surface-500 font-medium mb-1">Status Kepegawaian</p>
                    <p class="font-medium text-surface-900">{{ $pegawai->status_kepegawaian }}</p>
                </div>
                <div>
                    <p class="text-xs text-surface-500 font-medium mb-1">NUPTK (Nasional)</p>
                    <p class="font-medium text-surface-900 font-mono">{{ $pegawai->nuptk ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-surface-500 font-medium mb-1">NIP (Lokal/Pesantren)</p>
                    <p class="font-medium text-surface-900 font-mono">{{ $pegawai->nip ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-surface-500 font-medium mb-1">Mulai Bekerja</p>
                    <p class="font-medium text-surface-900">{{ $pegawai->tanggal_masuk ? $pegawai->tanggal_masuk->format('d M Y') : '-' }}</p>
                </div>
                
                <div class="pt-4 border-t border-surface-100">
                    <a href="{{ route('admin.orang.show', $pegawai->orang_id) }}" class="text-sm font-semibold text-primary-600 hover:text-primary-700 flex items-center justify-between">
                        <span>Lihat Biodata Lengkap Induk</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Kolom Kanan --}}
    <div class="lg:col-span-2 space-y-6">
        
        <x-card title="Data Spesifik Kepegawaian">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-surface-500 font-medium mb-1">Jabatan Struktural</p>
                    <p class="font-medium text-surface-900">{{ $pegawai->jabatan ?? 'Tidak menjabat struktural khusus' }}</p>
                </div>
                <div>
                    <p class="text-xs text-surface-500 font-medium mb-1">Pendidikan Terakhir</p>
                    <p class="font-medium text-surface-900">{{ $pegawai->pendidikan_terakhir ?? 'Belum didata' }}</p>
                </div>
                <div>
                    <p class="text-xs text-surface-500 font-medium mb-1">Jurusan / Program Studi</p>
                    <p class="font-medium text-surface-900">{{ $pegawai->jurusan_pendidikan ?? '-' }}</p>
                </div>
            </div>
        </x-card>

        {{-- Beban Tugas & Tanggung Jawab Operasional --}}
        @if(in_array($pegawai->jenis_pegawai, ['GURU', 'USTADZ', 'PENGASUH']))
            <x-card title="Beban Tugas Mengajar & Pengasuhan">
                <div class="space-y-6">
                    {{-- Summary Metrics --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200">
                            <div class="text-xs font-semibold text-emerald-800 uppercase tracking-wider">Total Beban Mengajar</div>
                            <div class="text-2xl font-extrabold text-emerald-900 mt-1">{{ $totalSesiMingguan }} Sesi <span class="text-xs font-normal text-emerald-700">/minggu</span></div>
                        </div>

                        <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200">
                            <div class="text-xs font-semibold text-blue-800 uppercase tracking-wider">Mata Pelajaran Diampu</div>
                            <div class="text-2xl font-extrabold text-blue-900 mt-1">{{ $mapelDiampu->count() }} Mapel</div>
                        </div>

                        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200">
                            <div class="text-xs font-semibold text-amber-800 uppercase tracking-wider">Amanah Wali Kelas</div>
                            <div class="text-base font-extrabold text-amber-900 mt-1">
                                @if($pegawai->waliKelas->count() > 0)
                                    {{ $pegawai->waliKelas->map(fn($w) => $w->nama)->implode(', ') }}
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
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-800 font-extrabold text-xs border border-emerald-300 shadow-2xs">
                                        <i data-lucide="book-open" class="w-3.5 h-3.5 text-emerald-600"></i>
                                        {{ $mapelName }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-surface-400 italic">Belum ada mata pelajaran yang diampu di jadwal pelajaran.</p>
                        @endif
                    </div>

                    {{-- Tabel Jadwal Mengajar --}}
                    <div>
                        <h4 class="text-xs font-bold text-surface-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                            <i data-lucide="calendar" class="w-4 h-4 text-primary-600"></i>
                            Rincian Jadwal Mengajar Mingguan (Senin – Ahad)
                        </h4>

                        @if(!$jadwalGrouped->isEmpty())
                            <div class="overflow-x-auto rounded-2xl border border-surface-200">
                                <table class="w-full text-xs">
                                    <thead>
                                        <tr class="bg-surface-100 text-surface-600 uppercase tracking-wider font-bold text-[0.65rem]">
                                            <th class="px-4 py-2.5 text-center w-12">No</th>
                                            <th class="px-4 py-2.5 text-center w-20">Hari</th>
                                            <th class="px-4 py-2.5 text-center w-32">Jam Pelajaran</th>
                                            <th class="px-4 py-2.5 text-left">Mata Pelajaran</th>
                                            <th class="px-4 py-2.5 text-center w-28">Kelas / Rombel</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-surface-100 text-surface-800">
                                        @php $nomor = 1; @endphp
                                        @foreach($hariOrder as $hari)
                                            @if(isset($jadwalGrouped[$hari]) && $jadwalGrouped[$hari]->count() > 0)
                                                @foreach($jadwalGrouped[$hari] as $index => $jadwal)
                                                    <tr class="hover:bg-surface-50 transition-colors">
                                                        <td class="px-4 py-2.5 text-center font-bold text-surface-400">{{ $nomor++ }}</td>
                                                        @if($index === 0)
                                                            <td class="px-4 py-2.5 text-center font-bold text-surface-800 bg-surface-50" rowspan="{{ $jadwalGrouped[$hari]->count() }}" style="vertical-align: middle;">
                                                                <span class="inline-block px-2 py-0.5 rounded bg-primary-100 text-primary-800 font-extrabold text-[0.65rem]">{{ $hari }}</span>
                                                            </td>
                                                        @endif
                                                        <td class="px-4 py-2.5 text-center font-mono font-bold text-primary-700">
                                                            {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} – {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                                        </td>
                                                        <td class="px-4 py-2.5 font-bold text-surface-900">{{ $jadwal->mataPelajaran->nama ?? '-' }}</td>
                                                        <td class="px-4 py-2.5 text-center font-bold text-info-700">
                                                            <span class="px-2 py-0.5 rounded bg-info-50 border border-info-200">{{ $jadwal->rombel->nama ?? '-' }}</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-6 text-center bg-surface-50 rounded-2xl border border-surface-200 border-dashed">
                                <i data-lucide="calendar-x" class="w-8 h-8 text-surface-400 mx-auto mb-2"></i>
                                <p class="text-xs text-surface-500 font-medium">Belum ada sesi jadwal pelajaran yang terpasang untuk guru ini.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </x-card>
        @else
            {{-- Staff / Operasional Workload Card --}}
            <x-card title="Beban Tugas & Peran Operasional">
                <div class="space-y-4">
                    <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold shrink-0">
                            <i data-lucide="briefcase" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-blue-800 uppercase tracking-wider">Jabatan & Peran Utama</div>
                            <div class="text-lg font-extrabold text-blue-900 mt-0.5">{{ $pegawai->jabatan ?? 'Staf Operasional Pesantren' }}</div>
                            <div class="text-xs text-blue-700 mt-1">Jenis SDM: <strong>{{ str_replace('_', ' ', $pegawai->jenis_pegawai) }}</strong></div>
                        </div>
                    </div>

                    @if($pegawai->catatan)
                        <div>
                            <h4 class="text-xs font-bold text-surface-500 uppercase tracking-wider mb-2">Rincian Deskripsi Tugas & Tanggung Jawab</h4>
                            <div class="p-4 rounded-2xl bg-surface-50 border border-surface-200 text-xs text-surface-800 leading-relaxed whitespace-pre-wrap font-medium">
                                {{ $pegawai->catatan }}
                            </div>
                        </div>
                    @endif
                </div>
            </x-card>
        @endif

        {{-- Riwayat Jabatan (Timeline) --}}
        <x-card title="Riwayat Jabatan & Kepengurusan">
            <div class="space-y-4">
                @if($pegawai->riwayatJabatan->count() > 0)
                    <div class="relative border-l-2 border-surface-200 ml-3 py-2 space-y-6">
                        @foreach($pegawai->riwayatJabatan as $rj)
                            <div class="relative pl-6">
                                <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-white border-2 border-primary-500"></div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-bold text-surface-900">{{ $rj->jabatan }}</h4>
                                        <x-badge variant="info" class="text-[0.65rem] px-1.5 py-0.5">{{ $rj->jenis_pegawai }}</x-badge>
                                        @if(is_null($rj->tanggal_selesai))
                                            <x-badge variant="success" class="text-[0.65rem] px-1.5 py-0.5">Aktif Saat Ini</x-badge>
                                        @endif
                                    </div>
                                    <p class="text-xs text-surface-500 mt-1">
                                        Periode: {{ $rj->tanggal_mulai->format('d M Y') }} - 
                                        {{ $rj->tanggal_selesai ? $rj->tanggal_selesai->format('d M Y') : 'Sekarang' }}
                                    </p>
                                    @if($rj->keterangan)
                                        <p class="text-sm text-surface-600 mt-2 bg-surface-50 p-2.5 rounded-lg border border-surface-100 italic">
                                            {{ $rj->keterangan }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 bg-surface-50 rounded-lg border border-surface-100 border-dashed">
                        <i data-lucide="award" class="w-8 h-8 text-surface-300 mx-auto mb-2"></i>
                        <p class="text-sm text-surface-500">Belum ada catatan riwayat perpindahan jabatan. Jabatan saat ini: <strong>{{ $pegawai->jabatan ?? '-' }}</strong></p>
                    </div>
                @endif
            </div>
        </x-card>

        {{-- Catatan Tambahan --}}
        <x-card title="Catatan SDM">
            @if($pegawai->catatan)
                <p class="text-surface-700 whitespace-pre-wrap">{{ $pegawai->catatan }}</p>
            @else
                <p class="text-surface-400 italic">Tidak ada catatan kepegawaian.</p>
            @endif
        </x-card>
        
    </div>
</div>
@endsection
