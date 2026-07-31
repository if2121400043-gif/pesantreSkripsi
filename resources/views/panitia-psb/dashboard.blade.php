@extends('layouts.panitia-psb')

@section('title', 'Portal Panitia PSB — PP Nurul Furqon')

@section('content')
<div class="space-y-6">

    {{-- Welcome Banner Header --}}
    <div class="rounded-3xl p-6 md:p-8 shadow-lg relative overflow-hidden text-white" style="background: linear-gradient(135deg, #4c1d95, #2e1065) !important; color: #ffffff !important;">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-xs font-semibold backdrop-blur-sm border border-white/20 mb-3" style="color: #ffffff !important;">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-300"></i>
                    Portal Operasional Penerimaan Santri Baru (PSB)
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold font-heading" style="color: #ffffff !important;">
                    Manajemen Panitia PSB
                </h1>
                <p class="text-xs md:text-sm mt-1 max-w-xl" style="color: #ddd6fe !important;">
                    Pilih menu aksi cepat di bawah ini untuk seleksi calon santri, pengaturan gelombang pendaftaran, atau rekapitulasi pendaftar baru.
                </p>
            </div>
            
            <div class="flex items-center gap-3 shrink-0 p-3 rounded-2xl border border-white/20" style="background-color: rgba(255, 255, 255, 0.15) !important;">
                <div class="w-10 h-10 rounded-xl bg-amber-400/20 text-amber-300 flex items-center justify-center font-bold">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                </div>
                <div>
                    <div class="text-[0.65rem] uppercase tracking-wider font-semibold" style="color: #ddd6fe !important;">Tahun Pelajaran</div>
                    <div class="text-sm font-bold" style="color: #ffffff !important;">{{ $tahunAktif->nama ?? 'Aktif' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Bar --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white rounded-2xl border border-surface-200 p-4 text-center shadow-sm">
            <div class="text-2xl font-extrabold text-surface-900">{{ $totalPendaftar }}</div>
            <div class="text-xs text-surface-500 mt-1 font-medium">Total Pendaftar</div>
        </div>
        <div class="bg-blue-50/60 rounded-2xl border border-blue-100 p-4 text-center shadow-sm">
            <div class="text-2xl font-extrabold text-blue-700">{{ $baruMasuk }}</div>
            <div class="text-xs text-blue-600 mt-1 font-medium">Baru Masuk</div>
        </div>
        <div class="bg-amber-50/60 rounded-2xl border border-amber-100 p-4 text-center shadow-sm">
            <div class="text-2xl font-extrabold text-amber-700">{{ $hadirTes }}</div>
            <div class="text-xs text-amber-600 mt-1 font-medium">Hadir Tes</div>
        </div>
        <div class="bg-emerald-50/60 rounded-2xl border border-emerald-100 p-4 text-center shadow-sm">
            <div class="text-2xl font-extrabold text-emerald-700">{{ $diterima }}</div>
            <div class="text-xs text-emerald-600 mt-1 font-medium">Diterima</div>
        </div>
        <div class="bg-rose-50/60 rounded-2xl border border-rose-100 p-4 text-center shadow-sm">
            <div class="text-2xl font-extrabold text-rose-700">{{ $tidakLulus }}</div>
            <div class="text-xs text-rose-600 mt-1 font-medium">Tidak Lulus</div>
        </div>
        <div class="bg-surface-100 rounded-2xl border border-surface-200 p-4 text-center shadow-sm">
            <div class="text-2xl font-extrabold text-surface-600">{{ $dibatalkan }}</div>
            <div class="text-xs text-surface-500 mt-1 font-medium">Dibatalkan</div>
        </div>
    </div>

    {{-- MAIN ACTION CARDS GRID (Menu Utama Panitia PSB) --}}
    <div>
        <h2 class="text-base font-bold text-surface-900 mb-3 flex items-center gap-2">
            <i data-lucide="grid" class="w-5 h-5 text-purple-700"></i>
            Menu Utama Panitia PSB
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            
            {{-- Card 1: Kelola Calon Santri --}}
            <a href="{{ route('panitia-psb.calon-santri.index') }}" class="group bg-white rounded-3xl p-5 border border-surface-200 shadow-sm hover:shadow-xl hover:border-purple-500 hover:-translate-y-1 transition-all duration-300 relative overflow-hidden flex flex-col justify-between">
                <div class="absolute -right-8 -top-8 w-28 h-28 bg-purple-100/50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="w-14 h-14 rounded-2xl text-white flex items-center justify-center shadow-md mb-4 group-hover:rotate-6 transition-transform" style="background: linear-gradient(135deg, #6d28d9, #4c1d95) !important;">
                        <i data-lucide="users" class="w-7 h-7" style="color: #ffffff !important;"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-surface-900 group-hover:text-purple-700 transition-colors">Kelola Calon Santri</h3>
                    <p class="text-xs text-surface-500 mt-1 leading-relaxed">Verifikasi berkas, ubah status kelulusan, dan input nilai tes calon santri baru.</p>
                </div>
                <div class="mt-6 pt-3 border-t border-surface-100 flex items-center justify-between text-xs font-bold text-purple-700">
                    <span>Data Calon Santri</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            {{-- Card 2: Kelola Gelombang PSB --}}
            <a href="{{ route('panitia-psb.gelombang.index') }}" class="group bg-white rounded-3xl p-5 border border-surface-200 shadow-sm hover:shadow-xl hover:border-indigo-500 hover:-translate-y-1 transition-all duration-300 relative overflow-hidden flex flex-col justify-between">
                <div class="absolute -right-8 -top-8 w-28 h-28 bg-indigo-100/50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="w-14 h-14 rounded-2xl text-white flex items-center justify-center shadow-md mb-4 group-hover:rotate-6 transition-transform" style="background: linear-gradient(135deg, #4f46e5, #3730a3) !important;">
                        <i data-lucide="door-open" class="w-7 h-7" style="color: #ffffff !important;"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-surface-900 group-hover:text-indigo-700 transition-colors">Gelombang Pendaftaran</h3>
                    <p class="text-xs text-surface-500 mt-1 leading-relaxed">Atur kuota pendaftaran, jadwal tes, serta tanggal buka dan tutup gelombang.</p>
                </div>
                <div class="mt-6 pt-3 border-t border-surface-100 flex items-center justify-between text-xs font-bold text-indigo-700">
                    <span>Atur Gelombang</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            {{-- Card 3: Cetak & Rekap Data --}}
            <a href="{{ route('panitia-psb.calon-santri.index') }}" class="group bg-white rounded-3xl p-5 border border-surface-200 shadow-sm hover:shadow-xl hover:border-amber-500 hover:-translate-y-1 transition-all duration-300 relative overflow-hidden flex flex-col justify-between">
                <div class="absolute -right-8 -top-8 w-28 h-28 bg-amber-100/50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="w-14 h-14 rounded-2xl text-white flex items-center justify-center shadow-md mb-4 group-hover:rotate-6 transition-transform" style="background: linear-gradient(135deg, #d97706, #b45309) !important;">
                        <i data-lucide="printer" class="w-7 h-7" style="color: #ffffff !important;"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-surface-900 group-hover:text-amber-700 transition-colors">Rekapitulasi & Export</h3>
                    <p class="text-xs text-surface-500 mt-1 leading-relaxed">Download formulir pendaftaran, rekap data santri baru, & export Excel.</p>
                </div>
                <div class="mt-6 pt-3 border-t border-surface-100 flex items-center justify-between text-xs font-bold text-amber-700">
                    <span>Cetak & Export Data</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

        </div>
    </div>

    {{-- Tables Grid --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        
        {{-- Gelombang Aktif --}}
        <div class="bg-white rounded-3xl p-6 border border-surface-200 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-surface-900 text-base flex items-center gap-2">
                    <i data-lucide="door-open" class="w-5 h-5 text-purple-700"></i>
                    Gelombang Pendaftaran Aktif
                </h3>
            </div>

            <div class="divide-y divide-surface-100">
                @forelse($gelombangsAktif as $gel)
                    <div class="py-3.5 first:pt-0 last:pb-0">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="font-extrabold text-surface-900 text-sm">{{ $gel->nama }}</div>
                                <div class="text-xs text-surface-500 mt-0.5">
                                    {{ \Carbon\Carbon::parse($gel->tanggal_buka)->format('d M Y') }} — {{ \Carbon\Carbon::parse($gel->tanggal_tutup)->format('d M Y') }}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-extrabold text-purple-700">{{ $gel->calon_santri_count }}</div>
                                <div class="text-[0.65rem] text-surface-450">/ {{ $gel->kuota }} kuota</div>
                            </div>
                        </div>
                        @php
                            $persen = $gel->kuota > 0 ? min(($gel->calon_santri_count / $gel->kuota) * 100, 100) : 0;
                        @endphp
                        <div class="w-full h-2 bg-surface-100 rounded-full mt-2.5 overflow-hidden">
                            <div class="{{ $persen >= 100 ? 'bg-danger-500' : 'bg-purple-600' }} h-full rounded-full transition-all" style="width: {{ $persen }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-surface-500">
                        <i data-lucide="door-open" class="w-8 h-8 text-surface-300 mx-auto mb-2"></i>
                        <p class="text-xs">Belum ada gelombang aktif saat ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Pendaftar Terbaru --}}
        <div class="bg-white rounded-3xl p-6 border border-surface-200 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-surface-900 text-base flex items-center gap-2">
                    <i data-lucide="users" class="w-5 h-5 text-indigo-700"></i>
                    Pendaftar Terbaru
                </h3>
            </div>

            <div class="divide-y divide-surface-100">
                @forelse($pendaftarTerbaru as $cs)
                    <a href="{{ route('panitia-psb.calon-santri.show', $cs) }}" class="py-3 flex items-center justify-between gap-3 hover:bg-surface-50 p-2 rounded-2xl transition-colors">
                        <div>
                            <div class="font-bold text-surface-900 text-sm leading-tight">{{ $cs->nama_lengkap }}</div>
                            <div class="text-[0.65rem] text-surface-500 mt-0.5">
                                <span class="font-mono text-purple-700 font-semibold">{{ $cs->no_pendaftaran }}</span>
                                • {{ $cs->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div class="shrink-0">
                            @if($cs->status === 'DITERIMA')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-700 border border-emerald-200">Diterima</span>
                            @elseif($cs->status === 'TIDAK_LULUS')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-rose-100 text-rose-700 border border-rose-200">Tidak Lulus</span>
                            @elseif($cs->status === 'HADIR_TES')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-amber-100 text-amber-700 border border-amber-200">Hadir Tes</span>
                            @elseif($cs->status === 'DIBATALKAN')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-surface-200 text-surface-700 border border-surface-300">Dibatalkan</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-blue-100 text-blue-700 border border-blue-200">Baru Masuk</span>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="py-8 text-center text-surface-500">
                        <i data-lucide="users" class="w-8 h-8 text-surface-300 mx-auto mb-2"></i>
                        <p class="text-xs">Belum ada pendaftar baru.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
