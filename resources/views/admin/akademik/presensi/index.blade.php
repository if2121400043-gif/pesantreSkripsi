@extends('layouts.app')

@section('title', 'Input Presensi')

@section('page_header')
    <div>
        <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Input Presensi</h1>
        <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">Catat kehadiran santri berdasarkan jenis presensi.</p>
    </div>
@endsection

@section('content')
    @if(session('success'))
        <div class="p-4 mb-4 rounded-lg bg-success-50 text-success-800 dark:bg-success-900/30 dark:text-success-400 border border-success-200 dark:border-success-800 flex items-start">
            <i data-lucide="check-circle" class="w-5 h-5 mr-3 shrink-0 mt-0.5"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Left Sidebar / Filters -->
        <div class="lg:col-span-1 space-y-6">
            <x-card>
                <div class="font-semibold text-surface-900 dark:text-white mb-4 flex items-center">
                    <i data-lucide="filter" class="w-4 h-4 mr-2 text-surface-500"></i>
                    Filter Presensi
                </div>

                <form action="{{ route('admin.presensi.index') }}" method="GET" class="space-y-4">
                    <!-- Jenis Presensi -->
                    <div class="space-y-1">
                        <label for="jenis_presensi_id" class="block text-sm font-medium text-surface-700 dark:text-surface-300">
                            Jenis Presensi
                        </label>
                        <select id="jenis_presensi_id" name="jenis_presensi_id" class="form-input w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-800 dark:text-white" onchange="this.form.submit()">
                            <option value="">-- Pilih Jenis --</option>
                            @foreach($jenisPresensiList as $jp)
                                <option value="{{ $jp->id }}" {{ ($selectedJenis?->id == $jp->id) ? 'selected' : '' }}>
                                    {{ $jp->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tanggal -->
                    <div class="space-y-1">
                        <label for="tanggal" class="block text-sm font-medium text-surface-700 dark:text-surface-300">
                            Tanggal
                        </label>
                        <input type="date" id="tanggal" name="tanggal" class="form-input w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-800 dark:text-white" value="{{ $tanggal }}" onchange="this.form.submit()">
                    </div>

                    <!-- Dynamic Filter based on Tipe Target -->
                    @if($selectedJenis)
                        @if($selectedJenis->tipe_target === 'PER_ROMBEL')
                            <div class="space-y-1">
                                <label for="rombel_id" class="block text-sm font-medium text-surface-700 dark:text-surface-300">
                                    Rombel / Kelas
                                </label>
                                <select id="rombel_id" name="rombel_id" class="form-input w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-800 dark:text-white" onchange="this.form.submit()">
                                    <option value="">-- Pilih Rombel --</option>
                                    @foreach($rombels as $rombel)
                                        <option value="{{ $rombel->id }}" {{ ($selectedRombel?->id == $rombel->id) ? 'selected' : '' }}>
                                            {{ $rombel->nama_rombel }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @elseif($selectedJenis->tipe_target === 'PER_ASRAMA')
                            <div class="space-y-1">
                                <label for="asrama_id" class="block text-sm font-medium text-surface-700 dark:text-surface-300">
                                    Asrama
                                </label>
                                <select id="asrama_id" name="asrama_id" class="form-input w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-800 dark:text-white" onchange="this.form.submit()">
                                    <option value="">-- Pilih Asrama --</option>
                                    @foreach($asramas as $asrama)
                                        <option value="{{ $asrama->id }}" {{ ($selectedAsrama?->id == $asrama->id) ? 'selected' : '' }}>
                                            {{ $asrama->nama_asrama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    @endif
                </form>
            </x-card>

            @if(isset($tahunAktif) && $tahunAktif)
                <div class="bg-info-50 dark:bg-info-900/30 border border-info-200 dark:border-info-800 rounded-lg p-4 flex items-start">
                    <i data-lucide="info" class="w-5 h-5 text-info-500 mr-3 shrink-0 mt-0.5"></i>
                    <div>
                        <h4 class="text-sm font-medium text-info-800 dark:text-info-400">Tahun Pelajaran Aktif</h4>
                        <p class="text-xs text-info-600 dark:text-info-500 mt-1">
                            {{ $tahunAktif->nama_tahun }} - {{ $tahunAktif->semester === 'GANJIL' ? 'Ganjil' : 'Genap' }}
                        </p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Content / Input Table -->
        <div class="lg:col-span-3">
            <x-card>
                @if(!$selectedJenis)
                    <div class="p-12 text-center text-surface-500 dark:text-surface-400 flex flex-col items-center justify-center">
                        <div class="bg-surface-100 dark:bg-surface-800 p-4 rounded-full mb-4">
                            <i data-lucide="clipboard-list" class="w-10 h-10 text-surface-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-2">Pilih Jenis Presensi</h3>
                        <p>Silakan pilih jenis presensi pada panel filter di sebelah kiri untuk mulai mencatat kehadiran.</p>
                    </div>
                @elseif(($selectedJenis->tipe_target === 'PER_ROMBEL' && !$selectedRombel) || ($selectedJenis->tipe_target === 'PER_ASRAMA' && !$selectedAsrama))
                    <div class="p-12 text-center text-surface-500 dark:text-surface-400 flex flex-col items-center justify-center">
                        <div class="bg-surface-100 dark:bg-surface-800 p-4 rounded-full mb-4">
                            <i data-lucide="users" class="w-10 h-10 text-surface-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-2">
                            Pilih {{ $selectedJenis->tipe_target === 'PER_ROMBEL' ? 'Kelas/Rombel' : 'Asrama' }}
                        </h3>
                        <p>Silakan pilih {{ $selectedJenis->tipe_target === 'PER_ROMBEL' ? 'kelas' : 'asrama' }} untuk menampilkan daftar santri.</p>
                    </div>
                @elseif($peserta->isEmpty())
                    <div class="p-12 text-center text-surface-500 dark:text-surface-400 flex flex-col items-center justify-center">
                        <div class="bg-surface-100 dark:bg-surface-800 p-4 rounded-full mb-4">
                            <i data-lucide="user-x" class="w-10 h-10 text-surface-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-2">Belum Ada Santri</h3>
                        <p>Tidak ada santri yang ditemukan untuk kriteria yang dipilih.</p>
                    </div>
                @else
                    <div class="mb-4 pb-4 border-b border-surface-200 dark:border-surface-700 flex justify-between items-center">
                        <div>
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-white">
                                Input Presensi: {{ $selectedJenis->nama }}
                            </h2>
                            <p class="text-sm text-surface-500 dark:text-surface-400">
                                @if($selectedJenis->tipe_target === 'PER_ROMBEL' && $selectedRombel)
                                    Kelas: {{ $selectedRombel->nama_rombel }}
                                @elseif($selectedJenis->tipe_target === 'PER_ASRAMA' && $selectedAsrama)
                                    Asrama: {{ $selectedAsrama->nama_asrama }}
                                @elseif($selectedJenis->tipe_target === 'SEMUA_SANTRI')
                                    Semua Santri
                                @endif
                                | {{ \Carbon\Carbon::parse($tanggal)->isoFormat('D MMMM Y') }}
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('admin.presensi.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="jenis_presensi_id" value="{{ $selectedJenis->id }}">
                        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                        @if($selectedJenis->tipe_target === 'PER_ROMBEL' && $selectedRombel)
                            <input type="hidden" name="rombel_id" value="{{ $selectedRombel->id }}">
                        @endif
                        @if($selectedJenis->tipe_target === 'PER_ASRAMA' && $selectedAsrama)
                            <input type="hidden" name="asrama_id" value="{{ $selectedAsrama->id }}">
                        @endif

                        <div class="overflow-x-auto mb-6">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800/50">
                                        <th class="p-3 text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider w-12 text-center">No</th>
                                        <th class="p-3 text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider">Nama Santri</th>
                                        <th class="p-3 text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider text-center w-64">Status Kehadiran</th>
                                        <th class="p-3 text-xs font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-surface-200 dark:divide-surface-700">
                                    @foreach($peserta as $p)
                                        @php
                                            $currentStatus = old('presensi.'.$p->id.'.status', $presensiHariIni[$p->id]->status ?? 'HADIR');
                                            $currentKeterangan = old('presensi.'.$p->id.'.keterangan', $presensiHariIni[$p->id]->keterangan ?? '');
                                        @endphp
                                        <tr class="hover:bg-surface-50 dark:hover:bg-surface-800/50 transition-colors">
                                            <td class="p-3 text-sm text-surface-900 dark:text-surface-100 text-center">{{ $loop->iteration }}</td>
                                            <td class="p-3">
                                                <div class="text-sm font-medium text-surface-900 dark:text-surface-100">{{ $p->orang?->nama_lengkap ?? '-' }}</div>
                                                <div class="text-xs text-surface-500 dark:text-surface-400">{{ $p->orang?->niup ?? '-' }}</div>
                                            </td>
                                            <td class="p-3">
                                                <div class="flex items-center justify-center gap-3">
                                                    <!-- HADIR -->
                                                    <label class="cursor-pointer group flex flex-col items-center">
                                                        <input type="radio" name="presensi[{{ $p->id }}][status]" value="HADIR" class="peer sr-only" {{ $currentStatus === 'HADIR' ? 'checked' : '' }}>
                                                        <div class="w-8 h-8 rounded-full border-2 border-surface-300 dark:border-surface-600 flex items-center justify-center text-surface-500 dark:text-surface-400 peer-checked:border-success-500 peer-checked:bg-success-500 peer-checked:text-white transition-all group-hover:border-success-400">
                                                            <span class="text-xs font-bold">H</span>
                                                        </div>
                                                    </label>
                                                    
                                                    <!-- SAKIT -->
                                                    <label class="cursor-pointer group flex flex-col items-center">
                                                        <input type="radio" name="presensi[{{ $p->id }}][status]" value="SAKIT" class="peer sr-only" {{ $currentStatus === 'SAKIT' ? 'checked' : '' }}>
                                                        <div class="w-8 h-8 rounded-full border-2 border-surface-300 dark:border-surface-600 flex items-center justify-center text-surface-500 dark:text-surface-400 peer-checked:border-warning-500 peer-checked:bg-warning-500 peer-checked:text-white transition-all group-hover:border-warning-400">
                                                            <span class="text-xs font-bold">S</span>
                                                        </div>
                                                    </label>

                                                    <!-- IZIN -->
                                                    <label class="cursor-pointer group flex flex-col items-center">
                                                        <input type="radio" name="presensi[{{ $p->id }}][status]" value="IZIN" class="peer sr-only" {{ $currentStatus === 'IZIN' ? 'checked' : '' }}>
                                                        <div class="w-8 h-8 rounded-full border-2 border-surface-300 dark:border-surface-600 flex items-center justify-center text-surface-500 dark:text-surface-400 peer-checked:border-info-500 peer-checked:bg-info-500 peer-checked:text-white transition-all group-hover:border-info-400">
                                                            <span class="text-xs font-bold">I</span>
                                                        </div>
                                                    </label>

                                                    <!-- ALPHA -->
                                                    <label class="cursor-pointer group flex flex-col items-center">
                                                        <input type="radio" name="presensi[{{ $p->id }}][status]" value="ALPHA" class="peer sr-only" {{ $currentStatus === 'ALPHA' ? 'checked' : '' }}>
                                                        <div class="w-8 h-8 rounded-full border-2 border-surface-300 dark:border-surface-600 flex items-center justify-center text-surface-500 dark:text-surface-400 peer-checked:border-danger-500 peer-checked:bg-danger-500 peer-checked:text-white transition-all group-hover:border-danger-400">
                                                            <span class="text-xs font-bold">A</span>
                                                        </div>
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="p-3">
                                                <input type="text" name="presensi[{{ $p->id }}][keterangan]" class="form-input w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-800 dark:text-white" placeholder="Keterangan..." value="{{ $currentKeterangan }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-surface-50 dark:bg-surface-800/50 p-4 rounded-lg border border-surface-200 dark:border-surface-700">
                            <div class="flex gap-4 text-xs text-surface-600 dark:text-surface-400">
                                <div class="flex items-center"><div class="w-3 h-3 rounded-full bg-success-500 mr-1.5"></div> Hadir</div>
                                <div class="flex items-center"><div class="w-3 h-3 rounded-full bg-warning-500 mr-1.5"></div> Sakit</div>
                                <div class="flex items-center"><div class="w-3 h-3 rounded-full bg-info-500 mr-1.5"></div> Izin</div>
                                <div class="flex items-center"><div class="w-3 h-3 rounded-full bg-danger-500 mr-1.5"></div> Alpha</div>
                            </div>
                            <button type="submit" class="btn-primary inline-flex items-center px-6 py-2 rounded-lg w-full sm:w-auto justify-center">
                                <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                                Simpan Presensi
                            </button>
                        </div>
                    </form>
                @endif
            </x-card>
        </div>
    </div>
@endsection
