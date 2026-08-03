<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$mapels = App\Models\MataPelajaran::with('lembaga')->get();
foreach($mapels as $m) {
    $lNama = $m->lembaga ? $m->lembaga->nama : 'Semua';
    $lSingk = $m->lembaga ? $m->lembaga->singkatan : '-';
    echo "ID: {$m->id} | Nama: {$m->nama} ({$m->kode}) | LembagaID: {$m->lembaga_id} ({$lSingk} / {$lNama})\n";
}
