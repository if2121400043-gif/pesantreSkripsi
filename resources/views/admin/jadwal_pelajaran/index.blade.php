@extends('layouts.app')

@section('title', 'Jadwal Pelajaran — PP Nurul Furqon')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2 text-xs text-surface-500 mb-1">
            <span class="hover:text-emerald-700 font-medium">Akademik</span>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            <span class="text-surface-900 font-bold">Jadwal Pelajaran</span>
        </div>
        <h1 class="text-2xl font-extrabold text-surface-900 font-heading">Jadwal Pelajaran</h1>
        <p class="text-xs text-surface-500 mt-0.5">Kelola jam mengajar guru dan alokasi jadwal mata pelajaran per kelas.</p>
    </div>
    <div class="flex flex-wrap items-center gap-2.5 shrink-0">
        <a href="{{ route('admin.jadwal-pelajaran.export-csv', ['tahun_pelajaran_id' => $tahunId, 'rombel_id' => $rombelId, 'hari' => $hari, 'pegawai_id' => $pegawaiId]) }}" class="inline-flex items-center gap-2 py-2.5 px-4 rounded-2xl bg-emerald-50 text-emerald-800 hover:bg-emerald-700 hover:text-white border border-emerald-300 font-bold text-xs transition-all shadow-xs">
            <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
            <span>Cetak Roster CSV</span>
        </a>
        <a href="{{ route('admin.jadwal-pelajaran.create', ['rombel_id' => $rombelId, 'tahun_pelajaran_id' => $tahunId]) }}" class="btn-primary flex items-center gap-2 py-2.5 px-4 rounded-2xl font-bold text-xs shadow-md shadow-emerald-700/20">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>+ Tambah Jadwal Baru</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6 pb-12">

    {{-- Grid 3 Filter Controls Header (Hari, Kelas, Guru) --}}
    <div class="bg-white rounded-3xl border border-surface-200 shadow-md p-5 sm:p-6 overflow-hidden">
        <form action="{{ route('admin.jadwal-pelajaran.index') }}" method="GET" class="space-y-4">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-surface-100">
                <div class="flex items-center gap-2.5 text-surface-900 font-extrabold text-sm">
                    <div class="w-9 h-9 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0 border border-emerald-200">
                        <i data-lucide="filter" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <span class="font-heading">Filterisasi Jadwal Pelajaran</span>
                        <p class="text-[0.7rem] text-surface-500 font-normal">Pilih kriteria untuk menyaring daftar jadwal.</p>
                    </div>
                </div>

                {{-- Select Tahun Pelajaran --}}
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-xs font-bold text-surface-600 hidden sm:inline">Tahun Ajaran:</span>
                    <select name="tahun_pelajaran_id" class="px-3.5 py-2 rounded-xl border border-surface-300 bg-surface-50 text-xs font-bold text-surface-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600" onchange="this.form.submit()">
                        @foreach($tahuns as $t)
                            <option value="{{ $t->id }}" {{ $tahunId == $t->id ? 'selected' : '' }}>{{ $t->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Responsive Grid: 3 Main Filters (Hari, Kelas, Guru) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                {{-- Filter 1: Hari --}}
                <div>
                    <label class="block text-xs font-extrabold text-surface-700 mb-1.5 flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-emerald-600"></i>
                        <span>1. Filter Hari</span>
                    </label>
                    <select name="hari" class="w-full px-3.5 py-2.5 rounded-2xl border border-surface-300 bg-white text-xs font-bold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all" onchange="this.form.submit()">
                        <option value="">Semua Hari (Senin - Ahad)</option>
                        @foreach($daftarHari as $h)
                            <option value="{{ $h }}" {{ $hari === $h ? 'selected' : '' }}>Hari {{ ucfirst(strtolower($h)) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter 2: Kelas / Rombel --}}
                <div>
                    <label class="block text-xs font-extrabold text-surface-700 mb-1.5 flex items-center gap-1.5">
                        <i data-lucide="school" class="w-3.5 h-3.5 text-emerald-600"></i>
                        <span>2. Filter Kelas</span>
                    </label>
                    <select name="rombel_id" class="w-full px-3.5 py-2.5 rounded-2xl border border-surface-300 bg-white text-xs font-bold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all" onchange="this.form.submit()">
                        <option value="">Semua Kelas / Rombel</option>
                        @foreach($rombels as $r)
                            <option value="{{ $r->id }}" {{ $rombelId == $r->id ? 'selected' : '' }}>
                                {{ $r->lembaga->singkatan ?? $r->lembaga->nama }} | {{ str_starts_with(strtoupper($r->nama ?? ''), 'KELAS') ? strtoupper($r->nama) : 'KELAS ' . strtoupper($r->nama) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter 3: Guru / Pengajar --}}
                <div>
                    <label class="block text-xs font-extrabold text-surface-700 mb-1.5 flex items-center gap-1.5">
                        <i data-lucide="user-check" class="w-3.5 h-3.5 text-emerald-600"></i>
                        <span>3. Filter Guru Pengajar</span>
                    </label>
                    <select name="pegawai_id" class="w-full px-3.5 py-2.5 rounded-2xl border border-surface-300 bg-white text-xs font-bold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all" onchange="this.form.submit()">
                        <option value="">Semua Guru Pengajar</option>
                        @foreach($gurus as $g)
                            <option value="{{ $g->id }}" {{ $pegawaiId == $g->id ? 'selected' : '' }}>
                                {{ $g->orang->nama_lengkap ?? 'Guru' }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            {{-- Active Filters Reset Tag Bar --}}
            @if($hari || $rombelId || $pegawaiId)
            <div class="flex items-center justify-between pt-2 border-t border-surface-100 text-xs">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-surface-500 font-medium">Filter aktif:</span>
                    @if($hari)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[0.7rem]">
                            Hari: {{ ucfirst(strtolower($hari)) }}
                        </span>
                    @endif
                    @if($rombelId)
                        @php $rSelected = $rombels->firstWhere('id', $rombelId); @endphp
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 font-extrabold text-[0.7rem]">
                            Kelas: {{ $rSelected->nama ?? '-' }}
                        </span>
                    @endif
                    @if($pegawaiId)
                        @php $gSelected = $gurus->firstWhere('id', $pegawaiId); @endphp
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 font-extrabold text-[0.7rem]">
                            Guru: {{ $gSelected->orang->nama_lengkap ?? '-' }}
                        </span>
                    @endif
                </div>
                <a href="{{ route('admin.jadwal-pelajaran.index', ['tahun_pelajaran_id' => $tahunId]) }}" class="text-rose-600 hover:text-rose-800 font-bold hover:underline">
                    Reset Filter
                </a>
            </div>
            @endif

        </form>
    </div>

    {{-- Results Counter Bar --}}
    <div class="flex items-center justify-between px-1">
        <div class="flex items-center gap-2 text-surface-800 font-extrabold text-sm font-heading">
            <i data-lucide="layout-grid" class="w-4 h-4 text-emerald-600"></i>
            <span>Daftar Hasil Jadwal Pelajaran ({{ $jadwals->count() }} Data)</span>
        </div>
    </div>

    {{-- Grid Layout Hasil Filterisasi (Cards with Shadow boundary) --}}
    @if($jadwals->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($jadwals as $j)
                <div class="bg-white rounded-3xl border border-surface-200 shadow-md hover:shadow-xl hover:border-emerald-400 transition-all duration-300 p-6 relative overflow-hidden flex flex-col justify-between group">
                    
                    {{-- Decorative Top Accent Line --}}
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600"></div>

                    <div>
                        {{-- Header Row: Hari Badge & Time Slot --}}
                        <div class="flex items-center justify-between gap-2 mb-4">
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-100 text-emerald-900 border border-emerald-200">
                                <i data-lucide="calendar" class="w-3 h-3 text-emerald-700"></i>
                                <span>{{ ucfirst(strtolower($j->hari)) }}</span>
                            </span>
                            
                            {{-- Jam Mulai - Jam Berakhir --}}
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-100 text-surface-800 text-xs font-mono font-bold border border-surface-200">
                                <i data-lucide="clock" class="w-3.5 h-3.5 text-surface-500"></i>
                                <span>{{ date('H:i', strtotime($j->jam_mulai)) }} - {{ date('H:i', strtotime($j->jam_selesai)) }}</span>
                            </div>
                        </div>

                        {{-- 1. Nama Mata Pelajaran --}}
                        <div class="mb-4">
                            <h3 class="text-lg font-extrabold text-surface-900 font-heading leading-snug group-hover:text-emerald-700 transition-colors">
                                {{ $j->mataPelajaran->nama ?? '-' }}
                            </h3>
                            @if($j->mataPelajaran->kode)
                                <span class="text-[0.68rem] text-surface-450 font-mono">Kode Mapel: {{ $j->mataPelajaran->kode }}</span>
                            @endif
                        </div>

                        {{-- 2. Nama Kelas & 3. Nama Guru --}}
                        <div class="space-y-2.5 pt-3 border-t border-surface-100 text-xs">
                            
                            {{-- Nama Kelas --}}
                            <div class="flex items-center gap-2 text-surface-700 font-bold">
                                <div class="w-7 h-7 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center shrink-0 border border-blue-100">
                                    <i data-lucide="school" class="w-3.5 h-3.5"></i>
                                </div>
                                <span class="text-surface-900 font-extrabold">
                                    {{ str_starts_with(strtoupper($j->rombel->nama ?? ''), 'KELAS') ? strtoupper($j->rombel->nama) : 'KELAS ' . strtoupper($j->rombel->nama ?? '-') }}
                                </span>
                                @if($j->rombel->lembaga)
                                    <span class="text-[0.65rem] px-2 py-0.5 rounded-md bg-surface-100 text-surface-600 font-bold border border-surface-200 ml-auto">
                                        {{ $j->rombel->lembaga->singkatan ?? $j->rombel->lembaga->nama }}
                                    </span>
                                @endif
                            </div>

                            {{-- Nama Guru --}}
                            <div class="flex items-center gap-2 text-surface-700 font-bold">
                                <div class="w-7 h-7 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center shrink-0 border border-purple-100">
                                    <i data-lucide="user-check" class="w-3.5 h-3.5"></i>
                                </div>
                                <div>
                                    <p class="text-surface-900 font-extrabold leading-tight">
                                        {{ $j->guru->orang->nama_lengkap ?? 'Belum Ditentukan' }}
                                    </p>
                                    <p class="text-[0.65rem] text-surface-450 font-normal">Guru Pengajar</p>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Footer Action Bar --}}
                    <div class="mt-6 pt-4 border-t border-surface-100 flex items-center justify-end gap-2">
                        <form action="{{ route('admin.jadwal-pelajaran.destroy', $j->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal pelajaran ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 rounded-xl text-rose-600 hover:bg-rose-50 hover:text-rose-800 transition-colors border border-rose-100" title="Hapus Jadwal">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>

                </div>
            @endforeach
        </div>
    @else
        {{-- Empty State Card --}}
        <div class="bg-white rounded-3xl border-2 border-dashed border-surface-200 p-12 text-center shadow-xs">
            <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-4 border border-emerald-100">
                <i data-lucide="calendar-x" class="w-8 h-8"></i>
            </div>
            <h3 class="font-extrabold text-surface-900 text-base mb-1 font-heading">Tidak Ada Jadwal Pelajaran Ditemukan</h3>
            <p class="text-xs text-surface-500 max-w-md mx-auto leading-relaxed mb-6">
                Tidak ada jadwal pelajaran yang cocok dengan kriteria filterisasi (Hari, Kelas, atau Guru) yang dipilih.
            </p>
            <div class="flex items-center justify-center gap-3">
                <a href="{{ route('admin.jadwal-pelajaran.index', ['tahun_pelajaran_id' => $tahunId]) }}" class="px-4 py-2.5 rounded-2xl bg-surface-100 text-surface-800 font-bold text-xs hover:bg-surface-200 transition-colors border border-surface-200">
                    Reset Filter
                </a>
                <a href="{{ route('admin.jadwal-pelajaran.create', ['rombel_id' => $rombelId, 'tahun_pelajaran_id' => $tahunId]) }}" class="px-4 py-2.5 rounded-2xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs transition-colors shadow-md shadow-emerald-700/20">
                    + Tambah Jadwal Baru
                </a>
            </div>
        </div>
    @endif

</div>
@endsection
