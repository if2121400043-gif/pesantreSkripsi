@extends('layouts.portal')

@section('title', 'Kedisiplinan & Prestasi Santri — PP Nurul Furqon')

@section('content')
<div class="space-y-6">

    {{-- Hero Summary Header --}}
    <div class="rounded-3xl p-6 sm:p-7 shadow-lg text-white" style="background: linear-gradient(135deg, #b45309, #78350f) !important;">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-white/15 text-[0.7rem] font-bold backdrop-blur-sm border border-white/20 mb-2">
                    <i data-lucide="shield-alert" class="w-3.5 h-3.5 text-amber-300"></i>
                    Catatan Kedisiplinan
                </span>
                <h1 class="text-xl sm:text-2xl font-extrabold font-heading">
                    Kedisiplinan & Prestasi Santri
                </h1>
                <p class="text-xs text-amber-100 mt-1">
                    Santri: <strong class="text-white">{{ $activeAnak->orang->nama_lengkap ?? '-' }}</strong> ({{ $activeAnak->orang->niup ?? '-' }})
                </p>
            </div>

            <div class="bg-white/15 backdrop-blur-md rounded-2xl p-4 border border-white/20 text-right w-full sm:w-auto">
                <span class="text-[0.65rem] uppercase font-bold text-amber-200 block">Total Poin Pelanggaran</span>
                <span class="text-3xl font-black text-white">{{ $totalPoinPelanggaran }} <span class="text-xs text-amber-200 font-normal">/ 100</span></span>
            </div>
        </div>
    </div>

    {{-- 2 Columns Grid: Pelanggaran vs Prestasi --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Column 1: Pelanggaran --}}
        <div class="bg-white rounded-3xl border border-surface-200 shadow-sm p-6 flex flex-col justify-between">
            <div>
                <h2 class="text-base font-bold text-surface-900 border-b border-surface-100 pb-3 mb-4 flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600"></i>
                        Catatan Pelanggaran Disiplin
                    </span>
                    <span class="px-2.5 py-0.5 text-xs font-extrabold rounded-full {{ $totalPoinPelanggaran > 0 ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                        {{ $statusKedisiplinan }}
                    </span>
                </h2>

                @if($pelanggarans->count() > 0)
                    <div class="space-y-3">
                        @foreach($pelanggarans as $pelanggaran)
                            <div class="p-4 rounded-2xl border border-rose-200 bg-rose-50/30 flex justify-between items-start gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-extrabold text-surface-900 text-sm">{{ $pelanggaran->jenisPelanggaran->nama ?? 'Pelanggaran' }}</span>
                                    </div>
                                    <p class="text-xs text-surface-500 mt-0.5">Tanggal: {{ \Carbon\Carbon::parse($pelanggaran->tanggal)->translatedFormat('d M Y') }}</p>
                                    @if($pelanggaran->tindakan)
                                        <p class="text-xs text-rose-700 bg-white p-2 rounded-xl border border-rose-100 mt-2">
                                            <strong>Sanksi/Tindakan:</strong> {{ $pelanggaran->tindakan }}
                                        </p>
                                    @endif
                                </div>
                                <span class="px-2.5 py-1 bg-rose-600 text-white font-mono font-black text-xs rounded-xl shadow-xs shrink-0">
                                    +{{ $pelanggaran->jenisPelanggaran->poin ?? 0 }} Poin
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-12 text-center">
                        <i data-lucide="check-circle" class="w-12 h-12 text-emerald-500 mx-auto mb-3"></i>
                        <h3 class="text-base font-bold text-surface-900 mb-1">Alhamdulillah Suci Pelanggaran</h3>
                        <p class="text-xs text-surface-500 max-w-xs mx-auto">Tidak ada catatan pelanggaran disiplin yang tercatat. Pertahankan istiqomah ananda.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Column 2: Prestasi --}}
        <div class="bg-white rounded-3xl border border-surface-200 shadow-sm p-6 flex flex-col justify-between">
            <div>
                <h2 class="text-base font-bold text-surface-900 border-b border-surface-100 pb-3 mb-4 flex items-center gap-2">
                    <i data-lucide="award" class="w-5 h-5 text-amber-500"></i>
                    Catatan Prestasi & Apresiasi
                </h2>

                @if($prestasis->count() > 0)
                    <div class="space-y-3">
                        @foreach($prestasis as $prestasi)
                            <div class="p-4 rounded-2xl border border-amber-200 bg-amber-50/30 flex justify-between items-start gap-3">
                                <div>
                                    <h4 class="font-extrabold text-surface-900 text-sm">{{ $prestasi->judul ?? $prestasi->nama_prestasi ?? 'Prestasi' }}</h4>
                                    <p class="text-xs text-surface-500 mt-0.5">Tingkat: {{ $prestasi->tingkat ?? 'Internal' }} • {{ \Carbon\Carbon::parse($prestasi->tanggal)->translatedFormat('d M Y') }}</p>
                                    @if($prestasi->keterangan)
                                        <p class="text-xs text-surface-600 mt-1 italic">{{ $prestasi->keterangan }}</p>
                                    @endif
                                </div>
                                <span class="px-2.5 py-1 bg-amber-500 text-white font-extrabold text-xs rounded-xl shadow-xs shrink-0">
                                    🏆 Juara / Apresiasi
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-12 text-center">
                        <i data-lucide="award" class="w-12 h-12 text-amber-400 mx-auto mb-3"></i>
                        <h3 class="text-base font-bold text-surface-900 mb-1">Belum Ada Catatan Prestasi</h3>
                        <p class="text-xs text-surface-500 max-w-xs mx-auto">Catatan kejuaraan dan apresiasi santri akan dirangkum di sini.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
