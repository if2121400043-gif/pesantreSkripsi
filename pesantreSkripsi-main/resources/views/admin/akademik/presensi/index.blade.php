@extends('layouts.app')

@section('title', 'Presensi Kelas')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Presensi Kelas</h1>
        <p class="text-sm text-surface-500 mt-1">Kelola absensi harian santri berdasarkan rombongan belajar.</p>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    {{-- Left Sidebar: Filters --}}
    <div class="lg:col-span-1">
        <x-card title="Filter Presensi">
            <form action="{{ route('admin.presensi.index') }}" method="GET" class="space-y-4">
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-surface-700 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ $tanggal }}" class="form-input" required onchange="this.form.submit()">
                </div>
                
                <div>
                    <label for="rombel_id" class="block text-sm font-medium text-surface-700 mb-1">Pilih Kelas</label>
                    <select name="rombel_id" id="rombel_id" class="form-input" required onchange="this.form.submit()">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($rombels as $rombel)
                            <option value="{{ $rombel->id }}" {{ ($selectedRombel && $selectedRombel->id == $rombel->id) ? 'selected' : '' }}>
                                {{ $rombel->nama }} ({{ $rombel->lembaga?->nama ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                @if($tahunAktif)
                    <div class="mt-4 p-3 bg-primary-50 rounded-lg border border-primary-100">
                        <p class="text-xs text-primary-700 font-medium">Tahun Pelajaran Aktif:</p>
                        <p class="text-sm font-bold text-primary-900">{{ $tahunAktif->nama }}</p>
                    </div>
                @else
                    <div class="mt-4 p-3 bg-danger-50 rounded-lg border border-danger-100">
                        <p class="text-xs text-danger-700 font-medium">Peringatan:</p>
                        <p class="text-sm font-bold text-danger-900">Belum ada Tahun Pelajaran aktif!</p>
                    </div>
                @endif
            </form>
        </x-card>
    </div>

    {{-- Right Content: Students List --}}
    <div class="lg:col-span-3">
        @if($selectedRombel)
            <x-card title="Form Pengisian Presensi: {{ $selectedRombel->nama }}" :padding="false">
                <form action="{{ route('admin.presensi.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="rombel_id" value="{{ $selectedRombel->id }}">
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-surface-50 text-surface-600 border-b border-surface-100">
                                <tr>
                                    <th class="px-6 py-4 font-semibold w-12 text-center">No</th>
                                    <th class="px-6 py-4 font-semibold">Nama Santri</th>
                                    <th class="px-6 py-4 font-semibold text-center w-64">Status Kehadiran</th>
                                    <th class="px-6 py-4 font-semibold">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-100 text-surface-700">
                                @forelse($peserta as $index => $p)
                                    @php
                                        $status = isset($presensiHariIni[$p->id]) ? $presensiHariIni[$p->id]->status : 'HADIR';
                                        $keterangan = isset($presensiHariIni[$p->id]) ? $presensiHariIni[$p->id]->keterangan : '';
                                    @endphp
                                    <tr class="hover:bg-surface-50/50 transition-colors">
                                        <td class="px-6 py-4 text-center">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-surface-900">{{ $p->orang?->nama_lengkap ?? 'Tanpa Nama' }}</div>
                                            <div class="text-xs text-surface-500">NIUP: {{ $p->orang?->niup ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center gap-4">
                                                <label class="flex flex-col items-center cursor-pointer group">
                                                    <input type="radio" name="presensi[{{ $p->id }}][status]" value="HADIR" class="peer sr-only" {{ $status == 'HADIR' ? 'checked' : '' }}>
                                                    <div class="w-8 h-8 flex items-center justify-center rounded-full border-2 border-surface-200 peer-checked:border-success-500 peer-checked:bg-success-50 peer-checked:text-success-600 text-surface-400 group-hover:border-success-300 transition-all">
                                                        <span class="text-xs font-bold">H</span>
                                                    </div>
                                                </label>
                                                <label class="flex flex-col items-center cursor-pointer group">
                                                    <input type="radio" name="presensi[{{ $p->id }}][status]" value="SAKIT" class="peer sr-only" {{ $status == 'SAKIT' ? 'checked' : '' }}>
                                                    <div class="w-8 h-8 flex items-center justify-center rounded-full border-2 border-surface-200 peer-checked:border-warning-500 peer-checked:bg-warning-50 peer-checked:text-warning-600 text-surface-400 group-hover:border-warning-300 transition-all">
                                                        <span class="text-xs font-bold">S</span>
                                                    </div>
                                                </label>
                                                <label class="flex flex-col items-center cursor-pointer group">
                                                    <input type="radio" name="presensi[{{ $p->id }}][status]" value="IZIN" class="peer sr-only" {{ $status == 'IZIN' ? 'checked' : '' }}>
                                                    <div class="w-8 h-8 flex items-center justify-center rounded-full border-2 border-surface-200 peer-checked:border-info-500 peer-checked:bg-info-50 peer-checked:text-info-600 text-surface-400 group-hover:border-info-300 transition-all">
                                                        <span class="text-xs font-bold">I</span>
                                                    </div>
                                                </label>
                                                <label class="flex flex-col items-center cursor-pointer group">
                                                    <input type="radio" name="presensi[{{ $p->id }}][status]" value="ALPHA" class="peer sr-only" {{ $status == 'ALPHA' ? 'checked' : '' }}>
                                                    <div class="w-8 h-8 flex items-center justify-center rounded-full border-2 border-surface-200 peer-checked:border-danger-500 peer-checked:bg-danger-50 peer-checked:text-danger-600 text-surface-400 group-hover:border-danger-300 transition-all">
                                                        <span class="text-xs font-bold">A</span>
                                                    </div>
                                                </label>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <input type="text" name="presensi[{{ $p->id }}][keterangan]" value="{{ $keterangan }}" placeholder="Catatan..." class="form-input text-xs py-1">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-surface-500">
                                            <i data-lucide="users" class="w-10 h-10 text-surface-300 mx-auto mb-3"></i>
                                            <p>Belum ada santri di kelas ini.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if(count($peserta) > 0)
                        <div class="p-5 border-t border-surface-100 bg-surface-50 flex justify-end">
                            <button type="submit" class="btn-primary">
                                <i data-lucide="save" class="w-4 h-4"></i>
                                Simpan Data Presensi
                            </button>
                        </div>
                    @endif
                </form>
            </x-card>
            
            {{-- Quick legend --}}
            <div class="mt-4 flex gap-4 text-xs text-surface-500">
                <div class="flex items-center gap-1"><span class="w-4 h-4 rounded-full bg-success-100 text-success-600 font-bold flex items-center justify-center">H</span> Hadir</div>
                <div class="flex items-center gap-1"><span class="w-4 h-4 rounded-full bg-warning-100 text-warning-600 font-bold flex items-center justify-center">S</span> Sakit</div>
                <div class="flex items-center gap-1"><span class="w-4 h-4 rounded-full bg-info-100 text-info-600 font-bold flex items-center justify-center">I</span> Izin</div>
                <div class="flex items-center gap-1"><span class="w-4 h-4 rounded-full bg-danger-100 text-danger-600 font-bold flex items-center justify-center">A</span> Alpha</div>
            </div>
            
        @else
            <div class="flex items-center justify-center p-12 border-2 border-dashed border-surface-200 rounded-2xl h-full">
                <div class="text-center text-surface-500">
                    <i data-lucide="mouse-pointer-click" class="w-10 h-10 text-surface-300 mx-auto mb-3"></i>
                    <p class="font-medium text-surface-700">Pilih Kelas</p>
                    <p class="text-sm mt-1">Silakan pilih kelas pada panel di sebelah kiri untuk mulai mengisi absen.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
