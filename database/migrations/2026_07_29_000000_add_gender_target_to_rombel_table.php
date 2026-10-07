<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
        //    Use try-catch because the FK name may differ or not exist in production
        try {
            Schema::table('rombel', function (Blueprint $table) {
                $table->dropForeign(['lembaga_id']);
            });
        } catch (\Exception $e) {
            // FK doesn't exist, safe to continue
        }

        // 3. Now safely drop the unique index
        try {
            Schema::table('rombel', function (Blueprint $table) {
                $table->dropUnique('rombel_unique');
            });
        } catch (\Exception $e) {
            // Unique index doesn't exist, safe to continue
        }

        // 4. Recreate unique index with gender_target included, and re-add FK
        Schema::table('rombel', function (Blueprint $table) {
            $table->unique(
                ['lembaga_id', 'tahun_pelajaran_id', 'nama', 'gender_target'],
                'rombel_unique'
            );

            // Only re-add FK if it doesn't already exist
            // Check by looking at existing foreign keys
            $fks = collect(DB::select("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'rombel' AND CONSTRAINT_TYPE = 'FOREIGN KEY'"))
                ->pluck('CONSTRAINT_NAME')
                ->toArray();

            if (!in_array('rombel_lembaga_id_foreign', $fks)) {
                $table->foreign('lembaga_id')->references('id')->on('lembaga')->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        try {
            Schema::table('rombel', function (Blueprint $table) {
                $table->dropForeign(['lembaga_id']);
            });
        } catch (\Exception $e) {
            // FK doesn't exist
        }

        try {
            Schema::table('rombel', function (Blueprint $table) {
                $table->dropUnique('rombel_unique');
            });
        } catch (\Exception $e) {
            // Index doesn't exist
        }

        Schema::table('rombel', function (Blueprint $table) {
            $table->unique(
                ['lembaga_id', 'tahun_pelajaran_id', 'nama'],
                'rombel_unique'
            );
            $table->foreign('lembaga_id')->references('id')->on('lembaga')->cascadeOnDelete();

            if (Schema::hasColumn('rombel', 'gender_target')) {
                $table->dropColumn('gender_target');
            }
        });
    }
};
