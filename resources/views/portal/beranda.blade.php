@extends('layouts.portal')

@section('title', 'Portal Wali Santri — PP Nurul Furqon')

@section('content')
<div class="space-y-6">

    {{-- Merged Single Unified Hero Banner for Wali Santri (100% Mobile Responsive) --}}
    <div class="rounded-3xl p-5 sm:p-7 md:p-8 shadow-lg relative overflow-hidden text-white" style="background: linear-gradient(135deg, #065f46, #022c22) !important; color: #ffffff !important;">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 space-y-4">
            
            {{-- Top Row: Greeting, Badge & Child Selector --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-white/15 pb-4">
                <div>
                    <div class="flex items-center gap-2 flex-wrap mb-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-white/15 text-[0.7rem] font-bold backdrop-blur-sm border border-white/20" style="color: #ffffff !important;">
                            <i data-lucide="sparkles" class="w-3.5 h-3.5 text-warning-400"></i>
                            Portal Wali Santri
                        </span>
                        <span class="px-2.5 py-0.5 bg-white/20 text-white text-[0.68rem] font-extrabold rounded-full border border-white/20 shrink-0">
                            Santri Aktif
                        </span>
                    </div>

                    <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold font-heading leading-tight" style="color: #ffffff !important;">
                        Assalamu'alaikum, {{ auth()->user()->orang->nama_lengkap ?? auth()->user()->username }}
                    </h1>
                </div>

                {{-- Switch Child Selector --}}
                @if($anakList->count() > 1)
                    <div class="w-full sm:w-auto flex items-center gap-2 p-2.5 rounded-2xl border border-white/20 shrink-0" style="background-color: rgba(255, 255, 255, 0.15) !important;">
                        <label for="anak_id" class="text-xs font-bold whitespace-nowrap" style="color: #d1fae5 !important;">Pantau Ananda:</label>
                        <select name="anak_id" id="anak_id" onchange="window.location.href='{{ route('portal.beranda') }}?anak_id=' + this.value" class="w-full sm:w-auto bg-white/20 text-white text-xs font-bold rounded-xl border border-white/20 focus:ring-0 p-1.5 cursor-pointer" style="color: #ffffff !important; background-color: rgba(255, 255, 255, 0.2) !important;">
                            @foreach($anakList as $anak)
                                <option value="{{ $anak->id }}" class="text-surface-900 bg-white" {{ $activeAnak && $activeAnak->id == $anak->id ? 'selected' : '' }}>
                                    {{ $anak->orang->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            {{-- Bottom Row: Selected Child Academic & Dormitory Details --}}
            @if($activeAnak)
                <div class="space-y-2 pt-1">
                    <div class="text-[0.7rem] sm:text-xs text-emerald-200 uppercase tracking-wider font-extrabold flex items-center gap-1.5">
                        <i data-lucide="user-check" class="w-4 h-4 text-emerald-300 shrink-0"></i>
                        <span>MEMANTAU SANTRI:</span>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-baseline gap-1 sm:gap-2">
                        <h2 class="text-lg sm:text-xl font-black text-white leading-snug">
                            {{ $activeAnak->orang->nama_lengkap }}
                        </h2>
                        <span class="text-xs font-mono font-bold text-emerald-200">
                            (NIUP: {{ $activeAnak->orang->niup ?? '-' }})
                        </span>
                    </div>

                    {{-- Academic Grid Pills (Stacked on Mobile, Horizontal on Desktop) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 sm:gap-3 text-xs pt-1">
                        <div class="bg-white/10 rounded-xl px-3 py-2 border border-white/15 flex items-center gap-2">
                            <i data-lucide="building" class="w-4 h-4 text-emerald-300 shrink-0"></i>
                            <div class="truncate">
                                <span class="text-[0.65rem] text-emerald-200 block">Lembaga</span>
                                <strong class="text-white font-bold text-xs truncate block">{{ $activeAnak->lembaga->nama ?? '-' }}</strong>
                            </div>
                        </div>

                        <div class="bg-white/10 rounded-xl px-3 py-2 border border-white/15 flex items-center gap-2">
                            <i data-lucide="book-open" class="w-4 h-4 text-emerald-300 shrink-0"></i>
                            <div class="truncate">
                                <span class="text-[0.65rem] text-emerald-200 block">Kelas / Rombel</span>
                                <strong class="text-white font-bold text-xs truncate block">{{ $activeAnak->rombelAktif->nama ?? '-' }}</strong>
                            </div>
                        </div>

                        <div class="bg-white/10 rounded-xl px-3 py-2 border border-white/15 flex items-center gap-2">
                            <i data-lucide="home" class="w-4 h-4 text-emerald-300 shrink-0"></i>
                            <div class="truncate">
                                <span class="text-[0.65rem] text-emerald-200 block">Kamar & Asrama</span>
                                <strong class="text-white font-bold text-xs truncate block">{{ $activeAnak->kamarAktif->asrama->nama ?? '-' }} ({{ $activeAnak->kamarAktif->nama ?? '-' }})</strong>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

<div class="space-y-6 animate-fade-in-up">

    {{-- Three Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Card 1: Kehadiran --}}
        <div class="bg-white rounded-2xl shadow-sm border border-surface-200 p-6 flex flex-col justify-between min-h-[170px]">
            <div>
                <h3 class="text-sm font-semibold text-surface-500 uppercase tracking-wider">Kehadiran</h3>
                <p class="text-xs text-surface-400 mt-0.5">Persentase Kehadiran:</p>
            </div>
            <div class="flex items-center justify-between my-2">
                <span class="text-4xl sm:text-5xl font-extrabold text-surface-900">{{ $kehadiranPersen }}%</span>
                <div class="w-12 h-12 rounded-full {{ $kehadiranPersen >= 90 ? 'bg-success-50 text-success-500' : 'bg-warning-50 text-warning-500' }} flex items-center justify-center border border-current/10">
                    <i data-lucide="check-check" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="text-xs text-surface-500 border-t border-surface-100 pt-3">
                Hadir: <span class="font-bold text-surface-800">{{ $kehadiranStats['HADIR'] }}</span> &bull; 
                Izin: <span class="font-bold text-surface-800">{{ $kehadiranStats['IZIN'] }}</span> &bull; 
                Sakit: <span class="font-bold text-surface-800">{{ $kehadiranStats['SAKIT'] }}</span> &bull; 
                Alpha: <span class="font-bold text-danger-600">{{ $kehadiranStats['ALPA'] ?? 0 }}</span>
            </div>
        </div>

        {{-- Card 2: Pelanggaran --}}
        <div class="bg-white rounded-2xl shadow-sm border border-surface-200 p-6 flex flex-col justify-between min-h-[170px]">
            <div>
                <h3 class="text-sm font-semibold text-surface-500 uppercase tracking-wider">Pelanggaran</h3>
                <p class="text-xs text-surface-400 mt-0.5">Total Poin Pelanggaran:</p>
            </div>
            <div class="flex items-center justify-between my-2">
                <span class="text-4xl sm:text-5xl font-extrabold {{ $totalPoinPelanggaran > 0 ? 'text-danger-600' : 'text-success-600' }}">{{ $totalPoinPelanggaran }}</span>
                <div class="w-12 h-12 rounded-full {{ $totalPoinPelanggaran > 0 ? 'bg-danger-50 text-danger-500' : 'bg-success-50 text-success-500' }} flex items-center justify-center border border-current/10">
                    @if($totalPoinPelanggaran > 0)
                        <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                    @else
                        <i data-lucide="check" class="w-6 h-6"></i>
                    @endif
                </div>
            </div>
            <div class="text-xs text-surface-500 border-t border-surface-100 pt-3 flex justify-between items-center">
                <span>Poin: <span class="font-bold text-surface-850">{{ $totalPoinPelanggaran }}/100</span></span>
                <x-badge variant="{{ $totalPoinPelanggaran > 50 ? 'danger' : ($totalPoinPelanggaran > 15 ? 'warning' : 'success') }}" class="text-[0.65rem] px-2 py-0.5">
                    {{ $statusKedisiplinan }}
                </x-badge>
            </div>
        </div>

        {{-- Card 3: Status Keuangan --}}
        <div class="bg-white rounded-2xl shadow-sm border border-surface-200 p-6 flex flex-col justify-between min-h-[170px]">
            <div>
                <h3 class="text-sm font-semibold text-surface-500 uppercase tracking-wider">Status Keuangan</h3>
                <p class="text-xs text-surface-400 mt-0.5 font-medium">Total Tunggakan Tagihan:</p>
            </div>
            <div class="flex items-center justify-between my-2">
                @if($totalTagihanBelumLunas > 0)
                    <span class="text-3xl sm:text-4xl font-extrabold text-danger-600">Rp {{ number_format($totalTagihanBelumLunas, 0, ',', '.') }}</span>
                    <div class="w-12 h-12 rounded-full bg-danger-50 text-danger-500 flex items-center justify-center border border-current/10">
                        <i data-lucide="alert-circle" class="w-6 h-6"></i>
                    </div>
                @else
                    <span class="text-4xl sm:text-5xl font-extrabold text-success-600">Lunas</span>
                    <div class="w-12 h-12 rounded-full bg-success-50 text-success-500 flex items-center justify-center border border-current/10">
                        <i data-lucide="check" class="w-6 h-6"></i>
                    </div>
                @endif
            </div>
            <div class="text-xs text-surface-500 border-t border-surface-100 pt-3 flex justify-between items-center">
                <span class="truncate font-medium text-surface-550">{{ $tagihanTerakhirMsg }}</span>
                <span class="text-surface-650 font-bold text-xs bg-surface-50 border border-surface-100 px-2 py-0.5 rounded-full flex-shrink-0">
                    <!-- SPP {{ now()->isoFormat('MMMM') }}: {{ $sppStatus }} -->
                </span>
            </div>
            <div class="mt-4">
                @php
                    $firstUnpaid = $riwayatTagihan->first(function($t) {
                        $dibayar = $t->pembayaran->sum('jumlah');
                        return $dibayar < $t->total;
                    });
                @endphp

                @if($firstUnpaid)
                <a href="{{ route('portal.payment.show', $firstUnpaid) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow-sm transition-colors">
                    <i data-lucide="credit-card" class="w-4 h-4"></i>
                    Bayar Sekarang
                </a>
                @else
                <a href="{{ route('portal.tagihan') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow-sm transition-colors">
                    <i data-lucide="credit-card" class="w-4 h-4"></i>
                    Lihat Tagihan
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Tabs Section (Folder Style) --}}
    <div>
        {{-- Tab Folder Row --}}
        <div class="flex flex-wrap border-b border-surface-200 -mb-px">
            <button onclick="switchTab('nilai')" id="tab-btn-nilai" class="tab-btn transition-colors px-5 py-3 text-sm font-semibold border-t border-x rounded-t-lg focus:outline-none" style="margin-bottom: -1px;">
                <span class="flex items-center gap-2">
                    <i data-lucide="graduation-cap" class="w-4 h-4"></i> Rincian Nilai
                </span>
            </button>
            <button onclick="switchTab('absensi')" id="tab-btn-absensi" class="tab-btn transition-colors px-5 py-3 text-sm font-semibold border-t border-x rounded-t-lg focus:outline-none" style="margin-bottom: -1px;">
                <span class="flex items-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4"></i> Riwayat Absensi
                </span>
            </button>
            <button onclick="switchTab('tagihan')" id="tab-btn-tagihan" class="tab-btn transition-colors px-5 py-3 text-sm font-semibold border-t border-x rounded-t-lg focus:outline-none" style="margin-bottom: -1px;">
                <span class="flex items-center gap-2">
                    <i data-lucide="receipt" class="w-4 h-4"></i> Riwayat Tagihan
                </span>
            </button>
            <button onclick="switchTab('kedisiplinan')" id="tab-btn-kedisiplinan" class="tab-btn transition-colors px-5 py-3 text-sm font-semibold border-t border-x rounded-t-lg focus:outline-none" style="margin-bottom: -1px;">
                <span class="flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i> Kedisiplinan & Prestasi
                </span>
            </button>
        </div>

        {{-- Tab Content Box --}}
        <div class="bg-white border border-surface-200 rounded-b-2xl rounded-tr-2xl shadow-sm p-6 sm:p-8">
            {{-- TAB 1: Rincian Nilai --}}
            <div id="tab-content-nilai" class="tab-content space-y-6">
                <div class="flex justify-between items-center border-b border-surface-100 pb-3">
                    <h3 class="text-base font-bold text-surface-900">
                        Mata Pelajaran (Semester {{ $grades->first()?->semester === 'GENAP' ? 'Genap' : 'Ganjil' }} {{ $activeTahun?->nama ?? '' }})
                    </h3>
                </div>

                @if($grades->count() > 0)
                <div class="overflow-x-auto border border-surface-150 rounded-xl">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-surface-50 text-surface-600 border-b border-surface-155">
                            <tr>
                                <th class="px-6 py-4 font-semibold text-center w-12">No</th>
                                <th class="px-6 py-4 font-semibold">Mata Pelajaran</th>
                                <th class="px-6 py-4 font-semibold text-center">Nilai (H)</th>
                                <th class="px-6 py-4 font-semibold text-center">PTS</th>
                                <th class="px-6 py-4 font-semibold text-center">PAS</th>
                                <th class="px-6 py-4 font-semibold text-center">Nilai Akhir (Rata²)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-100 text-surface-700">
                            @foreach($grades as $index => $grade)
                            <tr class="hover:bg-surface-50/50 transition-colors">
                                <td class="px-6 py-3.5 text-center">{{ $index + 1 }}</td>
                                <td class="px-6 py-3.5 font-medium text-surface-900">{{ $grade->mataPelajaran->nama ?? '-' }}</td>
                                <td class="px-6 py-3.5 text-center font-medium">{{ round($grade->nilai_tugas) }}</td>
                                <td class="px-6 py-3.5 text-center font-medium">{{ round($grade->nilai_uts) }}</td>
                                <td class="px-6 py-3.5 text-center font-medium">{{ round($grade->nilai_uas) }}</td>
                                <td class="px-6 py-3.5 text-center font-bold text-primary-650 bg-primary-50/20">{{ round($grade->nilai_akhir) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-12 bg-surface-50 rounded-xl border border-surface-100 border-dashed">
                    <i data-lucide="graduation-cap" class="w-12 h-12 text-surface-300 mx-auto mb-3"></i>
                    <p class="font-medium text-surface-900 mb-1">Belum Ada Laporan Nilai</p>
                    <p class="text-sm text-surface-550">Laporan nilai rapor/mata pelajaran untuk semester aktif belum diinputkan oleh guru.</p>
                </div>
                @endif
            </div>

            {{-- TAB 2: Riwayat Absensi --}}
            <div id="tab-content-absensi" class="tab-content space-y-6 hidden">
                <div class="flex justify-between items-center border-b border-surface-100 pb-3">
                    <h3 class="text-base font-bold text-surface-900">Riwayat Kehadiran Kelas</h3>
                </div>

                @if($presensis->count() > 0)
                <div class="overflow-x-auto border border-surface-150 rounded-xl">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-surface-50 text-surface-600 border-b border-surface-155">
                            <tr>
                                <th class="px-6 py-4 font-semibold text-center w-12">No</th>
                                <th class="px-6 py-4 font-semibold">Tanggal</th>
                                <th class="px-6 py-4 font-semibold">Rombel/Kelas</th>
                                <th class="px-6 py-4 font-semibold text-center">Status</th>
                                <th class="px-6 py-4 font-semibold">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-100 text-surface-700">
                            @foreach($presensis as $index => $absensi)
                            <tr class="hover:bg-surface-50/50 transition-colors">
                                <td class="px-6 py-3.5 text-center">{{ $index + 1 }}</td>
                                <td class="px-6 py-3.5 font-medium text-surface-900">{{ $absensi->attendance_date->isoFormat('dddd, D MMMM YYYY') }}</td>
                                <td class="px-6 py-3.5 text-surface-600">{{ $absensi->rombel->nama ?? '-' }}</td>
                                <td class="px-6 py-3.5 text-center">
                                    @php
                                        $variant = match(strtoupper($absensi->status)) {
                                            'HADIR' => 'success',
                                            'IZIN' => 'warning',
                                            'SAKIT' => 'info',
                                            'ALPHA' => 'danger',
                                            'ALPA' => 'danger',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <x-badge variant="{{ $variant }}" class="px-2 py-0.5 text-xs font-semibold">
                                        {{ strtoupper($absensi->status) }}
                                    </x-badge>
                                </td>
                                <td class="px-6 py-3.5 text-surface-500 text-xs italic">{{ $absensi->keterangan ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-12 bg-surface-50 rounded-xl border border-surface-100 border-dashed">
                    <i data-lucide="calendar" class="w-12 h-12 text-surface-300 mx-auto mb-3"></i>
                    <p class="font-medium text-surface-900 mb-1">Belum Ada Riwayat Kehadiran</p>
                    <p class="text-sm text-surface-550">Belum ada pencatatan kehadiran pelajaran untuk santri di tahun ajaran ini.</p>
                </div>
                @endif
            </div>

            {{-- TAB 3: Riwayat Tagihan --}}
            <div id="tab-content-tagihan" class="tab-content space-y-6 hidden">
                <div class="flex justify-between items-center border-b border-surface-100 pb-3">
                    <h3 class="text-base font-bold text-surface-900">Daftar Tagihan & Status Pembayaran</h3>
                </div>

                @if($riwayatTagihan->count() > 0)
                <div class="overflow-x-auto border border-surface-150 rounded-xl">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-surface-50 text-surface-600 border-b border-surface-155">
                            <tr>
                                <th class="px-6 py-4 font-semibold text-center w-12">No</th>
                                <th class="px-6 py-4 font-semibold">Bulan / Komponen</th>
                                <th class="px-6 py-4 font-semibold text-right">Total Tagihan</th>
                                <th class="px-6 py-4 font-semibold text-right">Jumlah Dibayar</th>
                                <th class="px-6 py-4 font-semibold text-right">Sisa Tagihan</th>
                                <th class="px-6 py-4 font-semibold text-center">Status</th>
                                <th class="px-6 py-4 font-semibold">Jatuh Tempo</th>
                                <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-100 text-surface-700">
                            @foreach($riwayatTagihan as $index => $t)
                            @php
                                $dibayar = $t->pembayaran->sum('jumlah');
                                $sisa = max(0, $t->total - $dibayar);
                                $statusTampil = $sisa <= 0 ? 'LUNAS' : ($dibayar > 0 ? 'SEBAGIAN' : $t->status);
                            @endphp
                            <tr class="hover:bg-surface-50/50 transition-colors">
                                <td class="px-6 py-3.5 text-center">{{ $index + 1 }}</td>
                                <td class="px-6 py-3.5 font-bold text-surface-900">
                                    {{ $t->bulan }} - {{ $t->komponenBiaya->nama ?? 'SPP' }}
                                </td>
                                <td class="px-6 py-3.5 text-right font-medium">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                                <td class="px-6 py-3.5 text-right text-success-600 font-medium">Rp {{ number_format($dibayar, 0, ',', '.') }}</td>
                                <td class="px-6 py-3.5 text-right font-bold {{ $sisa > 0 ? 'text-danger-600 bg-danger-50/10' : 'text-success-600' }}">
                                    Rp {{ number_format($sisa, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                    <x-badge variant="{{ $statusTampil === 'LUNAS' ? 'success' : ($statusTampil === 'SEBAGIAN' ? 'warning' : 'danger') }}" class="px-2 py-0.5 text-xs font-semibold">
                                        {{ $statusTampil === 'LUNAS' ? 'LUNAS' : ($statusTampil === 'SEBAGIAN' ? 'SEBAGIAN' : 'BELUM BAYAR') }}
                                    </x-badge>
                                </td>
                                <td class="px-6 py-3.5 text-surface-500">{{ $t->jatuh_tempo ? $t->jatuh_tempo->isoFormat('D MMMM YYYY') : '-' }}</td>
                                <td class="px-6 py-3.5 text-center">
                                    @if($sisa > 0)
                                    <a href="{{ route('portal.payment.show', $t) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors shadow-sm">
                                        <i data-lucide="credit-card" class="w-3.5 h-3.5"></i>
                                        Bayar Online
                                    </a>
                                    @else
                                    <span class="text-xs text-success-500 font-medium">✓ Lunas</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-12 bg-surface-50 rounded-xl border border-surface-100 border-dashed">
                    <i data-lucide="receipt" class="w-12 h-12 text-surface-300 mx-auto mb-3"></i>
                    <p class="font-medium text-surface-900 mb-1">Tidak Ada Tagihan</p>
                    <p class="text-sm text-surface-550">Belum ada tagihan biaya pendidikan yang diterbitkan untuk santri ini.</p>
                </div>
                @endif
            </div>

            {{-- TAB 4: Kedisiplinan & Prestasi --}}
            <div id="tab-content-kedisiplinan" class="tab-content space-y-8 hidden">
                {{-- Bagian Pelanggaran --}}
                <div class="space-y-4">
                    <div class="border-b border-surface-100 pb-3">
                        <h3 class="text-base font-bold text-surface-900 flex items-center gap-2">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-danger-500"></i>
                            <span>Catatan Pelanggaran Tata Tertib</span>
                        </h3>
                    </div>

                    @if($pelanggarans->count() > 0)
                    <div class="overflow-x-auto border border-surface-150 rounded-xl">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-surface-50 text-surface-600 border-b border-surface-155">
                                <tr>
                                    <th class="px-6 py-4 font-semibold text-center w-12">No</th>
                                    <th class="px-6 py-4 font-semibold">Tanggal</th>
                                    <th class="px-6 py-4 font-semibold">Jenis Pelanggaran</th>
                                    <th class="px-6 py-4 font-semibold text-center">Kategori</th>
                                    <th class="px-6 py-4 font-semibold text-center">Poin</th>
                                    <th class="px-6 py-4 font-semibold">Tindakan / Sanksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-100 text-surface-700">
                                @foreach($pelanggarans as $index => $p)
                                <tr class="hover:bg-surface-50/50 transition-colors">
                                    <td class="px-6 py-3.5 text-center">{{ $index + 1 }}</td>
                                    <td class="px-6 py-3.5 font-medium text-surface-900">{{ $p->tanggal->isoFormat('D MMMM YYYY') }}</td>
                                    <td class="px-6 py-3.5 text-surface-800">
                                        <div class="font-bold text-surface-900">{{ $p->jenisPelanggaran->nama ?? '-' }}</div>
                                        @if($p->keterangan)
                                            <div class="text-xs text-surface-500 mt-0.5 whitespace-pre-wrap leading-relaxed">{{ $p->keterangan }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3.5 text-center">
                                        @php
                                            $katVariant = match(strtoupper($p->jenisPelanggaran->kategori ?? '')) {
                                                'RINGAN' => 'success',
                                                'SEDANG' => 'warning',
                                                'BERAT' => 'danger',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <x-badge variant="{{ $katVariant }}" class="px-2 py-0.5 text-xs font-semibold">
                                            {{ $p->jenisPelanggaran->kategori ?? '-' }}
                                        </x-badge>
                                    </td>
                                    <td class="px-6 py-3.5 text-center font-bold text-danger-600 bg-danger-50/20">{{ $p->jenisPelanggaran->poin ?? 0 }}</td>
                                    <td class="px-6 py-3.5 text-surface-700 font-medium italic">{{ $p->tindakan ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-8 bg-success-50/30 rounded-xl border border-success-200 border-dashed">
                        <i data-lucide="shield-check" class="w-10 h-10 text-success-500 mx-auto mb-2"></i>
                        <p class="font-semibold text-success-800 mb-0.5">Alhamdulillah, Nihil Pelanggaran</p>
                        <p class="text-xs text-success-600">Santri berperilaku baik dan disiplin selama berada di lingkungan pesantren.</p>
                    </div>
                    @endif
                </div>

                {{-- Bagian Prestasi --}}
                <div class="space-y-4 pt-4">
                    <div class="border-b border-surface-100 pb-3">
                        <h3 class="text-base font-bold text-surface-900 flex items-center gap-2">
                            <i data-lucide="award" class="w-5 h-5 text-success-500"></i>
                            <span>Catatan Prestasi & Penghargaan</span>
                        </h3>
                    </div>

                    @if($prestasis->count() > 0)
                    <div class="overflow-x-auto border border-surface-150 rounded-xl">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-surface-50 text-surface-600 border-b border-surface-155">
                                <tr>
                                    <th class="px-6 py-4 font-semibold text-center w-12">No</th>
                                    <th class="px-6 py-4 font-semibold">Tanggal</th>
                                    <th class="px-6 py-4 font-semibold">Judul Prestasi</th>
                                    <th class="px-6 py-4 font-semibold text-center">Tingkat</th>
                                    <th class="px-6 py-4 font-semibold">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-100 text-surface-700">
                                @foreach($prestasis as $index => $pr)
                                <tr class="hover:bg-surface-50/50 transition-colors">
                                    <td class="px-6 py-3.5 text-center">{{ $index + 1 }}</td>
                                    <td class="px-6 py-3.5 font-medium text-surface-900">{{ $pr->tanggal->isoFormat('D MMMM YYYY') }}</td>
                                    <td class="px-6 py-3.5 font-bold text-surface-900 text-success-700">{{ $pr->judul }}</td>
                                    <td class="px-6 py-3.5 text-center">
                                        <x-badge variant="info" class="px-2 py-0.5 text-xs font-semibold">
                                            {{ $pr->tingkat ?? 'INTERNAL' }}
                                        </x-badge>
                                    </td>
                                    <td class="px-6 py-3.5 text-surface-600 text-xs whitespace-pre-wrap leading-relaxed">{{ $pr->keterangan ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-8 bg-surface-50 rounded-xl border border-surface-150 border-dashed">
                        <i data-lucide="award" class="w-10 h-10 text-surface-300 mx-auto mb-2"></i>
                        <p class="font-medium text-surface-700">Belum Ada Catatan Prestasi</p>
                        <p class="text-xs text-surface-500">Santri belum memiliki riwayat prestasi akademis/non-akademis yang tercatat.</p>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
    function switchTab(tabId) {
        // Hide all contents
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        // Show selected content
        document.getElementById('tab-content-' + tabId).classList.remove('hidden');

        // Reset all tab button styles
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('bg-white', 'border-surface-200', 'text-primary-750');
            btn.classList.add('bg-surface-50/80', 'border-transparent', 'text-surface-500', 'hover:text-surface-700', 'hover:bg-surface-100/50');
            btn.style.borderColor = 'transparent';
        });

        // Set active tab button style
        const activeBtn = document.getElementById('tab-btn-' + tabId);
        activeBtn.classList.remove('bg-surface-50/80', 'border-transparent', 'text-surface-500', 'hover:text-surface-700', 'hover:bg-surface-100/50');
        activeBtn.classList.add('bg-white', 'text-primary-750');
        
        // Match Balsamiq folder tabs border
        activeBtn.style.borderTopColor = 'var(--color-surface-200)';
        activeBtn.style.borderLeftColor = 'var(--color-surface-200)';
        activeBtn.style.borderRightColor = 'var(--color-surface-200)';
        activeBtn.style.borderBottomColor = 'transparent';
    }

    // Initialize Active Tab
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab');
        
        const validTabs = ['nilai', 'absensi', 'tagihan', 'kedisiplinan'];
        let startTab = '{{ $activeTab }}';
        
        if (tabParam && validTabs.includes(tabParam)) {
            startTab = tabParam;
        }
        
        switchTab(startTab);
    });
</script>
@endpush
