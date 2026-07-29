<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    // ═══════════════════════════════════════════════════
    // CORE: Send message via Fonnte API
    // ═══════════════════════════════════════════════════
    private static function send($phone, $message)
    {
        $phone = self::formatPhone($phone);

        try {
            $token = config('services.fonnte.token');

            if (!$token) {
                Log::warning("FONNTE_TOKEN is not set. WhatsApp message to {$phone} was not sent. Message: \n{$message}");
                return false;
            }

            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => $token
            ])->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62',
            ]);

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['status']) && $responseData['status'] === true) {
                Log::info("WhatsApp message successfully sent to {$phone} via Fonnte.");
                return true;
            } else {
                $reason = $responseData['reason'] ?? $response->body();
                Log::error("Fonnte API Error for {$phone}: " . $reason);
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Failed to send WhatsApp message to {$phone}: " . $e->getMessage());
            return false;
        }
    }

    // ═══════════════════════════════════════════════════
    // 1. WELCOME MESSAGE (Saat Santri DITERIMA via PSB)
    // ═══════════════════════════════════════════════════
    public static function sendWelcomeMessage($phone, $santriName, $username, $password = null)
    {
        if ($password) {
            $portalUrl = url('/portal/beranda');
            $message = "Assalamu'alaikum Wr. Wb.\n\n"
                     . "Selamat! Ananda *{$santriName}* resmi DITERIMA sebagai santri.\n\n"
                     . "Silakan pantau perkembangan dan tagihan ananda melalui Portal Wali.\n\n"
                     . "🔑 *Akun Portal Wali:*\n"
                     . "Username: *{$username}*\n"
                     . "Password: *{$password}*\n\n"
                     . "🌐 Link Portal: {$portalUrl}\n\n"
                     . "⚠️ _Harap segera ganti password saat pertama kali login._\n\n"
                     . "Wassalamu'alaikum Wr. Wb.";
        } else {
            $message = "Assalamu'alaikum Wr. Wb.\n\n"
                     . "Selamat! Ananda *{$santriName}* resmi DITERIMA.\n\n"
                     . "Data ananda telah otomatis ditautkan ke Akun Portal Wali Anda yang sudah ada.\n"
                     . "Silakan login menggunakan Username Anda untuk memantau perkembangan ananda.\n\n"
                     . "Wassalamu'alaikum Wr. Wb.";
        }

        return self::send($phone, $message);
    }

    // ═══════════════════════════════════════════════════
    // 2. TAGIHAN BARU TERBIT (Saat Admin Generate Tagihan)
    // ═══════════════════════════════════════════════════
    public static function sendTagihanBaru($phone, $santriName, $komponenNama, $nominal, $bulan = null, $jatuhTempo = null)
    {
        $periode = $bulan ? " periode *{$bulan}*" : '';
        $tempo = $jatuhTempo ? "\n📅 Jatuh Tempo: *{$jatuhTempo}*" : '';

        $portalUrl = url('/portal/tagihan');
        $message = "Assalamu'alaikum Wr. Wb.\n\n"
                 . "📋 *TAGIHAN BARU*\n\n"
                 . "Yth. Wali dari *{$santriName}*,\n"
                 . "Terdapat tagihan baru yang perlu dilunasi:\n\n"
                 . "💰 *{$komponenNama}*{$periode}\n"
                 . "Nominal: *Rp " . number_format($nominal, 0, ',', '.') . "*{$tempo}\n\n"
                 . "Silakan lakukan pembayaran di kantor Tata Usaha pesantren.\n"
                 . "Cek detail: {$portalUrl}\n\n"
                 . "Wassalamu'alaikum Wr. Wb.";

        return self::send($phone, $message);
    }

    // ═══════════════════════════════════════════════════
    // 3. PENGINGAT TAGIHAN (Manual dari Admin)
    // ═══════════════════════════════════════════════════
    public static function sendTagihanReminder($phone, $santriName, $komponenNama, $sisaTagihan, $jatuhTempo = null)
    {
        $tempo = $jatuhTempo ? "\n📅 Jatuh Tempo: *{$jatuhTempo}*" : '';

        $portalUrl = url('/portal/tagihan');
        $message = "Assalamu'alaikum Wr. Wb.\n\n"
                 . "🔔 *PENGINGAT TAGIHAN*\n\n"
                 . "Yth. Wali dari *{$santriName}*,\n"
                 . "Kami mengingatkan bahwa ananda masih memiliki tagihan yang belum lunas:\n\n"
                 . "💰 *{$komponenNama}*\n"
                 . "Sisa: *Rp " . number_format($sisaTagihan, 0, ',', '.') . "*{$tempo}\n\n"
                 . "Mohon segera melakukan pelunasan di kantor Tata Usaha pesantren.\n"
                 . "Cek detail: {$portalUrl}\n\n"
                 . "Jazakumullahu Khairan.\n"
                 . "Wassalamu'alaikum Wr. Wb.";

        return self::send($phone, $message);
    }

    // ═══════════════════════════════════════════════════
    // 4. KONFIRMASI PEMBAYARAN (Saat Admin Input Bayar)
    // ═══════════════════════════════════════════════════
    public static function sendPembayaranSukses($phone, $santriName, $komponenNama, $jumlahBayar, $sisaTagihan, $noTransaksi)
    {
        $statusLunas = $sisaTagihan <= 0
            ? "\n✅ Status: *LUNAS*"
            : "\n⏳ Sisa Tagihan: *Rp " . number_format($sisaTagihan, 0, ',', '.') . "*";

        $portalUrl = url('/portal/tagihan');
        $message = "Assalamu'alaikum Wr. Wb.\n\n"
                 . "✅ *PEMBAYARAN DITERIMA*\n\n"
                 . "Yth. Wali dari *{$santriName}*,\n"
                 . "Pembayaran ananda telah kami terima:\n\n"
                 . "💳 *{$komponenNama}*\n"
                 . "Dibayar: *Rp " . number_format($jumlahBayar, 0, ',', '.') . "*\n"
                 . "No. Transaksi: *{$noTransaksi}*{$statusLunas}\n\n"
                 . "Lihat riwayat: {$portalUrl}\n\n"
                 . "Terima kasih atas kepercayaan Anda.\n"
                 . "Wassalamu'alaikum Wr. Wb.";

        return self::send($phone, $message);
    }

    // ═══════════════════════════════════════════════════
    // 5. CUSTOM MESSAGE (Broadcast dari Admin)
    // ═══════════════════════════════════════════════════
    public static function sendCustomMessage($phone, $message)
    {
        return self::send($phone, $message);
    }

    // ═══════════════════════════════════════════════════
    // HELPER: Format Phone Number
    // ═══════════════════════════════════════════════════
    private static function formatPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        return $phone;
    }
}
