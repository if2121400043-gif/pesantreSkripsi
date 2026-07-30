@extends('layouts.app')

@section('title', 'Pembayaran Online')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('portal.beranda', ['tab' => 'tagihan']) }}" class="text-surface-400 hover:text-surface-600 transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <h1 class="text-2xl font-bold text-surface-900 font-heading">Pembayaran Online</h1>
        </div>
        <p class="text-sm text-surface-500 mt-1">Bayar tagihan secara online melalui berbagai metode pembayaran.</p>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Detail Tagihan Card --}}
    <x-card>
        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-4 border-b border-surface-100">
                <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="receipt" class="w-5 h-5 text-primary-600"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-surface-900">Detail Tagihan</h2>
                    <p class="text-xs text-surface-500">Informasi tagihan yang akan dibayar</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Nama Santri</p>
                    <p class="text-sm font-bold text-surface-900 mt-1">{{ $tagihan->pesertaDidik->orang->nama_lengkap ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Komponen Biaya</p>
                    <p class="text-sm font-bold text-surface-900 mt-1">{{ $tagihan->komponenBiaya->nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Periode</p>
                    <p class="text-sm font-medium text-surface-700 mt-1">{{ $tagihan->bulan ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Jatuh Tempo</p>
                    <p class="text-sm font-medium text-surface-700 mt-1">
                        {{ $tagihan->jatuh_tempo ? $tagihan->jatuh_tempo->isoFormat('D MMMM YYYY') : '-' }}
                    </p>
                </div>
            </div>

            <div class="bg-surface-50 rounded-xl p-4 space-y-2">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-surface-500">Total Tagihan</span>
                    <span class="font-medium text-surface-700">Rp {{ number_format($tagihan->total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-surface-500">Sudah Dibayar</span>
                    <span class="font-medium text-success-600">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</span>
                </div>
                <div class="border-t border-surface-200 pt-2 mt-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-bold text-surface-900">Sisa yang Harus Dibayar</span>
                        <span class="text-xl font-extrabold text-danger-600">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </x-card>

    {{-- Metode Pembayaran Card --}}
    <x-card>
        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-4 border-b border-surface-100">
                <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="credit-card" class="w-5 h-5 text-emerald-600"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-surface-900">Metode Pembayaran</h2>
                    <p class="text-xs text-surface-500">Pilih metode pembayaran yang tersedia setelah klik tombol bayar</p>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-surface-50 rounded-lg p-3 text-center border border-surface-100">
                    <i data-lucide="building" class="w-6 h-6 mx-auto text-blue-500 mb-1.5"></i>
                    <p class="text-xs font-medium text-surface-600">Transfer Bank</p>
                </div>
                <div class="bg-surface-50 rounded-lg p-3 text-center border border-surface-100">
                    <i data-lucide="smartphone" class="w-6 h-6 mx-auto text-green-500 mb-1.5"></i>
                    <p class="text-xs font-medium text-surface-600">E-Wallet</p>
                </div>
            </div>

            {{-- Tombol Bayar --}}
            <button
                id="btn-bayar"
                onclick="payWithSnap()"
                class="w-full py-3.5 px-6 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-200 transition-all duration-200 flex items-center justify-center gap-2 text-base disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <i data-lucide="shield-check" class="w-5 h-5"></i>
                <span id="btn-bayar-text">Bayar Sekarang — Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</span>
            </button>

            <p class="text-xs text-center text-surface-400">
                <i data-lucide="lock" class="w-3 h-3 inline-block mr-1"></i>
                Pembayaran diproses secara aman melalui Midtrans
            </p>
        </div>
    </x-card>

    {{-- Status Info --}}
    <div id="payment-status" class="hidden">
        <x-card>
            <div id="status-content" class="text-center py-6 space-y-3">
                {{-- Diisi secara dinamis oleh JavaScript --}}
            </div>
        </x-card>
    </div>

</div>
@endsection

@push('scripts')
{{-- Midtrans Snap.js --}}
<script src="{{ config('midtrans.snap_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
    let isProcessing = false;

    async function payWithSnap() {
        if (isProcessing) return;
        isProcessing = true;

        const btn = document.getElementById('btn-bayar');
        const btnText = document.getElementById('btn-bayar-text');
        const originalText = btnText.textContent;

        btn.disabled = true;
        btnText.textContent = 'Memproses...';

        try {
            // Request snap token dari server
            const response = await fetch('{{ route("portal.payment.snap-token", $tagihan) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || 'Gagal mendapatkan token pembayaran.');
            }

            const data = await response.json();

            // Buka popup Midtrans Snap
            snap.pay(data.snap_token, {
                onSuccess: function(result) {
                    showStatus('success', 'Pembayaran Berhasil!', 'Terima kasih, pembayaran Anda telah berhasil diproses. Status tagihan akan diperbarui secara otomatis.');
                    btn.style.display = 'none';

                    fetch('{{ route("portal.payment.check-status", $tagihan) }}?order_id=' + encodeURIComponent(result.order_id || ''), {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                    }).catch(() => {});

                    setTimeout(() => {
                        window.location.replace('{{ route("portal.beranda", ["tab" => "tagihan"]) }}?t=' + Date.now());
                    }, 1500);
                },
                onPending: function(result) {
                    showStatus('pending', 'Menunggu Pembayaran', 'Silakan selesaikan pembayaran Anda sesuai instruksi yang diberikan. Status akan diperbarui otomatis setelah pembayaran dikonfirmasi.');
                    btn.disabled = false;
                    btnText.textContent = originalText;
                    isProcessing = false;
                },
                onError: function(result) {
                    showStatus('error', 'Pembayaran Gagal', 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.');
                    btn.disabled = false;
                    btnText.textContent = originalText;
                    isProcessing = false;
                },
                onClose: function() {
                    // User menutup popup tanpa menyelesaikan
                    btn.disabled = false;
                    btnText.textContent = originalText;
                    isProcessing = false;
                }
            });

        } catch (error) {
            alert(error.message || 'Terjadi kesalahan. Silakan coba lagi.');
            btn.disabled = false;
            btnText.textContent = originalText;
            isProcessing = false;
        }
    }

    function showStatus(type, title, message) {
        const statusDiv = document.getElementById('payment-status');
        const contentDiv = document.getElementById('status-content');

        const icons = {
            success: { name: 'check-circle', color: 'text-success-500', bg: 'bg-success-50' },
            pending: { name: 'clock', color: 'text-warning-500', bg: 'bg-warning-50' },
            error: { name: 'x-circle', color: 'text-danger-500', bg: 'bg-danger-50' },
        };

        const icon = icons[type];

        contentDiv.innerHTML = `
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full ${icon.bg} ${icon.color} mb-2">
                <i data-lucide="${icon.name}" class="w-8 h-8"></i>
            </div>
            <h3 class="text-lg font-bold text-surface-900">${title}</h3>
            <p class="text-sm text-surface-500 max-w-md mx-auto">${message}</p>
            <a href="{{ route('portal.beranda', ['tab' => 'tagihan']) }}" class="inline-flex items-center gap-2 mt-3 text-sm font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali ke Tagihan
            </a>
        `;

        statusDiv.classList.remove('hidden');

        // Re-initialize Lucide icons for the newly added elements
        if (window.lucide) {
            lucide.createIcons();
        }
    }
</script>
@endpush
