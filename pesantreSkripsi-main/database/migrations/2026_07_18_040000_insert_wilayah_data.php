<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->seedProvinsi();
        $this->seedKabupaten();
        $this->seedKecamatan();
        $this->seedDesa();
    }

    public function down(): void
    {
        DB::table('desa')->delete();
        DB::table('kecamatan')->delete();
        DB::table('kabupaten')->delete();
        DB::table('provinsi')->delete();
    }

    private function seedProvinsi(): void
    {
        $file = database_path('seeders/csv/provinces.csv');

        if (! file_exists($file)) {
            return;
        }

        $handle = fopen($file, 'r');
        $rows = [];
        $now = now();

        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = [
                'id' => (int) $row[0],
                'kode' => $row[0],
                'nama' => $row[1],
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

        if (! file_exists($file)) {
            return;
        }

        $handle = fopen($file, 'r');
        $rows = [];
        $now = now();

        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = [
                'id' => (int) $row[0],
                'provinsi_id' => (int) $row[1],
                'kode' => $row[0],
                'nama' => $row[2],
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

        if (! file_exists($file)) {
            return;
        }

        $handle = fopen($file, 'r');
        $rows = [];
        $now = now();

        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = [
                'id' => (int) $row[0],
                'kabupaten_id' => (int) $row[1],
                'kode' => $row[0],
                'nama' => $row[2],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($rows) >= 1000) {
                DB::table('kecamatan')->insertOrIgnore($rows);
                $rows = [];
            }
        }

        if (! empty($rows)) {
            DB::table('kecamatan')->insertOrIgnore($rows);
        }

        fclose($handle);
    }

    private function seedDesa(): void
    {
        $file = database_path('seeders/csv/villages.csv');

        if (! file_exists($file)) {
            return;
        }

        $handle = fopen($file, 'r');
        $rows = [];
        $now = now();

        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = [
                'id' => (int) $row[0],
                'kecamatan_id' => (int) $row[1],
                'kode' => $row[0],
                'nama' => $row[2],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($rows) >= 2000) {
                DB::table('desa')->insertOrIgnore($rows);
                $rows = [];
            }
        }

        if (! empty($rows)) {
            DB::table('desa')->insertOrIgnore($rows);
        }

        fclose($handle);
    }
};
