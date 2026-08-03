@extends('layouts.app')

@section('title', 'Rekap & Cetak Presensi Santri — PP Nurul Furqon')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Rekap & Cetak Presensi Santri</h1>
        <p class="text-xs text-surface-500 mt-0.5">Filter data presensi berdasarkan Guru Pengampu, Kelas, Mata Pelajaran, dan Periode (Hari, Minggu, Bulan).</p>
    </div>
    <a href="{{ route('admin.presensi.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-surface-100 text-surface-700 font-bold text-xs rounded-xl hover:bg-surface-200 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Presensi Harian
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Card Form Filter Admin --}}
    <x-card class="p-6">
        <form method="GET" action="{{ route('admin.presensi.rekap') }}" id="form_filter_admin_presensi" class="space-y-4">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                {{-- Filter Guru Pengampu --}}
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1.5">Filter Guru Pengampu</label>
                    <select name="pegawai_id" onchange="this.form.submit()" class="w-full rounded-xl border border-surface-300 bg-white px-3 py-2 text-xs font-bold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                        <option value="">-- Semua Guru Pengampu --</option>
                        @foreach($gurus as $g)
                            <option value="{{ $g->id }}" {{ $selectedPegawaiId == $g->id ? 'selected' : '' }}>
                                {{ $g->orang->nama_lengkap ?? 'Guru ID #'.$g->id }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Kelas / Rombel --}}
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1.5">Filter Kelas / Rombel</label>
                    <select name="rombel_id" onchange="this.form.submit()" class="w-full rounded-xl border border-surface-300 bg-white px-3 py-2 text-xs font-bold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                        <option value="">-- Semua Kelas / Rombel --</option>
                        @foreach($rombels as $r)
                            <option value="{{ $r->id }}" {{ $selectedRombelId == $r->id ? 'selected' : '' }}>
                                {{ str_starts_with(strtolower($r->nama ?? ''), 'kelas') ? $r->nama : 'Kelas ' . ($r->nama ?? '-') }} @if($r->lembaga) ({{ $r->lembaga->singkatan }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Mata Pelajaran --}}
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1.5">Filter Mata Pelajaran</label>
                    <select name="mata_pelajaran_id" onchange="this.form.submit()" class="w-full rounded-xl border border-surface-300 bg-white px-3 py-2 text-xs font-bold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                        <option value="">-- Semua Mata Pelajaran --</option>
                        @foreach($mapels as $m)
                            <option value="{{ $m->id }}" {{ $selectedMapelId == $m->id ? 'selected' : '' }}>
                                {{ $m->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Periode Filter Mode --}}
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1.5">Periode Rekapitulasi</label>
                    <select name="filter_mode" id="filter_mode" onchange="this.form.submit()" class="w-full rounded-xl border border-surface-300 bg-white px-3 py-2 text-xs font-bold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                        <option value="bulan" {{ $filterMode === 'bulan' ? 'selected' : '' }}>🗓️ Bulanan (Per Bulan)</option>
                        <option value="minggu" {{ $filterMode === 'minggu' ? 'selected' : '' }}>📅 Mingguan (Rentang Tanggal)</option>
                        <option value="hari" {{ $filterMode === 'hari' ? 'selected' : '' }}>📆 Harian (Per Tanggal)</option>
                    </select>
                </div>

            </div>

            {{-- Date Inputs Row --}}
            <div class="flex flex-col sm:flex-row items-end justify-between gap-4 pt-2 border-t border-surface-100">
                <div class="w-full sm:w-auto min-w-[280px]">
                    @if($filterMode === 'hari')
                        <label class="block text-xs font-bold text-surface-700 mb-1">Tanggal Pertemuan</label>
                        <input type="date" name="tanggal_start" value="{{ $tanggalStart }}" onchange="this.form.submit()" class="w-full rounded-xl border border-surface-300 bg-white px-3 py-2 text-xs font-bold text-surface-900">
                    @elseif($filterMode === 'minggu')
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[0.68rem] font-bold text-surface-600 mb-1">Tanggal Mulai</label>
                                <input type="date" name="tanggal_start" value="{{ $tanggalStart }}" class="w-full rounded-xl border border-surface-300 bg-white px-2.5 py-1.5 text-xs font-bold text-surface-900">
                            </div>
                            <div>
                                <label class="block text-[0.68rem] font-bold text-surface-600 mb-1">Tanggal Selesai</label>
                                <input type="date" name="tanggal_end" value="{{ $tanggalEnd }}" class="w-full rounded-xl border border-surface-300 bg-white px-2.5 py-1.5 text-xs font-bold text-surface-900">
                            </div>
                        </div>
                    @else
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[0.68rem] font-bold text-surface-600 mb-1">Bulan</label>
                                <select name="bulan" onchange="this.form.submit()" class="w-full rounded-xl border border-surface-300 bg-white px-2.5 py-1.5 text-xs font-bold text-surface-900">
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::createFromDate(2026, $m, 1)->locale('id')->isoFormat('MMMM') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[0.68rem] font-bold text-surface-600 mb-1">Tahun</label>
                                <select name="tahun" onchange="this.form.submit()" class="w-full rounded-xl border border-surface-300 bg-white px-2.5 py-1.5 text-xs font-bold text-surface-900">
                                    @foreach([2025, 2026, 2027] as $y)
                                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2 bg-surface-100 hover:bg-surface-200 text-surface-800 font-bold text-xs rounded-xl transition-all flex items-center gap-1.5">
                        <i data-lucide="filter" class="w-4 h-4"></i>
                        <span>Terapkan Filter</span>
                    </button>

                    <a href="{{ route('admin.presensi.cetak', [
                        'pegawai_id' => $selectedPegawaiId,
                        'rombel_id' => $selectedRombelId,
                        'mata_pelajaran_id' => $selectedMapelId,
                        'filter_mode' => $filterMode,
                        'tanggal_start' => $tanggalStart,
                        'tanggal_end' => $tanggalEnd,
                        'bulan' => $bulan,
                        'tahun' => $tahun
                    ]) }}" target="_blank" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-xs rounded-xl transition-all shadow-md flex items-center gap-2 cursor-pointer" style="background-color: #047857 !important; color: #ffffff !important;">
                        <i data-lucide="printer" class="w-4 h-4 text-white" style="color: #ffffff !important;"></i>
                        <span>Cetak Laporan / PDF</span>
                    </a>
                </div>
            </div>

        </form>
    </x-card>

    {{-- Preview Results List --}}
    @forelse($jadwals as $jadwal)
        <x-card class="p-6">
            <div class="bg-emerald-900 text-white p-4 rounded-2xl mb-4 flex flex-col md:flex-row md:items-center justify-between gap-3" style="background-color: #064e3b !important; color: #ffffff !important;">
                <div>
                    <div class="text-[0.65rem] font-bold text-emerald-200 uppercase">
                        {{ str_starts_with(strtolower($jadwal->rombel->nama ?? ''), 'kelas') ? $jadwal->rombel->nama : 'Kelas ' . ($jadwal->rombel->nama ?? '-') }}
                        @if($jadwal->rombel?->lembaga) ({{ $jadwal->rombel->lembaga->nama }}) @endif
                    </div>
                    <h3 class="text-base font-extrabold text-white leading-tight">{{ $jadwal->mataPelajaran->nama }}</h3>
                    <div class="text-xs text-emerald-100 font-medium pt-0.5">
                        👨‍🏫 Guru: <strong>{{ $jadwal->guru->orang->nama_lengkap ?? '-' }}</strong> • ⏰ Pukul {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }} WITA (Hari {{ ucfirst(strtolower($jadwal->hari)) }})
                    </div>
                </div>
                <div class="px-3 py-1 rounded-lg bg-white/10 text-xs font-bold text-white border border-white/20 shrink-0">
                    {{ $periodeLabel }}
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-surface-200">
                <table class="w-full text-xs text-left">
                    <thead class="bg-surface-50 text-surface-700 uppercase font-black text-[11px] tracking-wider border-b border-surface-200">
                        <tr>
                            <th class="px-4 py-3 w-10 text-center">No</th>
                            <th class="px-4 py-3 min-w-[180px]">Nama Santri & NISN</th>
                            <th class="px-3 py-3 text-center w-14 bg-emerald-50 text-emerald-800">H</th>
                            <th class="px-3 py-3 text-center w-14 bg-sky-50 text-sky-800">S</th>
                            <th class="px-3 py-3 text-center w-14 bg-amber-50 text-amber-800">I</th>
                            <th class="px-3 py-3 text-center w-14 bg-rose-50 text-rose-800">A</th>
                            <th class="px-4 py-3 text-center w-24">% Hadir</th>
                            @if(count($dateList) > 0 && count($dateList) <= 31)
                                @foreach($dateList as $d)
                                    <th class="px-2 py-3 text-center font-mono min-w-[40px]" title="{{ \Carbon\Carbon::parse($d)->locale('id')->isoFormat('D MMM YYYY') }}">
                                        {{ \Carbon\Carbon::parse($d)->format('d/m') }}
                                    </th>
                                @endforeach
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200 bg-white">
                        @php
                            $pesertaList = $jadwal->rombel->riwayatPeserta->map->pesertaDidik;
                        @endphp
                        @forelse($pesertaList as $index => $peserta)
                            @php
                                $hCount = 0; $sCount = 0; $iCount = 0; $aCount = 0;
                                foreach ($dateList as $d) {
                                    $p = $presensiData[$peserta->id][$d][0] ?? null;
                                    if ($p) {
                                        $st = strtoupper($p->status);
                                        if ($st === 'HADIR') $hCount++;
                                        elseif ($st === 'SAKIT') $sCount++;
                                        elseif ($st === 'IZIN') $iCount++;
                                        elseif ($st === 'ALPA' || $st === 'ALPHA') $aCount++;
                                    }
                                }
                                $totalPertemuan = count($dateList);
                                $persen = $totalPertemuan > 0 ? round(($hCount / $totalPertemuan) * 100, 1) : 0;
                            @endphp
                            <tr class="hover:bg-surface-50/60 transition-colors">
                                <td class="px-4 py-3 text-center font-bold text-surface-500">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-extrabold text-surface-900">{{ $peserta->orang->nama ?? '-' }}</div>
                                    <div class="text-[0.68rem] text-surface-500">NISN: {{ $peserta->nisn ?? '-' }}</div>
                                </td>
                                <td class="px-3 py-3 text-center font-black text-emerald-700 bg-emerald-50/40">{{ $hCount }}</td>
                                <td class="px-3 py-3 text-center font-black text-sky-700 bg-sky-50/40">{{ $sCount }}</td>
                                <td class="px-3 py-3 text-center font-black text-amber-700 bg-amber-50/40">{{ $iCount }}</td>
                                <td class="px-3 py-3 text-center font-black text-rose-700 bg-rose-50/40">{{ $aCount }}</td>
                                <td class="px-4 py-3 text-center font-extrabold">
                                    <span class="px-2 py-0.5 rounded-md text-[0.68rem] {{ $persen >= 85 ? 'bg-emerald-100 text-emerald-800' : ($persen >= 70 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                        {{ $persen }}%
                                    </span>
                                </td>
                                @if(count($dateList) > 0 && count($dateList) <= 31)
                                    @foreach($dateList as $d)
                                        @php
                                            $p = $presensiData[$peserta->id][$d][0] ?? null;
                                            $st = $p ? strtoupper($p->status) : '-';
                                        @endphp
                                        <td class="px-2 py-3 text-center font-black text-[11px]">
                                            @if($st === 'HADIR')
                                                <span class="text-emerald-700">H</span>
                                            @elseif($st === 'SAKIT')
                                                <span class="text-sky-700">S</span>
                                            @elseif($st === 'IZIN')
                                                <span class="text-amber-700">I</span>
                                            @elseif($st === 'ALPA' || $st === 'ALPHA')
                                                <span class="text-rose-700">A</span>
                                            @else
                                                <span class="text-surface-300">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="py-8 text-center text-surface-500">Belum ada santri aktif di kelas ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    @empty
        <x-card class="p-8 text-center text-surface-500">
            <i data-lucide="info" class="w-8 h-8 mx-auto mb-2 text-surface-400"></i>
            Tidak ada data jadwal / presensi yang sesuai dengan filter yang dipilih.
        </x-card>
    @endforelse

</div>
@endsection
