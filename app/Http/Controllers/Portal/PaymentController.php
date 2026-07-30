<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\HubunganKeluarga;
use App\Models\PesertaDidik;
use App\Services\MidtransService;
use App\Jobs\SendWhatsAppMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private function getAnakPesertaDidikIds(): array
    {
        $user = auth()->user();
        $orangId = $user->orang_id;

        $anakOrangIds = HubunganKeluarga::where('keluarga_id', $orangId)
            ->pluck('orang_id');

        return PesertaDidik::whereIn('orang_id', $anakOrangIds)
            ->where('status', 'AKTIF')
            ->pluck('id')
            ->toArray();
    }

    public function show(Tagihan $tagihan)
    {
        $anakIds = $this->getAnakPesertaDidikIds();
        if (!in_array($tagihan->peserta_didik_id, $anakIds)) {
            abort(403, 'Anda tidak memiliki akses ke tagihan ini.');
        }

        $tagihan->load(['pesertaDidik.orang', 'komponenBiaya', 'pembayaran']);

        $totalDibayar = $tagihan->pembayaran()
            ->where(function ($q) {
                $q->whereNull('midtrans_status')->orWhereIn('midtrans_status', ['settlement', 'capture']);
            })->sum('jumlah');

        $sisaTagihan = max(0, $tagihan->total - $totalDibayar);

        if ($sisaTagihan <= 0) {
            return redirect()->route('portal.beranda', ['tab' => 'tagihan'])
                ->with('success', 'Tagihan ini sudah lunas.');
        }

        return view('portal.payment', compact('tagihan', 'sisaTagihan', 'totalDibayar'));
    }

    public function getSnapToken(Request $request, Tagihan $tagihan)
    {
        $anakIds = $this->getAnakPesertaDidikIds();
        if (!in_array($tagihan->peserta_didik_id, $anakIds)) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $totalDibayar = $tagihan->pembayaran()
            ->where(function ($q) {
                $q->whereNull('midtrans_status')->orWhereIn('midtrans_status', ['settlement', 'capture']);
            })->sum('jumlah');
            
        $sisaTagihan = (int) max(0, $tagihan->total - $totalDibayar);

        if ($sisaTagihan <= 0) {
            return response()->json(['error' => 'Tagihan sudah lunas.'], 400);
        }

        try {
            $midtrans = new MidtransService();
            $result = $midtrans->createSnapToken($tagihan, $sisaTagihan);

            return response()->json([
                'snap_token' => $result['snap_token'],
                'order_id' => $result['order_id'],
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Token Error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membuat transaksi pembayaran. Silakan coba lagi.'], 500);
        }
    }

    private function processSuccessfulTransaction(Tagihan $tagihan, string $orderId, array $payload, ?string $transactionStatus = null, ?string $transactionId = null, ?string $paymentType = null, $grossAmount = null, ?Pembayaran $existingPayment = null): void
    {
        $normalizedAmount = $this->normalizeAmount($payload, $grossAmount);

        if ($existingPayment) {
            $existingPayment->update([
                'jumlah' => $normalizedAmount,
                'midtrans_status' => $transactionStatus ?? 'settlement',
                'midtrans_response' => $payload,
            ]);
            $tagihan->refreshPaymentStatus();
            return;
        }

        $metodePembayaran = $this->mapMidtransMethod($paymentType);
        $pembayaran = Pembayaran::create([
            'tagihan_id' => $tagihan->id,
            'no_transaksi' => 'MID-' . date('Ymd') . '-' . strtoupper(substr(md5($orderId), 0, 6)),
            'jumlah' => $normalizedAmount,
            'metode' => $metodePembayaran,
            'midtrans_order_id' => $orderId,
            'midtrans_transaction_id' => $transactionId,
            'midtrans_payment_type' => $paymentType,
            'midtrans_status' => $transactionStatus ?? 'settlement',
            'midtrans_response' => $payload,
            'tanggal_bayar' => now(),
        ]);

        $newSisa = $tagihan->refreshPaymentStatus();

        try {
            $tagihan->load(['pesertaDidik.orang', 'komponenBiaya']);
            $peserta = $tagihan->pesertaDidik;
            $phone = $peserta?->getWaliPhone();

            if ($phone) {
                SendWhatsAppMessage::dispatch('pembayaran_sukses', $phone, [
                    'santri_nama' => $peserta->orang->nama_lengkap,
                    'komponen_nama' => $tagihan->komponenBiaya->nama,
                    'jumlah_bayar' => $normalizedAmount,
                    'sisa_tagihan' => $newSisa,
                    'no_transaksi' => $pembayaran->no_transaksi,
                ]);
            }
        } catch (\Exception $waEx) {
            Log::warning('WA notif pembayaran Midtrans gagal: ' . $waEx->getMessage());
        }
    }

    private function normalizeAmount(array $payload, $fallback = null): float
    {
        $candidates = [
            $payload['gross_amount'] ?? null,
            $payload['amount'] ?? null,
            $payload['transaction_details']['gross_amount'] ?? null,
            $fallback,
        ];

        foreach ($candidates as $candidate) {
            if ($candidate === null || $candidate === '') {
                continue;
            }

            if (is_string($candidate)) {
                $candidate = trim($candidate);
                if (is_numeric($candidate)) {
                    return (float) $candidate;
                }
            } elseif (is_numeric($candidate)) {
                return (float) $candidate;
            }
        }

        return 0.0;
    }

    private function mapMidtransMethod(?string $paymentType): string
    {
        if ($paymentType === 'qris') {
            return 'QRIS';
        }
        if (in_array($paymentType, ['bank_transfer', 'echannel', 'permata_va', 'bca_va', 'bri_va', 'bni_va', 'credit_card', 'gopay', 'shopeepay', 'ovo', 'dana', 'linkaja'])) {
            return 'TRANSFER';
        }
        return 'LAINNYA';
    }

    public function checkStatus(Request $request, Tagihan $tagihan)
    {
        $anakIds = $this->getAnakPesertaDidikIds();
        if (!in_array($tagihan->peserta_didik_id, $anakIds)) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $orderId = $request->query('order_id');
        if (empty($orderId)) {
            return response()->json(['error' => 'order_id tidak ditemukan.'], 400);
        }

        try {
            $midtrans = new MidtransService();
            $payload = $midtrans->getTransactionStatus($orderId);
            $status = $payload['transaction_status'] ?? null;

            if ($status === 'settlement' || ($status === 'capture' && ($payload['fraud_status'] ?? null) === 'accept')) {
                $this->processSuccessfulTransaction($tagihan, $orderId, $payload);
                return response()->json(['status' => 'success', 'transaction_status' => $status]);
            }

            return response()->json(['status' => 'pending', 'transaction_status' => $status]);
        } catch (\Exception $e) {
            Log::warning('Midtrans status check failed: ' . $e->getMessage(), ['order_id' => $orderId]);
            return response()->json(['error' => 'Gagal memeriksa status pembayaran.'], 500);
        }
    }

    public function handleWebhook(Request $request)
    {
        try {
            $midtrans = new MidtransService();

            Log::info('Midtrans webhook received', ['payload' => $request->all()]);

            $signatureKey = $request->input('signature_key');
            $orderId = $request->input('order_id');
            $statusCode = $request->input('status_code');
            $grossAmount = $request->input('gross_amount');

            if (!$midtrans->verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
                Log::warning('Midtrans webhook: invalid signature', ['order_id' => $orderId]);
                return response()->json(['status' => 'invalid signature'], 403);
            }

            $transactionStatus = $request->input('transaction_status');
            $paymentType = $request->input('payment_type');
            $fraudStatus = $request->input('fraud_status');
            $transactionId = $request->input('transaction_id');

            $tagihanId = null;
            if (preg_match('/^SIMPP-(\d+)(?:-|$)/', $orderId, $matches)) {
                $tagihanId = (int) $matches[1];
            }

            if (!$tagihanId) {
                return response()->json(['status' => 'invalid order_id'], 400);
            }

            // Memulai DB Transaction dengan aman menggunakan Pessimistic Locking
            DB::beginTransaction();
            try {
                // Kunci baris data tagihan ini agar webhook duplikat mengantre di belakangnya
                $tagihan = Tagihan::where('id', $tagihanId)->lockForUpdate()->first();
                if (!$tagihan) {
                    DB::rollBack();
                    return response()->json(['status' => 'not found'], 404);
                }

                // Cek status pembayaran saat ini (Di dalam kunci transaksi)
                $existingPayment = Pembayaran::where('midtrans_order_id', $orderId)->first();

                if ($transactionStatus == 'settlement' || ($transactionStatus == 'capture' && $fraudStatus == 'accept')) {
                    $this->processSuccessfulTransaction($tagihan, $orderId, $request->all(), $transactionStatus, $transactionId, $paymentType, $grossAmount, $existingPayment);
                } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
                    if ($existingPayment) {
                        $existingPayment->update([
                            'midtrans_status' => $transactionStatus,
                            'midtrans_response' => $request->all(),
                        ]);
                        $tagihan->refreshPaymentStatus();
                    }
                }

                DB::commit();
                return response()->json(['status' => 'ok']);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Midtrans webhook processing error: ' . $e->getMessage());
                return response()->json(['status' => 'error'], 500);
            }

        } catch (\Exception $e) {
            Log::error('Midtrans webhook error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }
}