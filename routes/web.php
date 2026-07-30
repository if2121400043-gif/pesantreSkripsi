<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Frontend\FrontendController;

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['id', 'en'])) {
        Session::put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

Route::get('/offline', function () {
    return view('offline');
})->name('offline');

Route::get('/manifest.json', function () {
    $path = public_path('manifest.json');

    if (! file_exists($path)) {
        abort(404);
    }

    return response()->file($path, ['Content-Type' => 'application/manifest+json; charset=utf-8']);
})->name('manifest');

/*
|--------------------------------------------------------------------------
| Web Routes — Sistem Manajemen Pesantren Nurul Furqon
|--------------------------------------------------------------------------
*/



Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.submit');
});

// ── Authenticated Routes ──
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Force Password Change Routes
    Route::get('/password/force-change', [\App\Http\Controllers\Auth\ForcePasswordController::class, 'create'])->name('password.force-change');
    Route::post('/password/force-change', [\App\Http\Controllers\Auth\ForcePasswordController::class, 'store'])->name('password.force-change.store');

    // Secure PSB Document view/download
    Route::get('/psb/view-dokumen/{dokumen}', [FrontendController::class, 'serveSecureDokumen'])
        ->middleware('role:PANITIA_PSB,SUPER_ADMIN')
        ->name('frontend.psb.view-dokumen');

    // Akun Pengguna (Profil, Password, dan Peran)
    Route::prefix('akun')->name('akun.')->group(function () {
        Route::get('/profil', [\App\Http\Controllers\Auth\ProfileController::class, 'show'])->name('profil');
        Route::post('/profil', [\App\Http\Controllers\Auth\ProfileController::class, 'update'])->name('profil.update');
        Route::get('/ganti-password', [\App\Http\Controllers\Auth\ProfileController::class, 'editPassword'])->name('ganti-password');
        Route::post('/ganti-password', [\App\Http\Controllers\Auth\ProfileController::class, 'updatePassword'])->name('ganti-password.update');
        Route::get('/ganti-peran', [\App\Http\Controllers\Auth\ProfileController::class, 'editRole'])->name('ganti-peran');
        Route::post('/ganti-peran', [\App\Http\Controllers\Auth\ProfileController::class, 'updateRole'])->name('ganti-peran.update');
    });

    // ── Admin Routes ──
    Route::middleware('role:SUPER_ADMIN')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Wilayah
        Route::prefix('wilayah')->name('wilayah.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\WilayahController::class, 'index'])->name('index');
            // Provinsi
            Route::post('provinsi', [\App\Http\Controllers\Admin\WilayahController::class, 'storeProvinsi'])->name('provinsi.store');
            Route::put('provinsi/{provinsi}', [\App\Http\Controllers\Admin\WilayahController::class, 'updateProvinsi'])->name('provinsi.update');
            Route::delete('provinsi/{provinsi}', [\App\Http\Controllers\Admin\WilayahController::class, 'destroyProvinsi'])->name('provinsi.destroy');
            // Kabupaten
            Route::post('kabupaten', [\App\Http\Controllers\Admin\WilayahController::class, 'storeKabupaten'])->name('kabupaten.store');
            Route::put('kabupaten/{kabupaten}', [\App\Http\Controllers\Admin\WilayahController::class, 'updateKabupaten'])->name('kabupaten.update');
            Route::delete('kabupaten/{kabupaten}', [\App\Http\Controllers\Admin\WilayahController::class, 'destroyKabupaten'])->name('kabupaten.destroy');
            // Kecamatan
            Route::post('kecamatan', [\App\Http\Controllers\Admin\WilayahController::class, 'storeKecamatan'])->name('kecamatan.store');
            Route::put('kecamatan/{kecamatan}', [\App\Http\Controllers\Admin\WilayahController::class, 'updateKecamatan'])->name('kecamatan.update');
            Route::delete('kecamatan/{kecamatan}', [\App\Http\Controllers\Admin\WilayahController::class, 'destroyKecamatan'])->name('kecamatan.destroy');
            // Desa
            Route::post('desa', [\App\Http\Controllers\Admin\WilayahController::class, 'storeDesa'])->name('desa.store');
            Route::put('desa/{desa}', [\App\Http\Controllers\Admin\WilayahController::class, 'updateDesa'])->name('desa.update');
            Route::delete('desa/{desa}', [\App\Http\Controllers\Admin\WilayahController::class, 'destroyDesa'])->name('desa.destroy');
        });

        // Master Data
        Route::resource('lembaga', \App\Http\Controllers\Admin\LembagaController::class)->except(['create', 'show', 'edit']);
        Route::resource('tahun-pelajaran', \App\Http\Controllers\Admin\TahunPelajaranController::class)->except(['create', 'show', 'edit']);
        
        Route::resource('asrama', \App\Http\Controllers\Admin\AsramaController::class)->except(['create', 'edit']);
        Route::post('asrama/{asrama}/kamar', [\App\Http\Controllers\Admin\AsramaController::class, 'storeKamar'])->name('asrama.kamar.store');
        Route::put('asrama/{asrama}/kamar/{kamar}', [\App\Http\Controllers\Admin\AsramaController::class, 'updateKamar'])->name('asrama.kamar.update');
        Route::delete('asrama/{asrama}/kamar/{kamar}', [\App\Http\Controllers\Admin\AsramaController::class, 'destroyKamar'])->name('asrama.kamar.destroy');
        Route::get('penempatan-asrama', [\App\Http\Controllers\Admin\PenempatanAsramaController::class, 'index'])->name('penempatan-asrama.index');
        Route::post('penempatan-asrama', [\App\Http\Controllers\Admin\PenempatanAsramaController::class, 'store'])->name('penempatan-asrama.store');
        Route::delete('penempatan-asrama/remove', [\App\Http\Controllers\Admin\PenempatanAsramaController::class, 'destroy'])->name('penempatan-asrama.remove');
        // Kepesantrenan (Identity)
        Route::resource('orang', \App\Http\Controllers\Admin\OrangController::class);
        Route::resource('peserta-didik', \App\Http\Controllers\Admin\PesertaDidikController::class);
        Route::resource('pegawai', \App\Http\Controllers\Admin\PegawaiController::class);
        Route::resource('keluarga', \App\Http\Controllers\Admin\KeluargaController::class)->except(['create', 'show', 'edit']);
        Route::put('keluarga/wali/{orang}/update', [\App\Http\Controllers\Admin\KeluargaController::class, 'updateWali'])->name('keluarga.wali.update');
        Route::post('keluarga/wali/{orang}/reset-password', [\App\Http\Controllers\Admin\KeluargaController::class, 'resetPassword'])->name('keluarga.wali.reset-password');

        // Akademik
        Route::resource('rombel', \App\Http\Controllers\Admin\RombelController::class);
        Route::get('penempatan', [\App\Http\Controllers\Admin\PenempatanController::class, 'index'])->name('penempatan.index');
        Route::post('penempatan', [\App\Http\Controllers\Admin\PenempatanController::class, 'store'])->name('penempatan.store');
        Route::delete('penempatan/remove', [\App\Http\Controllers\Admin\PenempatanController::class, 'destroyRombelPeserta'])->name('penempatan.remove');
        
        Route::resource('mata-pelajaran', \App\Http\Controllers\Admin\MataPelajaranController::class)->except(['create', 'show', 'edit']);
        
        Route::get('penilaian', [\App\Http\Controllers\Admin\PenilaianController::class, 'index'])->name('penilaian.index');
        Route::post('penilaian', [\App\Http\Controllers\Admin\PenilaianController::class, 'store'])->name('penilaian.store');
        
        Route::get('jadwal-pelajaran', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'index'])->name('jadwal-pelajaran.index');
        Route::post('jadwal-pelajaran', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'store'])->name('jadwal-pelajaran.store');
        Route::delete('jadwal-pelajaran/{jadwal}', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'destroy'])->name('jadwal-pelajaran.destroy');
        
        Route::get('presensi', [\App\Http\Controllers\Admin\PresensiController::class, 'index'])->name('presensi.index');
        Route::post('presensi', [\App\Http\Controllers\Admin\PresensiController::class, 'store'])->name('presensi.store');
        Route::resource('jenis-presensi', \App\Http\Controllers\Admin\JenisPresensiController::class)->except(['show']);
        
        // Keuangan
        Route::resource('komponen-biaya', \App\Http\Controllers\Admin\KomponenBiayaController::class)->except(['create', 'show', 'edit']);
        Route::resource('tagihan', \App\Http\Controllers\Admin\TagihanController::class)->except(['edit', 'update']);
        Route::post('pembayaran', [\App\Http\Controllers\Admin\PembayaranController::class, 'store'])->name('pembayaran.store');
        Route::delete('pembayaran/{pembayaran}', [\App\Http\Controllers\Admin\PembayaranController::class, 'destroy'])->name('pembayaran.destroy');
        Route::get('pembayaran/{pembayaran}/kuitansi', [\App\Http\Controllers\Admin\PembayaranController::class, 'kuitansi'])->name('pembayaran.kuitansi');
        Route::get('laporan-keuangan', [\App\Http\Controllers\Bendahara\LaporanController::class, 'index'])->name('laporan-keuangan.index');
        Route::get('laporan-keuangan/export', [\App\Http\Controllers\Bendahara\LaporanController::class, 'export'])->name('laporan-keuangan.export');
        
        // WhatsApp Notification
        Route::post('tagihan/{tagihan}/wa-reminder', [\App\Http\Controllers\Admin\TagihanController::class, 'sendWaReminder'])->name('tagihan.wa-reminder');
        Route::post('tagihan-blast-reminder', [\App\Http\Controllers\Admin\TagihanController::class, 'blastWaReminder'])->name('tagihan.blast-reminder');
        Route::get('broadcast-wa', [\App\Http\Controllers\Admin\BroadcastWaController::class, 'index'])->name('broadcast-wa.index');
        Route::post('broadcast-wa/send', [\App\Http\Controllers\Admin\BroadcastWaController::class, 'send'])->name('broadcast-wa.send');
        
        // PSB
        Route::prefix('psb')->name('psb.')->group(function () {
            Route::resource('gelombang', \App\Http\Controllers\Admin\GelombangPsbController::class)->except(['create', 'show', 'edit']);
            Route::resource('calon-santri', \App\Http\Controllers\Admin\CalonSantriController::class);
            Route::put('calon-santri/{calon_santri}/verifikasi', [\App\Http\Controllers\Admin\CalonSantriController::class, 'verifikasi'])->name('calon-santri.verifikasi');
            Route::patch('calon-santri/{calon_santri}/dokumen/{dokumen}', [\App\Http\Controllers\Admin\CalonSantriController::class, 'verifikasiDokumen'])->name('calon-santri.verifikasi-dokumen');
        });
        
        // Kedisiplinan
        Route::resource('jenis-pelanggaran', \App\Http\Controllers\Admin\JenisPelanggaranController::class)->except(['create', 'show', 'edit']);
        Route::resource('pelanggaran', \App\Http\Controllers\Admin\CatatanPelanggaranController::class)->only(['index', 'store', 'destroy']);
        Route::resource('prestasi', \App\Http\Controllers\Admin\CatatanPrestasiController::class)->only(['index', 'store', 'destroy']);
        Route::resource('perizinan', \App\Http\Controllers\Admin\PerizinanKeluarController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('mutasi', \App\Http\Controllers\Admin\MutasiSantriController::class)->only(['index', 'create', 'store', 'destroy']);
        Route::get('api/search-santri', [\App\Http\Controllers\Admin\CatatanPelanggaranController::class, 'searchSantri']);
        
        // Konten Website
        Route::resource('berita', \App\Http\Controllers\Admin\BeritaController::class)->except(['show']);
        Route::resource('media', \App\Http\Controllers\Admin\MediaController::class)->except(['show']);
        
        // Pengaturan
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['show']);
        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class)->except(['create', 'show', 'edit']);
        Route::post('permissions', [\App\Http\Controllers\Admin\PermissionController::class, 'store'])->name('permissions.store');
        Route::delete('permissions/{permission}', [\App\Http\Controllers\Admin\PermissionController::class, 'destroy'])->name('permissions.destroy');
        Route::get('konfigurasi', [\App\Http\Controllers\Admin\KonfigurasiController::class, 'index'])->name('konfigurasi.index');
        Route::put('konfigurasi', [\App\Http\Controllers\Admin\KonfigurasiController::class, 'update'])->name('konfigurasi.update');
        Route::get('api/search-orang', [\App\Http\Controllers\Admin\UserController::class, 'searchOrang']);
        
        // Region API endpoints for dropdowns
        Route::get('api/provinsi/{provinsi}/kabupaten', [\App\Http\Controllers\Admin\OrangController::class, 'getKabupaten']);
        Route::get('api/kabupaten/{kabupaten}/kecamatan', [\App\Http\Controllers\Admin\OrangController::class, 'getKecamatan']);
        Route::get('api/kecamatan/{kecamatan}/desa', [\App\Http\Controllers\Admin\OrangController::class, 'getDesa']);
    });

    // ── Portal Wali Santri Routes ──
    Route::middleware('role:WALI_SANTRI')->prefix('portal')->name('portal.')->group(function () {
        Route::get('/beranda', [\App\Http\Controllers\Portal\PortalController::class, 'beranda'])->name('beranda');
        Route::get('/tagihan', [\App\Http\Controllers\Portal\PortalController::class, 'tagihan'])->name('tagihan');
        Route::get('/presensi', [\App\Http\Controllers\Portal\PortalController::class, 'presensi'])->name('presensi');
        Route::get('/kedisiplinan', [\App\Http\Controllers\Portal\PortalController::class, 'kedisiplinan'])->name('kedisiplinan');

        // Pembayaran Online (Midtrans Snap)
        Route::get('/tagihan/{tagihan}/bayar', [\App\Http\Controllers\Portal\PaymentController::class, 'show'])->name('payment.show');
        Route::post('/tagihan/{tagihan}/snap-token', [\App\Http\Controllers\Portal\PaymentController::class, 'getSnapToken'])->name('payment.snap-token');
        Route::get('/tagihan/{tagihan}/check-status', [\App\Http\Controllers\Portal\PaymentController::class, 'checkStatus'])->name('payment.check-status');
    });

    // ── Portal Guru Routes ──
    Route::middleware('role:GURU')->prefix('guru')->name('guru.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Guru\DashboardController::class, 'index'])->name('dashboard');
        
        // Presensi
        Route::get('/presensi', [\App\Http\Controllers\Guru\PresensiController::class, 'index'])->name('presensi.index');
        Route::get('/presensi/{jadwal_id}', [\App\Http\Controllers\Guru\PresensiController::class, 'create'])->name('presensi.create');
        Route::post('/presensi/{jadwal_id}', [\App\Http\Controllers\Guru\PresensiController::class, 'store'])->name('presensi.store');
        
        // Penilaian
        Route::get('/penilaian', [\App\Http\Controllers\Guru\PenilaianController::class, 'index'])->name('penilaian.index');
        Route::get('/penilaian/{jadwal_id}', [\App\Http\Controllers\Guru\PenilaianController::class, 'create'])->name('penilaian.create');
        Route::post('/penilaian/{jadwal_id}', [\App\Http\Controllers\Guru\PenilaianController::class, 'store'])->name('penilaian.store');
        
        // Kedisiplinan & Prestasi
        Route::get('/kedisiplinan', [\App\Http\Controllers\Guru\KedisiplinanController::class, 'index'])->name('kedisiplinan.index');
        Route::post('/kedisiplinan/pelanggaran', [\App\Http\Controllers\Guru\KedisiplinanController::class, 'storePelanggaran'])->name('pelanggaran.store');
        Route::post('/kedisiplinan/prestasi', [\App\Http\Controllers\Guru\KedisiplinanController::class, 'storePrestasi'])->name('prestasi.store');
        Route::get('/api/search-santri', [\App\Http\Controllers\Guru\KedisiplinanController::class, 'searchSantri'])->name('api.search-santri');
    });

    // ── Panitia PSB Routes ──
    Route::middleware('role:PANITIA_PSB')->prefix('panitia-psb')->name('panitia-psb.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\PanitiaPsb\DashboardController::class, 'index'])->name('dashboard');

        // Gelombang PSB
        Route::resource('gelombang', \App\Http\Controllers\PanitiaPsb\GelombangController::class)->except(['create', 'show', 'edit']);

        // Calon Santri
        Route::resource('calon-santri', \App\Http\Controllers\PanitiaPsb\CalonSantriController::class);
        Route::put('calon-santri/{calon_santri}/verifikasi', [\App\Http\Controllers\PanitiaPsb\CalonSantriController::class, 'verifikasi'])->name('calon-santri.verifikasi');
        Route::patch('calon-santri/{calon_santri}/dokumen/{dokumen}', [\App\Http\Controllers\PanitiaPsb\CalonSantriController::class, 'verifikasiDokumen'])->name('calon-santri.verifikasi-dokumen');
    });

    // ── Bendahara Routes ──
    Route::middleware('role:BENDAHARA')->prefix('bendahara')->name('bendahara.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Bendahara\DashboardController::class, 'index'])->name('dashboard');

        // Komponen Biaya
        Route::resource('komponen-biaya', \App\Http\Controllers\Bendahara\KomponenBiayaController::class)->except(['create', 'show', 'edit']);

        // Tagihan
        Route::resource('tagihan', \App\Http\Controllers\Bendahara\TagihanController::class)->except(['edit', 'update']);
        Route::get('tagihan/{tagihan}/bayar', [\App\Http\Controllers\Bendahara\TagihanController::class, 'bayar'])->name('tagihan.bayar');
        Route::post('pembayaran', [\App\Http\Controllers\Bendahara\PembayaranController::class, 'store'])->name('pembayaran.store');
        Route::delete('pembayaran/{pembayaran}', [\App\Http\Controllers\Bendahara\PembayaranController::class, 'destroy'])->name('pembayaran.destroy');
        Route::get('pembayaran/{pembayaran}/kuitansi', [\App\Http\Controllers\Bendahara\PembayaranController::class, 'kuitansi'])->name('pembayaran.kuitansi');
        Route::get('laporan-keuangan', [\App\Http\Controllers\Bendahara\LaporanController::class, 'index'])->name('laporan-keuangan.index');
        Route::get('laporan-keuangan/export', [\App\Http\Controllers\Bendahara\LaporanController::class, 'export'])->name('laporan-keuangan.export');
        Route::post('tagihan/{tagihan}/wa-reminder', [\App\Http\Controllers\Bendahara\TagihanController::class, 'sendWaReminder'])->name('tagihan.wa-reminder');
        Route::post('tagihan-blast-reminder', [\App\Http\Controllers\Bendahara\TagihanController::class, 'blastWaReminder'])->name('tagihan.blast-reminder');
    });
});

