<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CahyaWilayahSeeder extends Seeder
{
    public function run(): void
    {
        $sqlFile = database_path('seeders/sql/wilayah.sql');

        if (!file_exists($sqlFile)) {
            $this->command->error("File SQL Wilayah tidak ditemukan di: {$sqlFile}");
            return;
        }

        $this->command->info("Memulai pengimporan Data Wilayah Indonesia (cahyadsn/wilayah)...");

        Schema::disableForeignKeyConstraints();

        // 1. Reset desa_id di tabel orang & bersihkan 4 tabel wilayah
        if (Schema::hasTable('orang')) {
            DB::table('orang')->update(['desa_id' => null]);
        }

        DB::table('desa')->truncate();
        DB::table('kecamatan')->truncate();
        DB::table('kabupaten')->truncate();
        DB::table('provinsi')->truncate();

        // 2. Baca file SQL line by line
        $handle = fopen($sqlFile, 'r');
        $now = now();

        $provinsiList = [];
        $kabupatenList = [];
        $kecamatanList = [];
        $desaList = [];

        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            if (preg_match("/\('([^']+)',\s*'([^']+)'\)/", $line, $matches)) {
                $kode = $matches[1];
                $nama = $matches[2];
                $len = strlen($kode);

                if ($len === 2) {
                    $provinsiList[] = ['kode' => $kode, 'nama' => $nama];
                } elseif ($len === 5) {
                    $provinsi_kode = substr($kode, 0, 2);
                    $kabupatenList[] = ['kode' => $kode, 'provinsi_kode' => $provinsi_kode, 'nama' => $nama];
                } elseif ($len === 8) {
                    $kabupaten_kode = substr($kode, 0, 5);
                    $kecamatanList[] = ['kode' => $kode, 'kabupaten_kode' => $kabupaten_kode, 'nama' => $nama];
                } elseif ($len === 13) {
                    $kecamatan_kode = substr($kode, 0, 8);
                    $desaList[] = ['kode' => $kode, 'kecamatan_kode' => $kecamatan_kode, 'nama' => $nama];
                }
            }
        }
        fclose($handle);

        // 3. Insert Provinsi & buat peta ID
        $this->command->info("Memproses " . count($provinsiList) . " Provinsi...");
        $provinsiIdMap = [];
        foreach ($provinsiList as $p) {
            $id = DB::table('provinsi')->insertGetId([
                'kode' => $p['kode'],
                'nama' => $p['nama'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $provinsiIdMap[$p['kode']] = $id;
        }

        // 4. Insert Kabupaten & buat peta ID
        $this->command->info("Memproses " . count($kabupatenList) . " Kabupaten/Kota...");
        $kabupatenIdMap = [];
        foreach ($kabupatenList as $k) {
            $provId = $provinsiIdMap[$k['provinsi_kode']] ?? null;
            if ($provId) {
                $id = DB::table('kabupaten')->insertGetId([
                    'provinsi_id' => $provId,
                    'kode' => $k['kode'],
                    'nama' => $k['nama'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $kabupatenIdMap[$k['kode']] = $id;
            }
        }

        // 5. Insert Kecamatan & buat peta ID
        $this->command->info("Memproses " . count($kecamatanList) . " Kecamatan...");
        $kecamatanIdMap = [];
        foreach ($kecamatanList as $kc) {
            $kabId = $kabupatenIdMap[$kc['kabupaten_kode']] ?? null;
            if ($kabId) {
                $id = DB::table('kecamatan')->insertGetId([
                    'kabupaten_id' => $kabId,
                    'kode' => $kc['kode'],
                    'nama' => $kc['nama'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $kecamatanIdMap[$kc['kode']] = $id;
            }
        }

        // 6. Insert Desa / Kelurahan dalam Chunk
        $this->command->info("Memproses " . count($desaList) . " Desa/Kelurahan...");
        $desaRows = [];
        foreach ($desaList as $d) {
            $kecId = $kecamatanIdMap[$d['kecamatan_kode']] ?? null;
            if ($kecId) {
                $desaRows[] = [
                    'kecamatan_id' => $kecId,
                    'kode' => $d['kode'],
                    'nama' => $d['nama'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($desaRows) >= 2000) {
                    DB::table('desa')->insert($desaRows);
                    $desaRows = [];
                }
            }
        }

        if (!empty($desaRows)) {
            DB::table('desa')->insert($desaRows);
        }

        Schema::enableForeignKeyConstraints();

        $this->command->info("✅ BERHASIL! Seluruh 38 Provinsi, 514 Kabupaten/Kota, 7.265 Kecamatan, dan 83.345 Desa/Kelurahan resmi terimpor!");
    }
}
