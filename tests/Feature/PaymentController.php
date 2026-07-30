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
    /**
     * Get peserta_didik IDs yang terkait dengan wali yang login.
     */
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

    /**
     * Tampilkan halaman pembayaran online untuk tagihan tertentu.
     */
    public function show(Tagihan $tagihan)
    {
        // Pastikan tagihan milik anak wali yang login
        $anakIds = $this->getAnakPesertaDidikIds();
        if (!in_array($tagihan->peserta_didik_id, $anakIds)) {
            abort(403, 'Anda tidak memiliki akses ke tagihan ini.');
        }

        $tagihan->load(['pesertaDidik.orang', 'komponenBiaya', 'pembayaran']);

        $totalDibayar = $tagihan->pembayaran->sum('jumlah');
        $sisaTagihan = max(0, $tagihan->total - $totalDibayar);

        if ($sisaTagihan <= 0) {
            return redirect()->route('portal.beranda', ['tab' => 'tagihan'])
                ->with('success', 'Tagihan ini sudah lunas.');
        }

        return view('portal.payment', compact('tagihan', 'sisaTagihan', 'totalDibayar'));
    }

    /**
     * Generate Snap Token untuk transaksi (AJAX request).
     */
    public function getSnapToken(Request $request, Tagihan $tagihan)
    {
        // Pastikan tagihan milik anak wali yang login
        $anakIds = $this->getAnakPesertaDidikIds();
        if (!in_array($tagihan->peserta_didik_id, $anakIds)) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $totalDibayar = $tagihan->pembayaran()->sum('jumlah');
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

    /**
     * Handle Webhook/Notification dari Midtrans.
     *
     * Route ini HARUS dikecualikan dari CSRF middleware dan tidak perlu auth,
     * karena dipanggil langsung oleh server Midtrans.
     */
    public function handleWebhook(Request $request)
    {
        try {
            $midtrans = new MidtransService();

            // Verifikasi signature
            $signatureKey = $request->input('signature_key');
            $orderId = $request->input('order_id');
            $statusCode = $request->input('status_code');
            $grossAmount = $request->input('gross_amount');

            if (!$midtrans->verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
                Log::warning('Midtrans webhook: invalid signature', [
                    'order_id' => $orderId,
                ]);
                return response()->json(['status' => 'invalid signature'], 403);
            }

            $transactionStatus = $request->input('transaction_status');
            $paymentType = $request->input('payment_type');
            $fraudStatus = $request->input('fraud_status');
            $transactionId = $request->input('transaction_id');

            // Extract tagihan_id dari order_id format: SIMPP-{tagihan_id}-{timestamp}
            $tagihanId = null;
            if (preg_match('/^SIMPP-(\d+)(?:-|$)/', $orderId, $matches)) {
                $tagihanId = (int) $matches[1];
            }

            if (!$tagihanId) {
                Log::error('Midtrans webhook: invalid order_id format', ['order_id' => $orderId]);
                return response()->json(['status' => 'invalid order_id'], 400);
            }

            $tagihan = Tagihan::find($tagihanId);
            if (!$tagihan) {
                Log::error('Midtrans webhook: tagihan not found', ['tagihan_id' => $tagihanId]);
                return response()->json(['status' => 'not found'], 404);
            }

            // Cek apakah transaksi dengan order_id ini sudah pernah diproses
            $existingPayment = Pembayaran::where('midtrans_order_id', $orderId)->first();

            DB::beginTransaction();
            try {
                if ($transactionStatus == 'settlement' ||
                    ($transactionStatus == 'capture' && $fraudStatus == 'accept')) {

                    // Pembayaran BERHASIL — buat record jika belum ada
                    if (!$existingPayment) {
                        $metodePembayaran = $this->mapMidtransMethod($paymentType);

                        $pembayaran = Pembayaran::create([
                            'tagihan_id' => $tagihan->id,
                            'no_transaksi' => 'MID-' . date('Ymd') . '-' . strtoupper(substr(md5($orderId), 0, 6)),
                            'jumlah' => $grossAmount,
                            'metode' => $metodePembayaran,
                            'midtrans_order_id' => $orderId,
                            'midtrans_transaction_id' => $transactionId,
                            'midtrans_payment_type' => $paymentType,
                            'midtrans_status' => $transactionStatus,
                            'midtrans_response' => json_encode($request->all()),
                            'tanggal_bayar' => now(),
                        ]);

                        // Update status tagihan
                        $tagihan->refreshPaymentStatus();
                        $totalDibayar = Pembayaran::where('tagihan_id', $tagihan->id)->sum('jumlah');
                        $newSisa = max(0, (float) $tagihan->total - (float) $totalDibayar);

                        // Kirim notifikasi WhatsApp
                        try {
                            $tagihan->load(['pesertaDidik.orang', 'komponenBiaya']);
                            $peserta = $tagihan->pesertaDidik;
                            $phone = $peserta?->getWaliPhone();

                            if ($phone) {
                                SendWhatsAppMessage::dispatch('pembayaran_sukses', $phone, [
                                    'santri_nama' => $peserta->orang->nama_lengkap,
                                    'komponen_nama' => $tagihan->komponenBiaya->nama,
                                    'jumlah_bayar' => $grossAmount,
                                    'sisa_tagihan' => $newSisa,
                                    'no_transaksi' => $pembayaran->no_transaksi,
                                ]);
                            }
                        } catch (\Exception $waEx) {
                            Log::warning('WA notif pembayaran Midtrans gagal: ' . $waEx->getMessage());
                        }
                    } else {
                        // Update status jika sudah ada
                        $existingPayment->update([
                            'midtrans_status' => $transactionStatus,
                            'midtrans_response' => json_encode($request->all()),
                        ]);

                        // Pastikan tagihan ikut diperbarui saat status transaksi berubah
                        $tagihan->refreshPaymentStatus();
                    }

                } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
                    // Pembayaran GAGAL
                    if ($existingPayment) {
                        $existingPayment->update([
                            'midtrans_status' => $transactionStatus,
                            'midtrans_response' => json_encode($request->all()),
                        ]);
                    }
                    Log::info("Midtrans: transaksi {$orderId} status {$transactionStatus}");

                } elseif ($transactionStatus == 'pending') {
                    // Pembayaran PENDING
                    Log::info("Midtrans: transaksi {$orderId} pending, menunggu pembayaran");
                }

                DB::commit();
                return response()->json(['status' => 'ok']);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Midtrans webhook processing error: ' . $e->getMessage(), [
                    'order_id' => $orderId,
                    'trace' => $e->getTraceAsString(),
                ]);
                return response()->json(['status' => 'error'], 500);
            }

        } catch (\Exception $e) {
            Log::error('Midtrans webhook error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }
}
