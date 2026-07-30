<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Server Key
    |--------------------------------------------------------------------------
    |
    | Server key dari dashboard Midtrans. Digunakan di sisi server untuk
    | membuat transaksi dan verifikasi webhook notification.
    | JANGAN pernah expose key ini ke frontend/client-side.
    |
    */
    'server_key' => env('MIDTRANS_SERVER_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Midtrans Client Key
    |--------------------------------------------------------------------------
    |
    | Client key dari dashboard Midtrans. Digunakan di frontend/browser
    | untuk menampilkan popup Snap.
    |
    */
    'client_key' => env('MIDTRANS_CLIENT_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Production Mode
    |--------------------------------------------------------------------------
    |
    | Set true jika sudah production (menggunakan real money).
    | Set false untuk mode sandbox (testing).
    |
    */
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    /*
    |--------------------------------------------------------------------------
    | Snap URL
    |--------------------------------------------------------------------------
    |
    | URL Snap.js dari Midtrans. Otomatis berubah sesuai mode.
    | Sandbox: https://app.sandbox.midtrans.com/snap/snap.js
    | Production: https://app.midtrans.com/snap/snap.js
    |
    */
    'snap_url' => env('MIDTRANS_SNAP_URL', 'https://app.sandbox.midtrans.com/snap/snap.js'),
];
