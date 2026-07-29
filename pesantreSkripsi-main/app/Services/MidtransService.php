<?php

namespace App\Services;

use App\Models\Tagihan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Buat Snap Token untuk tagihan tertentu.
     *
     * @param Tagihan $tagihan Tagihan yang akan dibayar
     * @param int $jumlahBayar Jumlah yang harus dibayar (sisa tagihan)
     * @return array ['snap_token' => string, 'order_id' => string]
     */
    public function createSnapToken(Tagihan $tagihan, int $jumlahBayar): array
    {
        $tagihan->load(['pesertaDidik.orang', 'komponenBiaya']);

        $orderId = 'SIMPP-' . $tagihan->id . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $jumlahBayar,
            ],
            'customer_details' => $this->buildCustomerDetails($tagihan),
            'item_details' => [
                [
                    'id' => (string) $tagihan->komponen_biaya_id,
                    'price' => $jumlahBayar,
                    'quantity' => 1,
                    'name' => substr($tagihan->komponenBiaya->nama ?? 'Tagihan', 0, 50),
                ],
            ],
        ];

        try {
            // Prioritaskan SDK resmi Midtrans bila cURL tersedia.
            if (function_exists('curl_init')) {
                $response = Snap::createTransaction($params);
                $token = $response->token ?? ($response['token'] ?? null);
                $redirect = $response->redirect_url ?? ($response['redirect_url'] ?? null);
            } else {
                $response = $this->postToMidtransWithStream($params);
                $token = $response['token'] ?? null;
                $redirect = $response['redirect_url'] ?? null;
            }

            if (empty($token)) {
                throw new \RuntimeException('Midtrans tidak mengembalikan token pembayaran.');
            }

            return [
                'snap_token' => $token,
                'order_id' => $orderId,
                'redirect_url' => $redirect,
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans SDK Error: ' . $e->getMessage());
            throw new \RuntimeException('Gagal menghubungi server pembayaran Midtrans.');
        }
    }

    public function getTransactionStatus(string $orderId): array
    {
        $serverKey = config('midtrans.server_key');
        if (empty($serverKey)) {
            throw new \RuntimeException('MIDTRANS_SERVER_KEY belum dikonfigurasi.');
        }

        $endpoint = config('midtrans.is_production', false)
            ? 'https://api.midtrans.com/v2/' . rawurlencode($orderId) . '/status'
            : 'https://api.sandbox.midtrans.com/v2/' . rawurlencode($orderId) . '/status';

        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->withBasicAuth($serverKey, '')
            ->timeout(30)
            ->get($endpoint);

        if (!$response->successful()) {
            throw new \RuntimeException('Tidak dapat menghubungi Midtrans untuk memeriksa status transaksi.');
        }

        $decoded = $response->json();
        if (!is_array($decoded)) {
            throw new \RuntimeException('Respons status Midtrans tidak valid.');
        }

        return $decoded;
    }

    public function buildCustomerDetails(Tagihan $tagihan): array
    {
        $pesertaDidik = $tagihan->pesertaDidik;
        $orang = $pesertaDidik?->orang;
        $email = null;

        if ($orang) {
            $email = $orang->email ?? $orang->user?->email ?? null;
        }

        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $normalizedEmail = strtolower(trim($email));
        } else {
            $normalizedEmail = 'wali-' . ($orang->id ?? $tagihan->peserta_didik_id ?? 'santri') . '@pesantren.local';
        }

        $phone = '';
        if ($pesertaDidik && method_exists($pesertaDidik, 'getWaliPhone')) {
            $phone = $pesertaDidik->getWaliPhone() ?? '';
        }

        return [
            'first_name' => $orang->nama_lengkap ?? 'Wali Santri',
            'phone' => $phone,
            'email' => $normalizedEmail,
        ];
    }

    protected function postToMidtransWithStream(array $payload): array
    {
        $serverKey = config('midtrans.server_key');
        if (empty($serverKey)) {
            throw new \RuntimeException('MIDTRANS_SERVER_KEY belum dikonfigurasi.');
        }

        $endpoint = config('midtrans.is_production', false)
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $body = json_encode($payload, JSON_UNESCAPED_SLASHES);
        if ($body === false) {
            throw new \RuntimeException('Gagal membuat payload Midtrans.');
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Authorization: Basic ' . base64_encode($serverKey . ':'),
                    'Content-Length: ' . strlen($body),
                ],
                'content' => $body,
                'ignore_errors' => true,
                'timeout' => 30,
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
                'allow_self_signed' => false,
            ],
        ]);

        $responseBody = @file_get_contents($endpoint, false, $context);
        if ($responseBody === false) {
            throw new \RuntimeException('Tidak dapat menghubungi Midtrans.');
        }

        $decoded = json_decode($responseBody, true);
        if (!is_array($decoded)) {
            throw new \RuntimeException('Respons Midtrans tidak valid.');
        }

        return $decoded;
    }

    /**
     * Verifikasi signature key dari webhook notification.
     *
     * @param string $orderId
     * @param string $statusCode
     * @param string $grossAmount
     * @param string $signatureKey
     * @return bool
     */
    public function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $serverKey = config('midtrans.server_key');
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        // Use constant-time comparison to mitigate timing attacks
        return function_exists('hash_equals') ? hash_equals($expectedSignature, $signatureKey) : ($expectedSignature === $signatureKey);
    }
}
