@extends('layouts.app')

@section('title', 'Input Nilai Akademik')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Form Penilaian Rapor</h1>
        <p class="text-sm text-surface-500 mt-1">Kelas: <strong>{{ $jadwal->rombel->nama }}</strong> | Mapel: <strong>{{ $jadwal->mataPelajaran->nama }}</strong></p>
    </div>
    <a href="{{ route('guru.penilaian.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-surface-200 text-surface-700 font-medium rounded-xl hover:bg-surface-50 hover:text-surface-900 transition-colors shadow-sm">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
    </a>
</div>
@endsection

@section('content')
<x-card>
    <form method="GET" action="{{ route('guru.penilaian.create', $jadwal->id) }}" class="mb-6 bg-surface-50 p-4 rounded-xl border border-surface-200">
        <div class="flex flex-col sm:flex-row gap-4 items-end">
            <div>
                <label class="block text-sm font-bold text-surface-700 mb-1">Pilih Semester</label>
                <select name="semester" class="rounded-lg border-surface-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500/20 font-medium text-surface-900 w-full sm:w-48" onchange="this.form.submit()">
                    <option value="Ganjil" {{ strcasecmp($semester, 'Ganjil') == 0 ? 'selected' : '' }}>Semester Ganjil</option>
                    <option value="Genap" {{ strcasecmp($semester, 'Genap') == 0 ? 'selected' : '' }}>Semester Genap</option>
                </select>
            </div>
            <div class="flex-1 text-right text-sm text-surface-500 pb-1">
                Tahun Pelajaran: <strong>{{ $jadwal->tahunPelajaran->nama }}</strong>
            </div>
        </div>
    </form>

    <form method="POST" action="{{ route('guru.penilaian.store', $jadwal->id) }}">
        @csrf
        <input type="hidden" name="semester" value="{{ strtoupper($semester) }}">

        <div class="overflow-x-auto rounded-xl border border-surface-200 mb-6">
            <table class="w-full text-sm text-left">
                <thead class="bg-surface-50 text-surface-700 uppercase font-bold text-[11px] tracking-wider border-b border-surface-200">
                    <tr>
                        <th class="px-4 py-3 w-12 text-center" rowspan="2">No</th>
                        <th class="px-4 py-3 min-w-[200px]" rowspan="2">Nama Santri</th>
                        <th class="px-4 py-2 text-center border-b border-surface-200" colspan="3">Komponen Nilai (0-100)</th>
                        <th class="px-4 py-3 min-w-[250px]" rowspan="2">Catatan Guru (Opsional)</th>
                    </tr>
                    <tr>
                        <th class="px-2 py-2 text-center border-t border-surface-200 w-24">Tugas/UH (30%)</th>
                        <th class="px-2 py-2 text-center border-t border-surface-200 w-24">UTS (30%)</th>
                        <th class="px-2 py-2 text-center border-t border-surface-200 w-24">UAS (40%)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-200 bg-white">
                    @forelse($jadwal->rombel->riwayatPeserta as $index => $riwayat)
                        @php
                            $peserta = $riwayat->pesertaDidik;
                            $oldTugas = old("nilai.{$peserta->id}.tugas", $existingNilai[$peserta->id]->nilai_tugas ?? '');
                            $oldUts = old("nilai.{$peserta->id}.uts", $existingNilai[$peserta->id]->nilai_uts ?? '');
                            $oldUas = old("nilai.{$peserta->id}.uas", $existingNilai[$peserta->id]->nilai_uas ?? '');
                            $oldCatatan = old("nilai.{$peserta->id}.catatan", $existingNilai[$peserta->id]->catatan_guru ?? '');
                        @endphp
                        <tr class="hover:bg-surface-50/50 transition-colors">
                            <td class="px-4 py-3 text-center font-medium text-surface-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-bold text-surface-900">
                                {{ $peserta->orang->nama ?? '-' }}
                                <div class="text-xs font-normal text-surface-500">NISN: {{ $peserta->nisn ?? '-' }}</div>
                            </td>
                            <td class="px-2 py-3 text-center">
                                <input type="number" step="0.01" min="0" max="100" name="nilai[{{ $peserta->id }}][tugas]" value="{{ $oldTugas }}" class="w-full text-center rounded-lg border-surface-300 text-sm shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500/20" placeholder="0">
                            </td>
                            <td class="px-2 py-3 text-center">
                                <input type="number" step="0.01" min="0" max="100" name="nilai[{{ $peserta->id }}][uts]" value="{{ $oldUts }}" class="w-full text-center rounded-lg border-surface-300 text-sm shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500/20" placeholder="0">
                            </td>
                            <td class="px-2 py-3 text-center">
                                <input type="number" step="0.01" min="0" max="100" name="nilai[{{ $peserta->id }}][uas]" value="{{ $oldUas }}" class="w-full text-center rounded-lg border-surface-300 text-sm shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500/20" placeholder="0">
                            </td>
                            <td class="px-4 py-3">
                                <input type="text" name="nilai[{{ $peserta->id }}][catatan]" value="{{ $oldCatatan }}" class="w-full rounded-lg border-surface-300 text-sm shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500/20" placeholder="Tingkatkan belajarnya...">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-surface-500">
                                <i data-lucide="users" class="w-8 h-8 mx-auto mb-3 text-surface-300"></i>
                                Belum ada santri yang terdaftar aktif di kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jadwal->rombel->riwayatPeserta->count() > 0)
            <div class="flex justify-between items-center bg-surface-50 p-4 rounded-xl border border-surface-200">
                <p class="text-sm text-surface-600">
                    <i data-lucide="info" class="w-4 h-4 inline-block text-info-500 mr-1"></i>
                    Nilai akhir otomatis dihitung oleh sistem.
                </p>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 focus:ring-4 focus:ring-primary-500/30 transition-all shadow-md shadow-primary-500/20">
                    <i data-lucide="save" class="w-5 h-5"></i> Simpan Nilai
                </button>
            </div>
        @endif
    </form>
</x-card>
@endsection
