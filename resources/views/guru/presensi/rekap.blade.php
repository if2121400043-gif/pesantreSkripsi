@extends('layouts.guru')

@section('title', 'Rekap & Cetak Laporan Presensi — PP Nurul Furqon')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-3xl border border-surface-200 shadow-sm">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold shrink-0">
                <i data-lucide="printer" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-surface-900 font-heading">Rekap & Cetak Laporan Presensi</h1>
                <p class="text-xs text-surface-500 mt-0.5">Filter dan cetak rekapitulasi kehadiran santri per Hari, Minggu, atau Bulan.</p>
            </div>
        </div>

        <a href="{{ route('guru.presensi.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-surface-100 text-surface-700 font-bold text-xs rounded-xl hover:bg-surface-200 transition-colors shrink-0">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Daftar Presensi
        </a>
    </div>

    {{-- Card Filter Rekap Presensi --}}
    <div class="bg-white p-6 rounded-3xl border border-surface-200 shadow-sm">
        <form method="GET" action="{{ route('guru.presensi.rekap') }}" id="form_filter_rekap" class="space-y-4">
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                
                {{-- Pilih Kelas & Mapel --}}
                <div class="md:col-span-5">
                    <label class="block text-xs font-bold text-surface-700 mb-1.5">Pilih Kelas & Mata Pelajaran <span class="text-rose-500">*</span></label>
                    <select name="jadwal_id" required class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-bold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                        @foreach($jadwals as $j)
                            <option value="{{ $j->id }}" {{ $selectedJadwalId == $j->id ? 'selected' : '' }}>
                                {{ str_starts_with(strtolower($j->rombel->nama ?? ''), 'kelas') ? $j->rombel->nama : 'Kelas ' . ($j->rombel->nama ?? '-') }} — {{ $j->mataPelajaran->nama }} (Hari {{ ucfirst(strtolower($j->hari)) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Mode Filter (Hari, Minggu, Bulan) --}}
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold text-surface-700 mb-1.5">Periode Laporan <span class="text-rose-500">*</span></label>
                    <select name="filter_mode" id="filter_mode" onchange="this.form.submit()" class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-bold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                        <option value="bulan" {{ $filterMode === 'bulan' ? 'selected' : '' }}>🗓️ Bulanan (Per Bulan)</option>
                        <option value="minggu" {{ $filterMode === 'minggu' ? 'selected' : '' }}>📅 Mingguan (Rentang Tanggal)</option>
                        <option value="hari" {{ $filterMode === 'hari' ? 'selected' : '' }}>📆 Harian (Per Tanggal)</option>
                    </select>
                </div>

                {{-- Dynamic Inputs based on filterMode --}}
                <div class="md:col-span-4">
                    @if($filterMode === 'hari')
                        <label class="block text-xs font-bold text-surface-700 mb-1.5">Tanggal Pertemuan</label>
                        <input type="date" name="tanggal_start" value="{{ $tanggalStart }}" onchange="this.form.submit()" class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-bold text-surface-900">
                    @elseif($filterMode === 'minggu')
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[0.68rem] font-bold text-surface-600 mb-1">Mulai Tanggal</label>
                                <input type="date" name="tanggal_start" value="{{ $tanggalStart }}" class="w-full rounded-xl border border-surface-300 bg-white px-2.5 py-2 text-xs font-bold text-surface-900">
                            </div>
                            <div>
                                <label class="block text-[0.68rem] font-bold text-surface-600 mb-1">Sampai Tanggal</label>
                                <input type="date" name="tanggal_end" value="{{ $tanggalEnd }}" class="w-full rounded-xl border border-surface-300 bg-white px-2.5 py-2 text-xs font-bold text-surface-900">
                            </div>
                        </div>
                    @else
                        {{-- bulan --}}
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[0.68rem] font-bold text-surface-600 mb-1">Bulan</label>
                                <select name="bulan" onchange="this.form.submit()" class="w-full rounded-xl border border-surface-300 bg-white px-2.5 py-2 text-xs font-bold text-surface-900">
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::createFromDate(2026, $m, 1)->locale('id')->isoFormat('MMMM') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[0.68rem] font-bold text-surface-600 mb-1">Tahun</label>
                                <select name="tahun" onchange="this.form.submit()" class="w-full rounded-xl border border-surface-300 bg-white px-2.5 py-2 text-xs font-bold text-surface-900">
                                    @foreach([2025, 2026, 2027] as $y)
                                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                </div>

            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-surface-100">
                <div class="text-xs text-surface-500 font-semibold">
                    *Tampilkan preview rekapitulasi di bawah sebelum melakukan pencetakan.
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2 bg-surface-100 hover:bg-surface-200 text-surface-800 font-bold text-xs rounded-xl transition-all flex items-center gap-1.5">
                        <i data-lucide="filter" class="w-4 h-4"></i>
                        <span>Terapkan Filter</span>
                    </button>
                    @if($selectedJadwal)
                        <a href="{{ route('guru.presensi.cetak', [
                            'jadwal_id' => $selectedJadwalId,
                            'filter_mode' => $filterMode,
                            'tanggal_start' => $tanggalStart,
                            'tanggal_end' => $tanggalEnd,
                            'bulan' => $bulan,
                            'tahun' => $tahun
                        ]) }}" target="_blank" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-xs rounded-xl transition-all shadow-md flex items-center gap-2 cursor-pointer" style="background-color: #047857 !important; color: #ffffff !important;">
                            <i data-lucide="printer" class="w-4 h-4 text-white" style="color: #ffffff !important;"></i>
                            <span>Cetak Laporan / PDF</span>
                        </a>
                    @endif
                </div>
            </div>

        </form>
    </div>

    {{-- Preview Laporan Presensi --}}
    @if($selectedJadwal)
        <div class="bg-white p-6 rounded-3xl border border-surface-200 shadow-sm space-y-6">
            
            {{-- Header Details Preview --}}
            <div class="bg-emerald-900 text-white p-5 rounded-2xl border border-emerald-800 flex flex-col md:flex-row md:items-center justify-between gap-4" style="background-color: #064e3b !important; color: #ffffff !important;">
                <div class="space-y-1">
                    <div class="text-[0.68rem] font-bold text-emerald-200 uppercase tracking-wider">Laporan Rekapitulasi Presensi Santri</div>
                    <h2 class="text-lg font-black text-white">{{ $selectedJadwal->mataPelajaran->nama }}</h2>
                    <div class="flex flex-wrap items-center gap-3 text-xs font-semibold text-emerald-100 pt-0.5">
                        <span>🏫 {{ str_starts_with(strtolower($selectedJadwal->rombel->nama ?? ''), 'kelas') ? $selectedJadwal->rombel->nama : 'Kelas ' . ($selectedJadwal->rombel->nama ?? '-') }}</span>
                        <span>•</span>
                        <span>👨‍🏫 {{ $selectedJadwal->guru->orang->nama_lengkap ?? $pegawai->orang->nama_lengkap ?? '-' }}</span>
                        <span>•</span>
                        <span>⏰ {{ substr($selectedJadwal->jam_mulai, 0, 5) }} - {{ substr($selectedJadwal->jam_selesai, 0, 5) }} WITA (Hari {{ ucfirst(strtolower($selectedJadwal->hari)) }})</span>
                    </div>
                </div>
                <div class="px-3.5 py-1.5 rounded-xl bg-white/10 text-white text-xs font-bold border border-white/20 shrink-0">
                    {{ $periodeLabel }}
                </div>
            </div>

            {{-- Tabel Preview --}}
            <div class="overflow-x-auto rounded-2xl border border-surface-200">
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
                                    <th class="px-2 py-3 text-center font-mono min-w-[40px]" title="{{ Carbon\Carbon::parse($d)->locale('id')->isoFormat('D MMM YYYY') }}">
                                        {{ Carbon\Carbon::parse($d)->format('d/m') }}
                                    </th>
                                @endforeach
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200 bg-white">
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
                                                <span class="text-emerald-700" title="Hadir">H</span>
                                            @elseif($st === 'SAKIT')
                                                <span class="text-sky-700" title="Sakit">S</span>
                                            @elseif($st === 'IZIN')
                                                <span class="text-amber-700" title="Izin">I</span>
                                            @elseif($st === 'ALPA' || $st === 'ALPHA')
                                                <span class="text-rose-700" title="Alpha">A</span>
                                            @else
                                                <span class="text-surface-300">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="py-10 text-center text-surface-500">
                                    Belum ada data santri aktif di kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    @endif

</div>
@endsection
