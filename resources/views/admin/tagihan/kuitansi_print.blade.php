<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi Pembayaran - {{ $pembayaran->no_transaksi }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                background: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: A5 landscape;
                margin: 0;
            }
        }
        body {
            background-color: #f3f4f6;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }
        .receipt-container {
            width: 100%;
            max-width: 21cm; /* A4 width or A5 landscape width */
            min-height: 14.8cm; /* A5 height */
            margin: 0 auto;
            background: white;
            padding: 2rem;
            position: relative;
        }
        @media screen {
            .receipt-container {
                margin-top: 2rem;
                margin-bottom: 2rem;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            }
        }
    </style>
</head>
<body>

    <div class="fixed top-4 right-4 no-print flex gap-2">
        <a href="{{ url()->previous() }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg shadow hover:bg-gray-600 transition-colors flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            Kembali
        </a>
        <button onclick="window.print()" class="px-4 py-2 bg-emerald-600 text-white rounded-lg shadow hover:bg-emerald-700 transition-colors flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Cetak Kuitansi
        </button>
    </div>

    <div class="receipt-container border-t-8 border-emerald-600">
        
        <!-- Header -->
        <div class="flex justify-between items-start border-b-2 border-emerald-100 pb-6 mb-6">
            <div class="flex items-center gap-4">
                @if($pesantren && $pesantren->logo)
                    <img src="{{ Storage::url($pesantren->logo) }}" alt="Logo" class="w-16 h-16 object-contain">
                @else
                    <picture>
                        <source srcset="{{ asset('images/logo-pesantren-256.webp') }}" type="image/webp">
                        <img src="{{ asset('images/logo-pesantren-256.webp') }}" alt="Logo" class="w-16 h-16 object-contain">
                    </picture>
                @endif
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $pesantren->nama ?? 'Pondok Pesantren Nurul Furqon' }}</h1>
                    <p class="text-sm text-gray-600 mt-1 max-w-md">{{ $pesantren->alamat ?? 'Jl. Raya Pesantren No. 1, Kab. Contoh, Jawa Timur' }}</p>
                    @if($pesantren->telepon || $pesantren->email)
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $pesantren->telepon ? 'Telp: ' . $pesantren->telepon : '' }} 
                        {{ $pesantren->telepon && $pesantren->email ? '|' : '' }}
                        {{ $pesantren->email ? 'Email: ' . $pesantren->email : '' }}
                    </p>
                    @endif
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-3xl font-bold text-emerald-600 tracking-tight">KUITANSI</h2>
                <p class="text-sm font-medium text-gray-500 mt-1">No: {{ $pembayaran->no_transaksi }}</p>
            </div>
        </div>

        <!-- Meta Info -->
        <div class="flex justify-between mb-8">
            <div class="space-y-1">
                <p class="text-sm text-gray-500">Telah Terima Dari:</p>
                <p class="text-lg font-bold text-gray-900">{{ $pembayaran->tagihan->pesertaDidik->orang->nama }}</p>
                <p class="text-sm text-gray-600">NIS: {{ $pembayaran->tagihan->pesertaDidik->nis ?? '-' }}</p>
            </div>
            <div class="space-y-1 text-right">
                <p class="text-sm text-gray-500">Tanggal Pembayaran:</p>
                <p class="text-base font-semibold text-gray-900">{{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->translatedFormat('d F Y H:i') }}</p>
                <p class="text-sm text-gray-500 mt-2">Metode Pembayaran:</p>
                <p class="text-base font-semibold text-gray-900">{{ $pembayaran->metode }}</p>
            </div>
        </div>

        <!-- Detail Pembayaran -->
        <div class="bg-gray-50 rounded-xl p-6 mb-8 border border-gray-100">
            <p class="text-sm text-gray-500 mb-2">Untuk Pembayaran:</p>
            <p class="text-xl font-semibold text-gray-900 mb-1">
                {{ $pembayaran->tagihan->komponenBiaya->nama }} - {{ $pembayaran->tagihan->bulan }}
            </p>
            @if($pembayaran->keterangan)
            <p class="text-sm text-gray-600 italic">Catatan: {{ $pembayaran->keterangan }}</p>
            @endif
        </div>

        <!-- Nominal Box -->
        <div class="flex justify-between items-end border-b-2 border-emerald-100 pb-6 mb-6">
            <div>
                <p class="text-sm text-gray-500 italic mb-1">Terbilang:</p>
                <div class="bg-emerald-50 px-4 py-2 rounded-lg border border-emerald-100 inline-block">
                    <p class="font-medium text-emerald-800 capitalize">
                        # {{ terbilang($pembayaran->jumlah) }} Rupiah #
                    </p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500 mb-1">Jumlah Uang:</p>
                <p class="text-4xl font-bold text-gray-900 tracking-tight">
                    Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <!-- Signatures -->
        <div class="flex justify-between mt-12 pt-4">
            <div class="text-center w-48">
                <p class="text-sm text-gray-500 mb-16">Penyetor,</p>
                <div class="border-b border-gray-400 pb-1">
                    <p class="font-semibold text-gray-900">( ........................................ )</p>
                </div>
            </div>
            <div class="text-center w-48">
                <p class="text-sm text-gray-500 mb-16">Kasir / Penerima,</p>
                <div class="border-b border-gray-400 pb-1">
                    <p class="font-semibold text-gray-900">{{ $pembayaran->kasir->name ?? 'Admin' }}</p>
                </div>
            </div>
        </div>

        <!-- Footer Note -->
        <div class="mt-8 text-center text-xs text-gray-400 italic">
            * Kuitansi ini adalah bukti pembayaran yang sah. Harap disimpan dengan baik.<br>
            Dicetak oleh sistem pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i:s') }}
        </div>

    </div>

    @php
    function terbilang($angka) {
        $angka = abs($angka);
        $baca = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $terbilang = "";
        
        if ($angka < 12) {
            $terbilang = " " . $baca[$angka];
        } else if ($angka < 20) {
            $terbilang = terbilang($angka - 10) . " belas";
        } else if ($angka < 100) {
            $terbilang = terbilang($angka / 10) . " puluh" . terbilang($angka % 10);
        } else if ($angka < 200) {
            $terbilang = " seratus" . terbilang($angka - 100);
        } else if ($angka < 1000) {
            $terbilang = terbilang($angka / 100) . " ratus" . terbilang($angka % 100);
        } else if ($angka < 2000) {
            $terbilang = " seribu" . terbilang($angka - 1000);
        } else if ($angka < 1000000) {
            $terbilang = terbilang($angka / 1000) . " ribu" . terbilang($angka % 1000);
        } else if ($angka < 1000000000) {
            $terbilang = terbilang($angka / 1000000) . " juta" . terbilang($angka % 1000000);
        }
        
        return $terbilang;
    }
    @endphp

</body>
</html>
