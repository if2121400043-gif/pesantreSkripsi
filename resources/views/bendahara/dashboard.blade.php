@extends('layouts.bendahara')

@section('title', 'Portal Keuangan Bendahara — PP Nurul Furqon')

@section('content')
<div class="space-y-6">

    {{-- Welcome Banner Header --}}
    <div class="rounded-3xl p-6 md:p-8 shadow-lg relative overflow-hidden text-white" style="background: linear-gradient(135deg, #0f766e, #042f2e) !important; color: #ffffff !important;">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-xs font-semibold backdrop-blur-sm border border-white/20 mb-3" style="color: #ffffff !important;">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-300"></i>
                    Portal Operasional Keuangan Pesantren
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold font-heading" style="color: #ffffff !important;">
                    Manajemen Keuangan & Kasir
                </h1>
                <p class="text-xs md:text-sm mt-1 max-w-xl" style="color: #ccfbf1 !important;">
                    Pilih menu aksi cepat di bawah ini untuk pencatatan pembayaran SPP/Syahriah, pembuatan tagihan santri, atau unduh laporan keuangan.
                </p>
            </div>
            
            <div class="flex items-center gap-3 shrink-0 p-3 rounded-2xl border border-white/20" style="background-color: rgba(255, 255, 255, 0.15) !important;">
                <div class="w-10 h-10 rounded-xl bg-amber-400/20 text-amber-300 flex items-center justify-center font-bold">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                </div>
                <div>
                    <div class="text-[0.65rem] uppercase tracking-wider font-semibold" style="color: #ccfbf1 !important;">Tahun Pelajaran</div>
                    <div class="text-sm font-bold" style="color: #ffffff !important;">{{ $tahunAktif->nama ?? 'Aktif' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Financial Stats Bar --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Card 1: Pemasukan Kas --}}
        <div class="bg-white p-5 rounded-2xl border border-surface-200 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-xs text-surface-500 font-medium">Total Pemasukan Kas</div>
                <div class="text-xl md:text-2xl font-extrabold text-emerald-700 mt-1">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
                <div class="text-[0.7rem] text-surface-450 mt-1">Penerimaan riil pembayaran santri</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-100">
                <i data-lucide="wallet" class="w-6 h-6"></i>
            </div>
        </div>

        {{-- Card 2: Tunggakan --}}
        <div class="bg-white p-5 rounded-2xl border border-surface-200 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-xs text-surface-500 font-medium">Total Piutang / Tunggakan</div>
                <div class="text-xl md:text-2xl font-extrabold text-danger-600 mt-1">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</div>
                <div class="text-[0.7rem] text-surface-450 mt-1">Sisa tagihan yang belum dibayar</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-danger-50 text-danger-600 flex items-center justify-center shrink-0 border border-danger-100">
                <i data-lucide="alert-circle" class="w-6 h-6"></i>
            </div>
        </div>

        {{-- Card 3: Realisasi Bulan Ini --}}
        <div class="bg-white p-5 rounded-2xl border border-surface-200 shadow-sm flex items-center justify-between">
            <div class="flex-1 pr-3">
                <div class="text-xs text-surface-500 font-medium">Realisasi Bulan Ini ({{ $rekapBulanIni['bulan'] }})</div>
                <div class="text-xl font-extrabold text-teal-800 mt-1">Rp {{ number_format($rekapBulanIni['pemasukan'], 0, ',', '.') }}</div>
                <div class="text-[0.7rem] text-teal-700 font-bold mt-1">{{ $rekapBulanIni['persenLunas'] }}% Terbayar</div>
                <div class="w-full bg-surface-100 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-teal-700 h-1.5 rounded-full" style="width: {{ $rekapBulanIni['persenLunas'] }}%"></div>
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center shrink-0 border border-teal-100">
                <i data-lucide="trending-up" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    {{-- MAIN ACTION CARDS GRID (Menu Utama Bendahara) --}}
    <div>
        <h2 class="text-base font-bold text-surface-900 mb-3 flex items-center gap-2">
            <i data-lucide="grid" class="w-5 h-5 text-teal-700"></i>
            Menu Utama Operasional Keuangan
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            {{-- Card 1: Kelola & Input Pembayaran SPP --}}
            <a href="{{ route('bendahara.tagihan.index') }}" class="group bg-white rounded-3xl p-5 border border-surface-200 shadow-sm hover:shadow-xl hover:border-teal-500 hover:-translate-y-1 transition-all duration-300 relative overflow-hidden flex flex-col justify-between">
                <div class="absolute -right-8 -top-8 w-28 h-28 bg-teal-100/50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="w-14 h-14 rounded-2xl text-white flex items-center justify-center shadow-md mb-4 group-hover:rotate-6 transition-transform" style="background: linear-gradient(135deg, #0f766e, #042f2e) !important;">
                        <i data-lucide="banknote" class="w-7 h-7" style="color: #ffffff !important;"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-surface-900 group-hover:text-teal-700 transition-colors">Bayar Tagihan Santri</h3>
                    <p class="text-xs text-surface-500 mt-1 leading-relaxed">Terima pembayaran kasir SPP/Syahriah santri & cetak kuitansi resmi.</p>
                </div>
                <div class="mt-6 pt-3 border-t border-surface-100 flex items-center justify-between text-xs font-bold text-teal-700">
                    <span>Kasir & Tagihan</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            {{-- Card 2: Laporan Keuangan --}}
            <a href="{{ route('bendahara.laporan-keuangan.index') }}" class="group bg-white rounded-3xl p-5 border border-surface-200 shadow-sm hover:shadow-xl hover:border-emerald-500 hover:-translate-y-1 transition-all duration-300 relative overflow-hidden flex flex-col justify-between">
                <div class="absolute -right-8 -top-8 w-28 h-28 bg-emerald-100/50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="w-14 h-14 rounded-2xl text-white flex items-center justify-center shadow-md mb-4 group-hover:rotate-6 transition-transform" style="background: linear-gradient(135deg, #059669, #047857) !important;">
                        <i data-lucide="file-spreadsheet" class="w-7 h-7" style="color: #ffffff !important;"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-surface-900 group-hover:text-emerald-700 transition-colors">Laporan Keuangan</h3>
                    <p class="text-xs text-surface-500 mt-1 leading-relaxed">Rekapitulasi total pemasukan, piutang, dan export laporan ke Excel.</p>
                </div>
                <div class="mt-6 pt-3 border-t border-surface-100 flex items-center justify-between text-xs font-bold text-emerald-700">
                    <span>Cetak Laporan</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            {{-- Card 3: Blast WA Reminder Tagihan --}}
            <form action="{{ route('bendahara.tagihan.blast-reminder') }}" method="POST" class="group bg-white rounded-3xl p-5 border border-surface-200 shadow-sm hover:shadow-xl hover:border-green-500 hover:-translate-y-1 transition-all duration-300 relative overflow-hidden flex flex-col justify-between cursor-pointer" onclick="if(confirm('Kirim WhatsApp pengingat tagihan ke seluruh wali santri yang belum lunas?')) this.submit();">
                @csrf
                <div class="absolute -right-8 -top-8 w-28 h-28 bg-green-100/50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="w-14 h-14 rounded-2xl text-white flex items-center justify-center shadow-md mb-4 group-hover:rotate-6 transition-transform" style="background: linear-gradient(135deg, #16a34a, #15803d) !important;">
                        <i data-lucide="send" class="w-7 h-7" style="color: #ffffff !important;"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-surface-900 group-hover:text-green-700 transition-colors">Blast Reminder WA</h3>
                    <p class="text-xs text-surface-500 mt-1 leading-relaxed">Kirim notifikasi tagihan massal otomatis ke WhatsApp wali santri.</p>
                </div>
                <div class="mt-6 pt-3 border-t border-surface-100 flex items-center justify-between text-xs font-bold text-green-700">
                    <span>Kirim WA Massal</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </form>

            {{-- Card 4: Komponen Biaya --}}
            <a href="{{ route('bendahara.komponen-biaya.index') }}" class="group bg-white rounded-3xl p-5 border border-surface-200 shadow-sm hover:shadow-xl hover:border-amber-500 hover:-translate-y-1 transition-all duration-300 relative overflow-hidden flex flex-col justify-between">
                <div class="absolute -right-8 -top-8 w-28 h-28 bg-amber-100/50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="w-14 h-14 rounded-2xl text-white flex items-center justify-center shadow-md mb-4 group-hover:rotate-6 transition-transform" style="background: linear-gradient(135deg, #d97706, #b45309) !important;">
                        <i data-lucide="layers" class="w-7 h-7" style="color: #ffffff !important;"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-surface-900 group-hover:text-amber-700 transition-colors">Komponen Biaya</h3>
                    <p class="text-xs text-surface-500 mt-1 leading-relaxed">Atur tarif komponen SPP, pendaftaran, bangunan, dan syahriah santri.</p>
                </div>
                <div class="mt-6 pt-3 border-t border-surface-100 flex items-center justify-between text-xs font-bold text-amber-700">
                    <span>Atur Tarif Biaya</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

        </div>
    </div>

    {{-- Tables Grid (Pemasukan & Tunggakan) --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        
        {{-- Transaksi Pembayaran Terbaru --}}
        <div class="bg-white rounded-3xl p-6 border border-surface-200 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-surface-900 text-base flex items-center gap-2">
                    <i data-lucide="history" class="w-5 h-5 text-teal-700"></i>
                    Transaksi Pembayaran Terbaru
                </h3>
            </div>

            <div class="divide-y divide-surface-100">
                @forelse($pembayaranTerbaru as $p)
                    <div class="py-3 flex items-center justify-between gap-3 hover:bg-surface-50 p-2 rounded-2xl transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 font-bold border border-emerald-100">
                                <i data-lucide="check" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-surface-900 leading-tight">{{ $p->tagihan->pesertaDidik->orang->nama_lengkap ?? '-' }}</h4>
                                <p class="text-[0.65rem] text-surface-450 font-mono mt-0.5">{{ $p->no_transaksi }} • {{ $p->tanggal_bayar->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-xs font-mono font-bold text-emerald-700 block">+ Rp {{ number_format($p->jumlah, 0, ',', '.') }}</span>
                            <span class="inline-block mt-0.5 px-2 py-0.5 rounded-md text-[9px] font-extrabold uppercase bg-surface-100 text-surface-700 border border-surface-200">{{ $p->metode }}</span>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-surface-500">
                        <i data-lucide="history" class="w-8 h-8 text-surface-300 mx-auto mb-2"></i>
                        <p class="text-xs">Belum ada transaksi pembayaran yang tercatat.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Tunggakan Jatuh Tempo Terdekat --}}
        <div class="bg-white rounded-3xl p-6 border border-surface-200 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-surface-900 text-base flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-danger-600"></i>
                    Jatuh Tempo Terdekat
                </h3>
            </div>

            <div class="divide-y divide-surface-100">
                @forelse($tunggakanJatuhTempo as $t)
                    @php
                        $isOverdue = $t->jatuh_tempo && $t->jatuh_tempo->isPast();
                        $sisa = $t->total - $t->pembayaran->sum('jumlah');
                    @endphp
                    <div class="py-3 flex items-center justify-between gap-3 hover:bg-surface-50 p-2 rounded-2xl transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 font-bold {{ $isOverdue ? 'bg-danger-50 text-danger-600 border border-danger-100' : 'bg-warning-50 text-warning-600 border border-warning-100' }}">
                                <i data-lucide="{{ $isOverdue ? 'alert-triangle' : 'calendar' }}" class="w-5 h-5"></i>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-sm font-bold text-surface-900 leading-tight truncate">{{ $t->pesertaDidik->orang->nama_lengkap ?? '-' }}</h4>
                                <p class="text-[0.65rem] text-surface-500 mt-0.5 truncate">{{ $t->komponenBiaya->nama }}</p>
                            </div>
                        </div>
                        <div class="text-right flex items-center gap-3 shrink-0">
                            <div>
                                <span class="text-xs font-mono font-bold text-danger-600 block">Rp {{ number_format($sisa, 0, ',', '.') }}</span>
                                <span class="text-[9px] text-surface-400 block">Sisa Tagihan</span>
                            </div>
                            <a href="{{ route('bendahara.tagihan.show', $t) }}" class="btn-primary py-1.5 px-3 rounded-xl text-xs font-bold flex items-center gap-1" style="color: #ffffff !important; background-color: #0f766e !important;">
                                <span style="color: #ffffff !important;">Bayar</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-surface-500">
                        <i data-lucide="check-circle" class="w-8 h-8 text-success-300 mx-auto mb-2"></i>
                        <p class="text-xs">Luar biasa! Tidak ada tagihan yang belum lunas.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