// ── Midtrans Webhook (tanpa auth & CSRF) ──
Route::post('/midtrans/webhook', [\App\Http\Controllers\Portal\PaymentController::class, 'handleWebhook'])
    ->name('midtrans.webhook');

// // ── Public Routes (Frontend) ──
// Route::get('/', [FrontendController::class, 'index'])->name('frontend.home');
// Route::get('/profil', [FrontendController::class, 'profil'])->name('frontend.profil');
// Route::get('/berita', [FrontendController::class, 'berita'])->name('frontend.berita');
// Route::get('/berita/{slug}', [FrontendController::class, 'showBerita'])->name('frontend.berita.show');
// Route::get('/galeri', [FrontendController::class, 'media'])->name('frontend.media');

// // PSB Public Routes
// Route::prefix('psb')->name('frontend.psb')->group(function () {
//     Route::get('/', [FrontendController::class, 'psb']);
//     Route::get('/daftar', [FrontendController::class, 'daftar'])->name('.daftar');
//     Route::post('/daftar', [FrontendController::class, 'storePsb'])->name('.store');
//     Route::get('/upload/{no_pendaftaran}', [FrontendController::class, 'uploadBerkas'])->name('.upload');
//     Route::post('/upload/{no_pendaftaran}', [FrontendController::class, 'storeBerkas'])->name('.store-berkas');
//     Route::get('/selesai/{no_pendaftaran}', [FrontendController::class, 'selesai'])->name('.selesai');
// });

Route::controller(FrontendController::class)->group(function () {
    Route::get('/', 'index')->name('frontend.home');
    Route::get('/profil', 'profil')->name('frontend.profil');
    Route::get('/berita', 'berita')->name('frontend.berita');
    Route::get('/berita/{slug}', 'showBerita')->name('frontend.berita.show');
    Route::get('/galeri', 'media')->name('frontend.media');

    // PSB Public Routes
    Route::prefix('psb')->name('frontend.psb')->group(function () {
        Route::get('/', 'psb');
        Route::get('/daftar', 'daftar')->name('.daftar');
        Route::post('/daftar', 'storePsb')->middleware('throttle:5,1')->name('.store');
        Route::get('/upload/{no_pendaftaran}', 'uploadBerkas')->name('.upload');
        Route::post('/upload/{no_pendaftaran}', 'storeBerkas')->name('.store-berkas');
        Route::get('/selesai/{no_pendaftaran}', 'selesai')->name('.selesai');
    });
});
