<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add gender_target column if it doesn't exist yet
        if (!Schema::hasColumn('rombel', 'gender_target')) {
            Schema::table('rombel', function (Blueprint $table) {
                $table->enum('gender_target', ['CAMPUR', 'PUTRA', 'PUTRI'])
                      ->default('CAMPUR')
                      ->after('kapasitas');
            });
        }

        // 2. Drop FK on lembaga_id first (MySQL requires this before dropping unique index)
        Schema::table('rombel', function (Blueprint $table) {
            $table->dropForeign(['lembaga_id']);
        });

        // 3. Now safely drop the unique index
        Schema::table('rombel', function (Blueprint $table) {
            $table->dropUnique('rombel_unique');
        });

        // 4. Recreate unique index with gender_target included, and re-add FK
        Schema::table('rombel', function (Blueprint $table) {
            $table->unique(
                ['lembaga_id', 'tahun_pelajaran_id', 'nama', 'gender_target'],
                'rombel_unique'
            );
            $table->foreign('lembaga_id')->references('id')->on('lembaga')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rombel', function (Blueprint $table) {
            $table->dropForeign(['lembaga_id']);
            $table->dropUnique('rombel_unique');
        });

        Schema::table('rombel', function (Blueprint $table) {
            $table->unique(
                ['lembaga_id', 'tahun_pelajaran_id', 'nama'],
                'rombel_unique'
            );
            $table->foreign('lembaga_id')->references('id')->on('lembaga')->cascadeOnDelete();
            $table->dropColumn('gender_target');
        });
    }
};
