<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rombel', function (Blueprint $table) {
            // Drop old index if exists
            try {
                $table->dropUnique('rombel_unique');
            } catch (\Exception $e) {
                // Ignore if already dropped
            }
        });

        Schema::table('rombel', function (Blueprint $table) {
            // Create new composite unique key including gender_target
            $table->unique(['lembaga_id', 'tahun_pelajaran_id', 'nama', 'gender_target'], 'rombel_unique');
        });
    }

    public function down(): void
    {
        Schema::table('rombel', function (Blueprint $table) {
            try {
                $table->dropUnique('rombel_unique');
            } catch (\Exception $e) {
                // Ignore
            }
            $table->unique(['lembaga_id', 'tahun_pelajaran_id', 'nama'], 'rombel_unique');
        });
    }
};
