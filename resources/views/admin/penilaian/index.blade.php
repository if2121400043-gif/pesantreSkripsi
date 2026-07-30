@extends('layouts.app')

@section('title', 'Penilaian & Rapor Santri')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Input Nilai & Rapor</h1>
        <p class="text-sm text-surface-500 mt-1">Sistem input nilai akademik siswa per kelas dan mata pelajaran.</p>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Filter Parameter --}}
    <x-card title="Pilih Parameter Kelas & Mata Pelajaran" class="border-t-4 border-t-primary-500">
        <form action="{{ route('admin.penilaian.index') }}" method="GET" id="filter-form">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Tahun Ajaran <span class="text-danger-500">*</span></label>
                    <select name="tahun_pelajaran_id" required class="w-full px-3 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" onchange="document.getElementById('filter-form').submit()">
                        <option value="">Pilih Tahun...</option>
                        @foreach($tahuns as $tahun)
                            <option value="{{ $tahun->id }}" {{ $tahunId == $tahun->id ? 'selected' : ($loop->first && !$tahunId ? 'selected' : '') }}>
                                {{ $tahun->nama }} {{ $tahun->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Semester <span class="text-danger-500">*</span></label>
                    <select name="semester" required class="w-full px-3 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" onchange="document.getElementById('filter-form').submit()">
                        <option value="GANJIL" {{ $semester === 'GANJIL' ? 'selected' : '' }}>Semester Ganjil</option>
                        <option value="GENAP" {{ $semester === 'GENAP' ? 'selected' : '' }}>Semester Genap</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Pilih Kelas <span class="text-danger-500">*</span></label>
                    <select name="rombel_id" required class="w-full px-3 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" onchange="document.getElementById('filter-form').submit()" {{ empty($rombels) ? 'disabled' : '' }}>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($rombels as $r)
                            <option value="{{ $r->id }}" {{ $rombelId == $r->id ? 'selected' : '' }}>Kelas {{ $r->tingkat ? $r->tingkat . '-' : '' }}{{ $r->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Mata Pelajaran <span class="text-danger-500">*</span></label>
                    <select name="mata_pelajaran_id" required class="w-full px-3 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" onchange="document.getElementById('filter-form').submit()" {{ empty($mapels) ? 'disabled' : '' }}>
                        <option value="">-- Pilih Mapel --</option>
                        @foreach($mapels as $m)
                            <option value="{{ $m->id }}" {{ $mapelId == $m->id ? 'selected' : '' }}>{{ $m->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </x-card>

    @if($rombelId && $mapelId)
        <x-card :padding="false">
            <form action="{{ route('admin.penilaian.store') }}" method="POST">
                @csrf
                <input type="hidden" name="tahun_pelajaran_id" value="{{ $tahunId }}">
                <input type="hidden" name="rombel_id" value="{{ $rombelId }}">
                <input type="hidden" name="mata_pelajaran_id" value="{{ $mapelId }}">
                <input type="hidden" name="semester" value="{{ $semester }}">
                
                <div class="p-4 border-b border-surface-100 flex flex-col sm:flex-row justify-between items-center gap-4 bg-surface-50 rounded-t-xl">
                    <h3 class="font-bold text-surface-900 flex items-center gap-2">
                        <i data-lucide="edit-3" class="w-5 h-5 text-primary-500"></i>
                        Form Input Nilai: {{ collect($mapels)->firstWhere('id', $mapelId)->nama_mapel ?? '' }}
                    </h3>
                    <button type="submit" class="btn-primary py-2 px-6 shrink-0 w-full sm:w-auto">
                        Simpan Semua Nilai
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-surface-50/50 text-surface-600 sticky top-0 border-b border-surface-100">
                            <tr>
                                <th class="px-4 py-3 font-semibold w-10 text-center">No</th>
                                <th class="px-4 py-3 font-semibold">Nama Siswa / NIS</th>
                                <th class="px-4 py-3 font-semibold text-center w-24">Tugas</th>
                                <th class="px-4 py-3 font-semibold text-center w-24">UTS</th>
                                <th class="px-4 py-3 font-semibold text-center w-24">UAS</th>
                                <th class="px-4 py-3 font-semibold text-center w-32 bg-primary-50/50">NILAI AKHIR</th>
                                <th class="px-4 py-3 font-semibold text-center w-20">Grade</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-100 text-surface-700">
                            @forelse($pesertaList as $index => $riwayat)
                                @php
                                    $pd_id = $riwayat->peserta_didik_id;
                                    $nilai = $nilaiMap[$pd_id] ?? null;
                                @endphp
                                <tr class="hover:bg-surface-50 transition-colors">
                                    <td class="px-4 py-3 text-center text-surface-500">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-surface-900">{{ $riwayat->pesertaDidik->orang->nama_lengkap }}</div>
                                        <div class="text-xs text-surface-500">{{ $riwayat->pesertaDidik->nis ?? $riwayat->pesertaDidik->orang->niup }}</div>
                                        <input type="hidden" name="nilai[{{ $index }}][peserta_didik_id]" value="{{ $pd_id }}">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" name="nilai[{{ $index }}][nilai_tugas]" value="{{ $nilai->nilai_tugas ?? '' }}" min="0" max="100" class="w-full text-center rounded border-surface-300 py-1 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="-">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" name="nilai[{{ $index }}][nilai_uts]" value="{{ $nilai->nilai_uts ?? '' }}" min="0" max="100" class="w-full text-center rounded border-surface-300 py-1 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="-">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" name="nilai[{{ $index }}][nilai_uas]" value="{{ $nilai->nilai_uas ?? '' }}" min="0" max="100" class="w-full text-center rounded border-surface-300 py-1 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="-">
                                    </td>
                                    <td class="px-4 py-3 bg-primary-50/20">
                                        <input type="number" name="nilai[{{ $index }}][nilai_akhir]" value="{{ $nilai->nilai_akhir ?? '' }}" min="0" max="100" class="w-full text-center font-bold rounded border-primary-300 bg-white py-1 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="-">
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($nilai && $nilai->predikat)
                                            <span class="inline-flex w-8 h-8 items-center justify-center rounded-full font-bold text-sm
                                                {{ $nilai->predikat == 'A' ? 'bg-success-100 text-success-700' : 
                                                  ($nilai->predikat == 'B' ? 'bg-info-100 text-info-700' : 
                                                  ($nilai->predikat == 'C' ? 'bg-warning-100 text-warning-700' : 'bg-danger-100 text-danger-700')) }}">
                                                {{ $nilai->predikat }}
                                            </span>
                                        @else
                                            <span class="text-surface-300">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-surface-500">
                                        Belum ada siswa di kelas ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
        </x-card>
    @else
        @if(request()->anyFilled(['tahun_pelajaran_id']))
            <div class="bg-surface-50 rounded-xl p-8 text-center border border-surface-200 border-dashed">
                <p class="text-surface-500">Pilih Kelas dan Mata Pelajaran untuk mulai menginput nilai.</p>
            </div>
        @endif
    @endif

</div>
@endsection
