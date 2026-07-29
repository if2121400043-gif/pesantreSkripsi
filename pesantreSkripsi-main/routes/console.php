<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('test:wa {phone} {name=Calon_Santri}', function ($phone, $name) {
    $this->info("Mengirim pesan WhatsApp via Fonnte ke $phone...");
    
    $result = \App\Services\WhatsAppService::sendWelcomeMessage($phone, str_replace('_', ' ', $name), 'username_test', 'password_test');
    
    if ($result) {
        $this->info('Pesan berhasil diproses (cek log untuk detail error jika gagal).');
    } else {
        $this->error('Gagal memproses pengiriman pesan (kemungkinan token belum diset).');
    }
})->purpose('Test pengiriman notifikasi WhatsApp Fonnte');
