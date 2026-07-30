<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

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

// ── Command 1: Auto Backup Database SQLite / MySQL ──
Artisan::command('app:backup-database', function () {
    $this->info('Memulai proses pembackupan database...');
    
    $connection = config('database.default');
    $backupDir = storage_path('app/backups');
    
    if (!File::exists($backupDir)) {
        File::makeDirectory($backupDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d_H-i-s');
    
    if ($connection === 'sqlite') {
        $dbPath = config('database.connections.sqlite.database');
        if (File::exists($dbPath)) {
            $destPath = $backupDir . "/database_backup_{$timestamp}.sqlite";
            File::copy($dbPath, $destPath);
            $this->info("Backup SQLite berhasil disimpan ke: $destPath");
        } else {
            $this->error('File database SQLite tidak ditemukan.');
        }
    } elseif ($connection === 'mysql') {
        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port');
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        
        $destPath = $backupDir . "/database_backup_{$timestamp}.sql";
        $command = sprintf(
            'mysqldump -h %s -P %s -u %s %s %s > %s',
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbUser),
            $dbPass ? '-p' . escapeshellarg($dbPass) : '',
            escapeshellarg($dbName),
            escapeshellarg($destPath)
        );
        
        exec($command, $output, $returnCode);
        
        if ($returnCode === 0) {
            $this->info("Backup MySQL berhasil disimpan ke: $destPath");
        } else {
            $this->error("Backup MySQL gagal. Pastikan mysqldump sudah terinstall.");
        }
    } else {
        $this->info("Backup koneksi $connection belum didukung.");
    }
})->purpose('Membuat cadangan database aplikasi otomatis');

// ── Command 2: Auto WhatsApp Blast Reminder Tagihan SPP ──
Artisan::command('app:blast-wa-tagihan', function () {
    $this->info('Memproses blast pengingat tagihan SPP via WhatsApp...');
    
    $tagihans = \App\Models\Tagihan::where('status', 'BELUM_LUNAS')
        ->with(['pesertaDidik.orang', 'komponenBiaya'])
        ->get();
        
    $count = 0;
    foreach ($tagihans as $tagihan) {
        $noHp = $tagihan->pesertaDidik?->orang?->telepon;
        if ($noHp) {
            $pesan = "Assalamu'alaikum Wr. Wb. Bapak/Ibu Wali Santri. Mengingatkan tagihan " . 
                     ($tagihan->komponenBiaya?->nama ?? 'SPP') . " sebesar Rp " . 
                     number_format($tagihan->nominal_akhir, 0, ',', '.') . " belum dilunasi. Terima kasih.";
            \App\Services\WhatsAppService::sendMessage($noHp, $pesan);
            $count++;
        }
    }
    
    $this->info("Pengingat berhasil dikirimkan ke $count wali santri.");
})->purpose('Kirim pengingat WhatsApp tagihan bulanan ke wali santri');

// ── Command 3: Auto Clean Draft PSB Unsubmitted ──
Artisan::command('app:clean-psb-drafts', function () {
    $this->info('Membersihkan data draf PSB yang tidak lengkap...');
    
    $expiredDate = now()->subDays(30);
    $deleted = \App\Models\CalonSantri::where('status', 'DRAFT')
        ->where('created_at', '<', $expiredDate)
        ->delete();
        
    $this->info("Berhasil menghapus $deleted draf PSB kedaluwarsa.");
})->purpose('Membersihkan draf pendaftaran PSB yang sudah lebih dari 30 hari');

// ── SCHEDULER REGISTRATION ──
Schedule::command('app:backup-database')->dailyAt('00:00');
Schedule::command('app:blast-wa-tagihan')->monthlyOn(1, '08:00');
Schedule::command('app:clean-psb-drafts')->weeklyOn(0, '02:00');
