<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$mapels = \App\Models\MataPelajaran::with('lembaga')->get();

echo "Total MataPelajaran: " . $mapels->count() . "\n\n";

foreach ($mapels as $m) {
    echo "ID: {$m->id} | Kode: {$m->kode} | Nama: {$m->nama} | LembagaID: {$m->lembaga_id} (" . ($m->lembaga->nama ?? 'Semua') . ")\n";
}
