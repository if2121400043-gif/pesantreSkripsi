@extends('layouts.app')

@section('title', 'Terima Pembayaran: INV-' . str_pad($tagihan->id, 6, '0', STR_PAD_LEFT))

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm text-surface-500 mb-2">
            <a href="{{ route('bendahara.tagihan.index') }}" class="hover:text-primary-600 transition-colors">Data Tagihan</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <a href="{{ route('bendahara.tagihan.show', $tagihan) }}" class="hover:text-primary-600 transition-colors">Invoice Detail</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-surface-900 font-medium">Terima Pembayaran</span>
        </div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">
            Terima Pembayaran: #INV-{{ str_pad($tagihan->id, 6, '0', STR_PAD_LEFT) }}
        </h1>
    </div>
    <a href="{{ route('bendahara.tagihan.show', $tagihan) }}" class="btn-secondary flex items-center gap-2">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Kembali ke Invoice</span>
    </a>
</div>
@endsection

@section('content')
<form action="{{ route('bendahara.pembayaran.store') }}" method="POST" class="max-w-4xl mx-auto space-y-6">
    @csrf
    <input type="hidden" name="tagihan_id" value="{{ $tagihan->id }}">
    
    {{-- Notifikasi Error --}}
    @if($errors->any())
        <div class="bg-danger-50 text-danger-700 p-4 rounded-xl border border-danger-200">
            <div class="flex gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
                <div>
                    <h3 class="font-semibold mb-1">Gagal memproses pembayaran:</h3>
                    <ul class="list-disc pl-5 space-y-1 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Kolom Kiri: Input Pembayaran --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Nominal Card --}}
            <x-card title="Input Nominal Transaksi">
                <div class="space-y-5">
                    {{-- Sisa Tagihan Box --}}
                    <div class="bg-gradient-to-br from-success-500 to-primary-600 p-5 rounded-2xl text-white shadow-sm flex justify-between items-center">
                        <div>
                            <span class="text-xs font-semibold text-success-100 uppercase tracking-wider block">Sisa Tagihan yang Harus Dibayar</span>
                            <span class="text-3xl font-extrabold font-mono tracking-tight">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</span>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                            <i data-lucide="wallet" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                    
                    {{-- Nominal Input --}}
                    <div class="space-y-2">
                        <label for="input-jumlah" class="block text-sm font-semibold text-surface-700">Nominal Bayar (Rp) <span class="text-danger-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-base font-bold text-surface-400">Rp</span>
                            <input type="number" name="jumlah" id="input-jumlah" required min="1" max="{{ $sisaTagihan }}" value="{{ $sisaTagihan }}" 
                                   class="w-full font-mono text-xl font-bold rounded-xl border border-surface-300 pl-11 pr-4 py-3 bg-surface-50 focus:bg-white text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all">
                        </div>
                        
                        {{-- Quick Fills --}}
                        <div class="flex gap-2 pt-1">
                            <button type="button" onclick="setNominal({{ $sisaTagihan }})" 
                                    class="px-3.5 py-2 text-xs font-bold rounded-xl bg-surface-100 hover:bg-primary-50 hover:text-primary-600 border border-surface-200 hover:border-primary-200 transition-all">
                                Bayar Lunas
                            </button>
                            @if($sisaTagihan > 1000)
                                <button type="button" onclick="setNominal({{ floor($sisaTagihan / 2) }})" 
                                        class="px-3.5 py-2 text-xs font-bold rounded-xl bg-surface-100 hover:bg-primary-50 hover:text-primary-600 border border-surface-200 hover:border-primary-200 transition-all">
                                    Bayar Setengah
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </x-card>

            {{-- Metode Pembayaran Card --}}
            <x-card title="Metode Pembayaran">
                <div class="space-y-4">
                    <input type="hidden" name="metode" id="input-metode" value="TUNAI">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <button type="button" onclick="selectMetode('TUNAI')" id="btn-metode-tunai" 
                                class="flex flex-col items-center justify-center p-4 border-2 rounded-2xl transition-all border-primary-500 bg-primary-50/40 text-primary-900 shadow-sm">
                            <i data-lucide="banknote" class="w-8 h-8 mb-2 text-primary-600"></i>
                            <span class="text-sm font-bold text-surface-800 block">Uang Tunai (Cash)</span>
                            <span class="text-[10px] text-surface-400 mt-1">Pembayaran langsung</span>
                        </button>
                        
                        <button type="button" onclick="selectMetode('TRANSFER')" id="btn-metode-transfer" 
                                class="flex flex-col items-center justify-center p-4 border-2 rounded-2xl transition-all border-surface-200 bg-white text-surface-500 hover:bg-surface-50">
                            <i data-lucide="landmark" class="w-8 h-8 mb-2 text-surface-400"></i>
                            <span class="text-sm font-bold text-surface-700 block">Transfer Bank</span>
                            <span class="text-[10px] text-surface-400 mt-1">Via ATM / Mobile Banking</span>
                        </button>
                        
                        <button type="button" onclick="selectMetode('QRIS')" id="btn-metode-qris" 
                                class="flex flex-col items-center justify-center p-4 border-2 rounded-2xl transition-all border-surface-200 bg-white text-surface-500 hover:bg-surface-50">
                            <i data-lucide="qr-code" class="w-8 h-8 mb-2 text-surface-400"></i>
                            <span class="text-sm font-bold text-surface-700 block">QRIS</span>
                            <span class="text-[10px] text-surface-400 mt-1">E-Wallet (Dana, OVO, dll)</span>
                        </button>
                    </div>

                    <div class="pt-4 border-t border-surface-100">
                        <label for="input-keterangan" class="block text-sm font-semibold text-surface-700 mb-1.5">Keterangan / Catatan Transaksi (Opsional)</label>
                        <input type="text" name="keterangan" id="input-keterangan" placeholder="Cth: Lunas via Bank Syariah Mandiri, titipan orang tua" 
                               class="w-full rounded-xl border border-surface-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors">
                    </div>
                </div>
            </x-card>
        </div>

        {{-- Kolom Kanan: Summary & Submit --}}
        <div class="lg:col-span-1 space-y-6">
            <x-card title="Ringkasan Invoice">
                <div class="space-y-4 text-sm">
                    <div>
                        <span class="text-xs text-surface-400 block font-semibold uppercase tracking-wider">Nama Santri</span>
                        <span class="font-bold text-surface-900">{{ $tagihan->pesertaDidik->orang->nama_lengkap }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-surface-400 block font-semibold uppercase tracking-wider">NIUP</span>
                        <span class="font-mono font-bold text-primary-700">{{ $tagihan->pesertaDidik->orang->niup }}</span>
                    </div>
                    
                    <div class="border-t border-surface-100 pt-3">
                        <span class="text-xs text-surface-400 block font-semibold uppercase tracking-wider">Komponen Biaya</span>
                        <span class="font-bold text-surface-900">{{ $tagihan->komponenBiaya->nama }}</span>
                        @if($tagihan->bulan)
                            <span class="text-xs text-surface-500 block mt-0.5">Bulan: {{ $tagihan->bulan }}</span>
                        @endif
                    </div>
                    
                    <div class="border-t border-surface-100 pt-3 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-surface-500">Total Tagihan:</span>
                            <span class="font-bold text-surface-900 font-mono">Rp {{ number_format($tagihan->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-surface-500">Total Terbayar:</span>
                            <span class="font-bold text-success-600 font-mono">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between border-t border-dashed border-surface-200 pt-2 text-base">
                            <span class="font-bold text-surface-900">Sisa Tagihan:</span>
                            <span class="font-bold text-danger-650 font-mono">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-surface-100">
                        <button type="submit" class="btn-primary w-full justify-center flex items-center gap-2 py-3 rounded-xl">
                            <i data-lucide="check-circle" class="w-5 h-5 text-white"></i>
                            <span class="text-white font-bold">Proses Pembayaran</span>
                        </button>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    function setNominal(val) {
        document.getElementById('input-jumlah').value = val;
    }

    function selectMetode(method) {
        document.getElementById('input-metode').value = method;
        
        const methods = ['TUNAI', 'TRANSFER', 'QRIS'];
        methods.forEach(m => {
            const btn = document.getElementById(`btn-metode-${m.toLowerCase()}`);
            const icon = btn.querySelector('i');
            const text = btn.querySelector('span:first-of-type');
            
            if (m === method) {
                btn.className = "flex flex-col items-center justify-center p-4 border-2 rounded-2xl transition-all border-primary-500 bg-primary-50/40 text-primary-900 shadow-sm";
                icon.className = "w-8 h-8 mb-2 text-primary-600";
                text.className = "text-sm font-bold text-surface-800 block";
            } else {
                btn.className = "flex flex-col items-center justify-center p-4 border-2 rounded-2xl transition-all border-surface-200 bg-white text-surface-500 hover:bg-surface-50";
                icon.className = "w-8 h-8 mb-2 text-surface-400";
                text.className = "text-sm font-bold text-surface-700 block";
            }
        });
    }
</script>
@endpush
