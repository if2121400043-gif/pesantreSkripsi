<?php

$sqlFile = __DIR__ . '/../database/seeders/sql/wilayah.sql';
if (!file_exists($sqlFile)) {
    echo "SQL file not found!\n";
    exit(1);
}

$lines = file($sqlFile);
$provinsi = [];
$kabupaten = [];
$kecamatan = [];
$desa = [];

foreach ($lines as $line) {
    $line = trim($line);
    // Match line format: ('11','Aceh'), or ('11.01','Kabupaten Aceh Selatan')
    if (preg_match("/\('([^']+)',\s*'([^']+)'\)/", $line, $matches)) {
        $kode = $matches[1];
        $nama = $matches[2];
        $len = strlen($kode);

        if ($len === 2) {
            $provinsi[] = ['kode' => $kode, 'nama' => $nama];
        } elseif ($len === 5) {
            $provinsi_kode = substr($kode, 0, 2);
            $kabupaten[] = ['kode' => $kode, 'provinsi_kode' => $provinsi_kode, 'nama' => $nama];
        } elseif ($len === 8) {
            $kabupaten_kode = substr($kode, 0, 5);
            $kecamatan[] = ['kode' => $kode, 'kabupaten_kode' => $kabupaten_kode, 'nama' => $nama];
        } elseif ($len === 13) {
            $kecamatan_kode = substr($kode, 0, 8);
            $desa[] = ['kode' => $kode, 'kecamatan_kode' => $kecamatan_kode, 'nama' => $nama];
        }
    }
}

echo "Total Provinsi  : " . count($provinsi) . "\n";
echo "Total Kabupaten : " . count($kabupaten) . "\n";
echo "Total Kecamatan : " . count($kecamatan) . "\n";
echo "Total Desa      : " . count($desa) . "\n";

// Sample outputs
echo "\nSample Provinsi  : " . json_encode($provinsi[0]) . "\n";
echo "Sample Kabupaten : " . json_encode($kabupaten[0]) . "\n";
echo "Sample Kecamatan : " . json_encode($kecamatan[0]) . "\n";
echo "Sample Desa      : " . json_encode($desa[0]) . "\n";
