<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\RateLimited;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

/**
 * Job untuk mengirim pesan WhatsApp secara asinkron via Laravel Queue.
 *
 * Menggantikan pengiriman synchronous (blocking) yang menyebabkan
 * timeout/kemacetan saat blast massal. Fonnte API membutuhkan jeda
 * ~2 detik antar pesan agar tidak di-block, sehingga job ini
 * diberi rate limit dan release delay.
 *
 * Contoh penggunaan:
 *   SendWhatsAppMessage::dispatch('tagihan_baru', '08123456789', [...]);
 */
class SendWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Jumlah percobaan ulang jika gagal.
     */
    public int $tries = 3;

    /**
     * Jeda (detik) sebelum retry jika gagal.
     */
    public int $backoff = 10;

    /**
     * @param string $type    Jenis pesan: 'tagihan_baru', 'tagihan_reminder', 'pembayaran_sukses', 'welcome', 'custom'
     * @param string $phone   Nomor HP tujuan (format 08xxx)
     * @param array  $data    Data dinamis sesuai jenis pesan
     */
    public function __construct(
        public string $type,
        public string $phone,
        public array  $data = [],
    ) {}

    /**
     * Eksekusi pengiriman pesan WhatsApp.
     */
    public function handle(): void
    {
        $result = match ($this->type) {
            'tagihan_baru' => WhatsAppService::sendTagihanBaru(
                $this->phone,
                $this->data['santri_nama'],
                $this->data['komponen_nama'],
                $this->data['nominal'],
                $this->data['bulan'] ?? null,
                $this->data['jatuh_tempo'] ?? null,
            ),
            'tagihan_reminder' => WhatsAppService::sendTagihanReminder(
                $this->phone,
                $this->data['santri_nama'],
                $this->data['komponen_nama'],
                $this->data['sisa_tagihan'],
                $this->data['jatuh_tempo'] ?? null,
            ),
            'pembayaran_sukses' => WhatsAppService::sendPembayaranSukses(
                $this->phone,
                $this->data['santri_nama'],
                $this->data['komponen_nama'],
                $this->data['jumlah_bayar'],
                $this->data['sisa_tagihan'],
                $this->data['no_transaksi'],
            ),
            'welcome' => WhatsAppService::sendWelcomeMessage(
                $this->phone,
                $this->data['santri_nama'],
                $this->data['username'],
                $this->data['password'] ?? null,
            ),
            'custom' => WhatsAppService::sendCustomMessage(
                $this->phone,
                $this->data['message'],
            ),
            default => false,
        };

        if (!$result) {
            Log::warning("WA Job gagal kirim [{$this->type}] ke {$this->phone}");
            // Lepaskan kembali ke queue untuk retry (jeda 5 detik)
            $this->release(5);
        } else {
            Log::info("WA Job sukses kirim [{$this->type}] ke {$this->phone}");
        }
    }

    /**
     * Handle kegagalan total (setelah semua retry habis).
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("WA Job FINAL FAIL [{$this->type}] ke {$this->phone}: " . $exception->getMessage());
    }
}
