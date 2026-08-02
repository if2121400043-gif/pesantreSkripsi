<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetWilayahSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Clearing old Wilayah data...');

        Schema::disableForeignKeyConstraints();

        // 1. Reset desa_id in orang table to null to avoid orphan references
        if (Schema::hasTable('orang')) {
            DB::table('orang')->update(['desa_id' => null]);
        }

        // 2. Truncate all 4 Wilayah tables
        DB::table('desa')->truncate();
        DB::table('kecamatan')->truncate();
        DB::table('kabupaten')->truncate();
        DB::table('provinsi')->truncate();

        Schema::enableForeignKeyConstraints();

        $this->command->info('Old Wilayah tables (desa, kecamatan, kabupaten, provinsi) have been completely cleaned!');
    }
}
