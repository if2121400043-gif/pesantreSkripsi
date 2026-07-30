@extends('layouts.app')

@section('title', 'Detail Tagihan: INV-' . str_pad($tagihan->id, 6, '0', STR_PAD_LEFT))

@php
    $pesantren = \App\Models\Pesantren::first();
@endphp

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 print:hidden">
    <div>
        <div class="flex items-center gap-2 text-sm text-surface-500 mb-2">
            <a href="{{ route('bendahara.tagihan.index') }}" class="hover:text-primary-600 transition-colors">Data Tagihan</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-surface-900 font-medium">Invoice Detail</span>
        </div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">
            Invoice #INV-{{ str_pad($tagihan->id, 6, '0', STR_PAD_LEFT) }}
        </h1>
    </div>
    <div class="flex gap-3">
        <button onclick="window.print()" class="btn-secondary flex items-center gap-2">
            <i data-lucide="printer" class="w-4 h-4"></i>
            <span>Cetak Invoice</span>
        </button>
        @if($tagihan->status !== 'LUNAS')
            <a href="{{ route('bendahara.tagihan.bayar', $tagihan) }}" class="btn-primary flex items-center gap-2">
                <i data-lucide="banknote" class="w-4 h-4 text-white"></i>
                <span class="text-white font-bold">Terima Pembayaran</span>
            </a>
        @endif
    </div>
</div>
@endsection

