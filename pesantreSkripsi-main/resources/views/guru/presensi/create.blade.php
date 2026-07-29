@extends('layouts.app')

@section('title', 'Isi Presensi Kelas')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Daftar Hadir Santri</h1>
        <p class="text-sm text-surface-500 mt-1">Kelas: <strong>{{ $jadwal->rombel->nama }}</strong> | Mapel: <strong>{{ $jadwal->mataPelajaran->nama }}</strong></p>
    </div>
    <a href="{{ route('guru.presensi.index', ['hari' => $jadwal->hari]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-surface-200 text-surface-700 font-medium rounded-xl hover:bg-surface-50 hover:text-surface-900 transition-colors shadow-sm">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
    </a>
</div>
@endsection

@section('content')
<x-card>
    <form method="GET" action="{{ route('guru.presensi.create', $jadwal->id) }}" class="mb-6 flex flex-wrap items-end gap-4 bg-surface-50 p-4 rounded-xl border border-surface-200">
        <div>
            <label class="block text-sm font-bold text-surface-700 mb-1">Tanggal Pertemuan</label>
            <input type="date" name="tanggal" value="{{ $tanggal }}" class="rounded-lg border-surface-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500/20" onchange="this.form.submit()">
        </div>
        <div class="flex-1 text-right">
            <p class="text-sm text-surface-500">Pilih tanggal untuk melihat atau mengubah data absensi pada hari tersebut.</p>
        </div>
    </form>

    <form method="POST" action="{{ route('guru.presensi.store', $jadwal->id) }}">
        @csrf
        <input type="hidden" name="tanggal" value="{{ $tanggal }}">

        <div class="overflow-x-auto rounded-xl border border-surface-200 mb-6">
            <table class="w-full text-sm text-left">
                <thead class="bg-surface-50 text-surface-700 uppercase font-bold text-xs">
                    <tr>
                        <th class="px-4 py-3 w-12 text-center">No</th>
                        <th class="px-4 py-3">Nama Santri</th>
                        <th class="px-4 py-3 text-center w-64">Status Kehadiran</th>
                        <th class="px-4 py-3">Keterangan (Opsional)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-200 bg-white">
                    @forelse($jadwal->rombel->riwayatPeserta as $index => $riwayat)
                        @php
                            $peserta = $riwayat->pesertaDidik;
                            $oldStatus = old("presensi.{$peserta->id}.status", $existingPresensi[$peserta->id]->status ?? 'HADIR');
                            $oldKeterangan = old("presensi.{$peserta->id}.keterangan", $existingPresensi[$peserta->id]->keterangan ?? '');
                        @endphp
                        <tr class="hover:bg-surface-50/50 transition-colors">
                            <td class="px-4 py-3 text-center font-medium text-surface-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-bold text-surface-900">
                                {{ $peserta->orang->nama ?? '-' }}
                                <div class="text-xs font-normal text-surface-500">NISN: {{ $peserta->nisn ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap items-center justify-center gap-1.5 bg-surface-100 p-1.5 rounded-lg mx-auto w-full sm:w-auto">
                                    <label class="flex-1 sm:flex-none cursor-pointer">
                                        <input type="radio" name="presensi[{{ $peserta->id }}][status]" value="HADIR" class="peer sr-only" {{ $oldStatus === 'HADIR' ? 'checked' : '' }}>
                                        <div class="min-w-[2.2rem] px-2 py-1.5 rounded-md text-xs font-bold text-center transition-all peer-checked:bg-success-500 peer-checked:text-white peer-checked:shadow text-surface-600 hover:bg-surface-200">H</div>
                                    </label>
                                    <label class="flex-1 sm:flex-none cursor-pointer">
                                        <input type="radio" name="presensi[{{ $peserta->id }}][status]" value="SAKIT" class="peer sr-only" {{ $oldStatus === 'SAKIT' ? 'checked' : '' }}>
                                        <div class="min-w-[2.2rem] px-2 py-1.5 rounded-md text-xs font-bold text-center transition-all peer-checked:bg-info-500 peer-checked:text-white peer-checked:shadow text-surface-600 hover:bg-surface-200">S</div>
                                    </label>
                                    <label class="flex-1 sm:flex-none cursor-pointer">
                                        <input type="radio" name="presensi[{{ $peserta->id }}][status]" value="IZIN" class="peer sr-only" {{ $oldStatus === 'IZIN' ? 'checked' : '' }}>
                                        <div class="min-w-[2.2rem] px-2 py-1.5 rounded-md text-xs font-bold text-center transition-all peer-checked:bg-warning-500 peer-checked:text-white peer-checked:shadow text-surface-600 hover:bg-surface-200">I</div>
                                    </label>
                                    <label class="flex-1 sm:flex-none cursor-pointer">
                                        <input type="radio" name="presensi[{{ $peserta->id }}][status]" value="ALPHA" class="peer sr-only" {{ $oldStatus === 'ALPA' || $oldStatus === 'ALPHA' ? 'checked' : '' }}>
                                        <div class="min-w-[2.2rem] px-2 py-1.5 rounded-md text-xs font-bold text-center transition-all peer-checked:bg-danger-500 peer-checked:text-white peer-checked:shadow text-surface-600 hover:bg-surface-200">A</div>
                                    </label>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <input type="text" name="presensi[{{ $peserta->id }}][keterangan]" value="{{ $oldKeterangan }}" class="w-full rounded-lg border-surface-300 text-sm shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500/20" placeholder="Alasan izin/sakit...">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-surface-500">
                                <i data-lucide="users" class="w-8 h-8 mx-auto mb-3 text-surface-300"></i>
                                Belum ada santri yang terdaftar aktif di kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jadwal->rombel->riwayatPeserta->count() > 0)
            <div class="flex justify-end gap-3">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 focus:ring-4 focus:ring-primary-500/30 transition-all shadow-md shadow-primary-500/20">
                    <i data-lucide="save" class="w-5 h-5"></i> Simpan Presensi
                </button>
            </div>
        @endif
    </form>
</x-card>
@endsection
