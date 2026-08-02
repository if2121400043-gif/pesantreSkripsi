<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Provinsi...');
        $this->seedProvinsi();

        $this->command->info('Seeding Kabupaten/Kota...');
        $this->seedKabupaten();

        $this->command->info('Seeding Kecamatan...');
        $this->seedKecamatan();

        $this->command->info('Seeding Desa/Kelurahan...');
        $this->seedDesa();

        $this->command->info('Wilayah Indonesia Seeder Completed Successfully!');
    }

    private function seedProvinsi(): void
    {
        $file = database_path('seeders/csv/provinces.csv');
        if (!file_exists($file)) return;

        $handle = fopen($file, 'r');
        $rows = [];
        $now = now();

        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row[0])) continue;
            $rows[] = [
                'id' => (int) $row[0],
                'kode' => trim($row[0]),
                'nama' => trim($row[1]),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        fclose($handle);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('provinsi')->insertOrIgnore($chunk);
        }
    }

    private function seedKabupaten(): void
    {
        $file = database_path('seeders/csv/regencies.csv');
        if (!file_exists($file)) return;

        $handle = fopen($file, 'r');
        $rows = [];
        $now = now();

        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row[0])) continue;
            $rows[] = [
                'id' => (int) $row[0],
                'provinsi_id' => (int) $row[1],
                'kode' => trim($row[0]),
                'nama' => trim($row[2]),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        fclose($handle);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('kabupaten')->insertOrIgnore($chunk);
        }
    }

    private function seedKecamatan(): void
    {
        $file = database_path('seeders/csv/districts.csv');
        if (!file_exists($file)) return;

        $handle = fopen($file, 'r');
        $rows = [];
        $now = now();

        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row[0])) continue;
            $rows[] = [
                'id' => (int) $row[0],
                'kabupaten_id' => (int) $row[1],
                'kode' => trim($row[0]),
                'nama' => trim($row[2]),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($rows) >= 1000) {
                DB::table('kecamatan')->insertOrIgnore($rows);
                $rows = [];
            }
        }

        if (!empty($rows)) {
            DB::table('kecamatan')->insertOrIgnore($rows);
        }

        fclose($handle);
    }

    private function seedDesa(): void
    {
        $file = database_path('seeders/csv/villages.csv');
        if (!file_exists($file)) return;

        $handle = fopen($file, 'r');
        $rows = [];
        $now = now();

        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row[0])) continue;
            $rows[] = [
                'id' => (int) $row[0],
                'kecamatan_id' => (int) $row[1],
                'kode' => trim($row[0]),
                'nama' => trim($row[2]),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($rows) >= 2000) {
                DB::table('desa')->insertOrIgnore($rows);
                $rows = [];
            }
        }

        if (!empty($rows)) {
            DB::table('desa')->insertOrIgnore($rows);
        }

        fclose($handle);
    }
}