@section('content')
<style>
    @media print {
        /* Sembunyikan sidebar utama, topbar navigasi, dan tombol aksi */
        #sidebar, #topbar, .print\:hidden, .no-print {
            display: none !important;
        }
        /* Reset padding & margin body/container agar bersih di kertas */
        body, main, #main-content {
            padding: 0 !important;
            margin: 0 !important;
            background: white !important;
        }
        /* Paksa kolom invoice memenuhi 100% lebar kertas print */
        .lg\:col-span-2 {
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 100% !important;
            grid-column: span 3 / span 3 !important;
        }
        /* Sembunyikan kolom riwayat pembayaran */
        .lg\:col-span-1 {
            display: none !important;
        }
        /* Hilangkan bayangan & border card agar terlihat bersih di kertas */
        .shadow-sm, .border-surface-200 {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    {{-- Main Invoice --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-surface-200 print:border-none print:shadow-none overflow-hidden">
            
            {{-- Header Block (Corporate Nurul Furqon Dark Green Theme) --}}
            <div class="bg-gradient-to-r from-primary-900 to-primary-950 text-white p-8 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/5 rounded-full blur-2xl"></div>
                <div class="absolute left-10 top-10 w-32 h-32 bg-accent-500/5 rounded-full blur-xl"></div>
                
                <div class="flex flex-col md:flex-row justify-between items-start gap-6 relative z-10">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            @if($pesantren && $pesantren->logo)
                                <img src="{{ Storage::url($pesantren->logo) }}" alt="Logo {{ $pesantren->nama }}" class="w-14 h-14 object-contain flex-shrink-0">
                            @else
                                <picture>
                                    <source srcset="{{ asset('images/logo-pesantren-256.webp') }}" type="image/webp">
                                    <img src="{{ asset('images/logo-pesantren-256.webp') }}" alt="Logo {{ $pesantren->nama ?? 'Pesantren Nurul Furqon' }}" class="w-14 h-14 object-contain flex-shrink-0">
                                </picture>
                            @endif
                            <div>
                                <span class="text-[10px] font-bold text-accent-300 uppercase tracking-widest block">Yayasan Nurul Furqon</span>
                                <h1 class="text-lg font-extrabold font-heading tracking-tight leading-none text-white">{{ $pesantren->nama ?? 'PONDOK PESANTREN NURUL FURQON' }}</h1>
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-white/10 space-y-1">
                            <h4 class="text-xs font-bold text-accent-300 uppercase tracking-wider">Rincian Kontak Pesantren</h4>
                            <p class="text-xs text-primary-250 flex items-center gap-1.5">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-accent-400"></i>
                                <span>{{ $pesantren->alamat ?? 'Jl. Raya Pesantren No. 1, Kepuhkembeng, Jombang' }}</span>
                            </p>
                            @if($pesantren->telepon || $pesantren->email)
                                <p class="text-xs text-primary-250 flex items-center gap-3">
                                    @if($pesantren->telepon)
                                        <span class="flex items-center gap-1"><i data-lucide="phone" class="w-3.5 h-3.5 text-accent-400"></i> {{ $pesantren->telepon }}</span>
                                    @endif
                                    @if($pesantren->email)
                                        <span class="flex items-center gap-1"><i data-lucide="mail" class="w-3.5 h-3.5 text-accent-400"></i> {{ $pesantren->email }}</span>
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="text-left md:text-right flex flex-col justify-between h-full md:items-end self-stretch">
                        <div>
                            <h2 class="text-4xl font-black font-heading tracking-wider text-white">INVOICE</h2>
                            <p class="text-xs font-mono font-bold text-accent-300 mt-1">NO: INV-{{ str_pad($tagihan->id, 6, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        
                        <div class="mt-6 md:mt-auto pt-4 md:pt-0 space-y-1 text-xs">
                            <div class="flex md:justify-end gap-2 text-primary-200">
                                <span class="font-medium">Tanggal Tagihan:</span>
                                <span class="font-bold text-white">{{ $tagihan->created_at->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex md:justify-end gap-2 text-primary-200">
                                <span class="font-medium">Tanggal Jatuh Tempo:</span>
                                <span class="font-bold text-accent-300">{{ $tagihan->jatuh_tempo ? $tagihan->jatuh_tempo->format('d/m/Y') : '-' }}</span>
                            </div>
                            <div class="flex md:justify-end gap-2 mt-2">
                                <span class="font-medium text-primary-200">Status:</span>
                                @if($tagihan->status === 'LUNAS')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-success-500 text-white uppercase tracking-wider">LUNAS</span>
                                @elseif($tagihan->status === 'SEBAGIAN')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-warning-500 text-white uppercase tracking-wider">SEBAGIAN</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-danger-500 text-white uppercase tracking-wider">BELUM LUNAS</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Accent Divider Line (Gold) --}}
            <div class="h-1.5 bg-accent-500"></div>
            
            {{-- Customer & Summary Banner --}}
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6 border-b border-surface-100 bg-surface-50/50">
                <div>
                    <span class="text-xs font-bold text-surface-400 uppercase tracking-wider block mb-2">Ditagihkan Kepada:</span>
                    <h3 class="text-lg font-extrabold text-surface-900 font-heading leading-tight">{{ $tagihan->pesertaDidik->orang->nama_lengkap }}</h3>
                    <p class="text-sm text-surface-600 mt-1 flex items-center gap-1.5">
                        <span class="font-semibold text-surface-700">NIUP:</span>
                        <span class="font-mono text-primary-700 font-bold">{{ $tagihan->pesertaDidik->orang->niup }}</span>
                    </p>
                    @if($tagihan->pesertaDidik->riwayatRombel->first())
                        <p class="text-xs text-surface-500 mt-1">Kelas: {{ $tagihan->pesertaDidik->riwayatRombel->first()->rombel->nama }}</p>
                    @endif
                    <p class="text-xs text-surface-500 mt-0.5">Alamat: {{ $tagihan->pesertaDidik->orang->alamat_lengkap ?? '-' }}</p>
                </div>
                
                <div class="flex flex-col md:items-end justify-center">
                    <span class="text-xs font-bold text-surface-400 uppercase tracking-wider block mb-1">Jumlah yang Harus Dibayar:</span>
                    <span class="text-3xl font-black text-primary-900 font-mono tracking-tight block text-right">
                        Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                    </span>
                    <span class="text-[10px] text-surface-450 mt-1 font-semibold text-right block">Tunggakan bersih setelah diskon dan cicilan</span>
                </div>
            </div>
            
            {{-- Table Block --}}
            <div class="p-8">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-primary-900 text-white uppercase tracking-wider text-[11px] font-bold">
                                <th class="px-5 py-3 rounded-l-xl">Keterangan Tagihan</th>
                                <th class="px-5 py-3 text-right">Harga Unit</th>
                                <th class="px-5 py-3 text-center">Kuantitas</th>
                                <th class="px-5 py-3 text-right rounded-r-xl">Total (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-100 text-surface-750">
                            <tr class="hover:bg-surface-50/30">
                                <td class="px-5 py-5">
                                    <p class="font-extrabold text-surface-900">{{ $tagihan->komponenBiaya->nama }}</p>
                                    <p class="text-[11px] text-surface-500 mt-0.5">
                                        Jenis: {{ $tagihan->komponenBiaya->jenis }} 
                                        @if($tagihan->bulan) • Bulan: {{ $tagihan->bulan }} @endif
                                        • T.A {{ $tagihan->tahunPelajaran->nama }}
                                    </p>
                                </td>
                                <td class="px-5 py-5 text-right font-mono text-surface-800">
                                    Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-5 text-center font-bold text-surface-700">1</td>
                                <td class="px-5 py-5 text-right font-mono font-bold text-surface-900">
                                    Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                            @if($tagihan->diskon > 0)
                                <tr class="bg-success-50/20">
                                    <td class="px-5 py-3 font-semibold text-success-800">
                                        Diskon / Potongan Khusus
                                    </td>
                                    <td class="px-5 py-3 text-right text-success-700 font-mono">-</td>
                                    <td class="px-5 py-3 text-center text-success-700 font-mono">-</td>
                                    <td class="px-5 py-3 text-right font-mono font-bold text-success-700">
                                        - Rp {{ number_format($tagihan->diskon, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- Bottom Summary Section --}}
            <div class="px-8 pb-8 grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                {{-- Left: Payment Info --}}
                <div class="space-y-4">
                    <div>
                        <h4 class="text-xs font-bold text-surface-450 uppercase tracking-wider mb-2">Metode Pembayaran Transfer</h4>
                        <div class="bg-surface-50 p-4 rounded-2xl border border-surface-200 space-y-1.5 text-xs text-surface-650 font-sans">
                            <p class="font-bold text-surface-800">BANK SYARIAH INDONESIA (BSI)</p>
                            <p class="font-mono flex justify-between"><span>No. Rekening:</span> <span class="font-bold text-primary-900 font-mono">711-2233-445</span></p>
                            <p class="flex justify-between"><span>Atas Nama:</span> <span class="font-bold text-surface-800">Yayasan Nurul Furqon</span></p>
                            <p class="flex justify-between"><span>Konfirmasi WA:</span> <span class="font-bold text-surface-800 font-mono">0823-3456-7890</span></p>
                        </div>
                    </div>
                    
                    {{-- Signature --}}
                    <div class="pt-2">
                        <div class="h-12 flex items-end">
                            <span class="font-heading font-semibold text-lg italic text-primary-800 tracking-wide">Ach. Zulfi Zibyan</span>
                        </div>
                        <div class="w-48 border-t border-surface-300 my-1"></div>
                        <p class="text-[10px] font-bold text-surface-400 uppercase tracking-wider">Bendahara Pesantren</p>
                    </div>
                </div>
                
                {{-- Right: Financial Summary --}}
                <div class="space-y-4 flex flex-col items-end">
                    <div class="w-full max-w-sm rounded-2xl border border-surface-200 overflow-hidden shadow-sm">
                        <div class="bg-surface-50 p-4 space-y-2.5 text-xs">
                            <div class="flex justify-between text-surface-600">
                                <span>Sub Total Tagihan:</span>
                                <span class="font-mono font-bold">Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</span>
                            </div>
                            @if($tagihan->diskon > 0)
                                <div class="flex justify-between text-success-700">
                                    <span>Potongan (Diskon):</span>
                                    <span class="font-mono font-bold">- Rp {{ number_format($tagihan->diskon, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-surface-600 border-t border-dashed border-surface-200 pt-2.5">
                                <span>Total Bersih:</span>
                                <span class="font-mono font-bold text-surface-900">Rp {{ number_format($tagihan->total, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-success-750 font-semibold">
                                <span>Total Telah Dibayar:</span>
                                <span class="font-mono text-success-700 font-bold">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="bg-primary-900 text-white p-4 flex justify-between items-center text-sm font-bold">
                            <span>SISA TAGIHAN:</span>
                            <span class="font-mono text-base text-accent-300">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <h4 class="text-xs font-bold text-surface-800 uppercase tracking-widest">TERIMA KASIH</h4>
                        <p class="text-[10px] text-surface-450 italic mt-0.5 font-medium">Pembayaran sah jika disertai stempel pesantren & tanda tangan bendahara.</p>
                    </div>
                </div>
            </div>
            
            {{-- Solid Bottom Accent Bar --}}
            <div class="h-4 bg-primary-900 border-t border-accent-400"></div>
            
        </div>
    </div>
    
    {{-- Payment History Sidebar --}}
    <div class="lg:col-span-1 space-y-6 print:hidden">
        <x-card title="Riwayat Pembayaran" class="border-t-4 border-t-success-500">
            @if($pembayarans->isEmpty())
                <div class="py-8 text-center text-surface-500 border-2 border-dashed border-surface-200 rounded-xl">
                    <i data-lucide="history" class="w-8 h-8 text-surface-300 mx-auto mb-2"></i>
                    <p class="text-sm">Belum ada riwayat pembayaran.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($pembayarans as $pembayaran)
                        <div class="p-3 border border-surface-200 rounded-lg hover:bg-surface-50 transition-colors">
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-xs font-mono font-bold text-primary-600">{{ $pembayaran->no_transaksi }}</span>
                                <span class="text-[0.65rem] text-surface-500">{{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/y H:i') }}</span>
                            </div>
                            <div class="flex justify-between items-center mt-2">
                                <div>
                                    <span class="inline-block px-2 py-0.5 rounded text-[0.6rem] font-bold bg-surface-200 text-surface-700 uppercase">{{ $pembayaran->metode }}</span>
                                </div>
                                <span class="font-mono font-bold text-success-600">+ Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</span>
                            </div>
                            <div class="mt-2 flex justify-between items-center text-xs text-surface-500 border-t border-surface-100 pt-2">
                                <span>Kasir: {{ $pembayaran->kasir->orang->nama_lengkap ?? $pembayaran->kasir->username ?? '-' }}</span>
                                <div class="flex gap-3 items-center">
                                    <a href="{{ route('bendahara.pembayaran.kuitansi', $pembayaran) }}" target="_blank" class="text-primary-600 hover:text-primary-800 flex items-center gap-1" title="Cetak Kuitansi">
                                        <i data-lucide="printer" class="w-3.5 h-3.5"></i> Cetak
                                    </a>
                                    <form action="{{ route('bendahara.pembayaran.destroy', $pembayaran) }}" method="POST" onsubmit="return confirm('Batalkan transaksi ini? Sisa tagihan akan dikalkulasi ulang.');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-danger-500 hover:text-danger-700" title="Batalkan Transaksi"><i data-lucide="x-circle" class="w-4 h-4"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            
            {{-- Action Buttons --}}
            <div class="mt-6 space-y-3 pt-4 border-t border-surface-100">
                @if($tagihan->status !== 'LUNAS')
                    <a href="{{ route('bendahara.tagihan.bayar', $tagihan) }}" class="w-full btn-primary flex items-center justify-center gap-2 py-2.5">
                        <i data-lucide="banknote" class="w-4 h-4 text-white"></i> <span class="text-white font-bold">Terima Pembayaran</span>
                    </a>

                    {{-- Tombol Kirim Pengingat WA --}}
                    <form action="{{ route('bendahara.tagihan.wa-reminder', $tagihan) }}" method="POST"
                          onsubmit="return confirm('Kirim pengingat tagihan via WhatsApp ke wali santri?');">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-lg text-sm font-medium border-2 border-green-500 text-green-700 bg-green-50 hover:bg-green-100 transition-colors">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            Kirim Pengingat WA
                        </button>
                    </form>
                @endif
                
                @if($tagihan->status === 'BELUM_BAYAR')
                    <form action="{{ route('bendahara.tagihan.destroy', $tagihan) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan (menghapus) invoice tagihan ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full btn-secondary text-danger-600 border-danger-200 hover:bg-danger-50 flex items-center justify-center gap-2">
                            <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus Tagihan
                        </button>
                    </form>
                @endif
            </div>
        </x-card>
    </div>
</div>

@endsection
