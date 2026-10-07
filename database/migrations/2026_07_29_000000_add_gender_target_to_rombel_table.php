<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('rombel')) {
            return;
        }

        if (!Schema::hasColumn('rombel', 'gender_target')) {
            Schema::table('rombel', function (Blueprint $table) {
                $table->enum('gender_target', ['CAMPUR', 'PUTRA', 'PUTRI'])
                      ->default('CAMPUR')
                      ->after('kapasitas');
            });
        }

        if ($this->hasIndex('rombel', 'rombel_unique')) {
            Schema::table('rombel', function (Blueprint $table) {
                $table->dropUnique('rombel_unique');
            });
        }

        if (! $this->hasIndex('rombel', 'rombel_unique')) {
            Schema::table('rombel', function (Blueprint $table) {
                $table->unique(
                    ['lembaga_id', 'tahun_pelajaran_id', 'nama', 'gender_target'],
                    'rombel_unique'
                );
            });
        }

        $this->ensureLembagaForeignKey();
    }

    public function down(): void
    {
        if (!Schema::hasTable('rombel')) {
            return;
        }

        if ($this->hasIndex('rombel', 'rombel_unique')) {
            Schema::table('rombel', function (Blueprint $table) {
                $table->dropUnique('rombel_unique');
            });
        }

        if (! $this->hasIndex('rombel', 'rombel_unique')) {
            Schema::table('rombel', function (Blueprint $table) {
                $table->unique(
                    ['lembaga_id', 'tahun_pelajaran_id', 'nama'],
                    'rombel_unique'
                );
            });
        }

        if (Schema::hasColumn('rombel', 'gender_target')) {
            Schema::table('rombel', function (Blueprint $table) {
                $table->dropColumn('gender_target');
            });
        }

        $this->ensureLembagaForeignKey();
    }

    private function hasIndex(string $table, string $index): bool
    {
        $schemaBuilder = Schema::getConnection()->getSchemaBuilder();

        if (method_exists($schemaBuilder, 'hasIndex')) {
            return $schemaBuilder->hasIndex($table, $index);
        }

        if (! $this->usesMySql()) {
            return false;
        }

        $result = DB::selectOne(
            'SELECT COUNT(1) AS aggregate
             FROM information_schema.statistics
             WHERE table_schema = DATABASE()
               AND table_name = ?
               AND index_name = ?',
            [$table, $index]
        );

        return (int) ($result->aggregate ?? 0) > 0;
    }

    private function hasLembagaForeignKey(): bool
    {
        if (! $this->usesMySql()) {
            return true;
        }

        $result = DB::selectOne(
            'SELECT COUNT(1) AS aggregate
             FROM information_schema.key_column_usage
             WHERE table_schema = DATABASE()
               AND table_name = ?
               AND column_name = ?
               AND referenced_table_name = ?',
            ['rombel', 'lembaga_id', 'lembaga']
        );

        return (int) ($result->aggregate ?? 0) > 0;
    }

    private function ensureLembagaForeignKey(): void
    {
        if ($this->hasLembagaForeignKey()) {
            return;
        }

        Schema::table('rombel', function (Blueprint $table) {
            $table->foreign('lembaga_id')->references('id')->on('lembaga')->cascadeOnDelete();
        });
    }

    private function usesMySql(): bool
    {
        return in_array(Schema::getConnection()->getDriverName(), ['mysql', 'mariadb'], true);
    }
};
