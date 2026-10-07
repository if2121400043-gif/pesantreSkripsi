<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AddGenderTargetToRombelMigrationTest extends TestCase
{
    public function test_migration_is_idempotent_when_unique_index_is_missing(): void
    {
        $this->prepareTables();
        $migration = require base_path('database/migrations/2026_07_29_000000_add_gender_target_to_rombel_table.php');

        $migration->up();
        $migration->up();

        $this->assertTrue(Schema::hasColumn('rombel', 'gender_target'));
        $this->assertSame(
            ['lembaga_id', 'tahun_pelajaran_id', 'nama', 'gender_target'],
            $this->indexColumns('rombel_unique')
        );

        $migration->down();
        $migration->down();

        $this->assertFalse(Schema::hasColumn('rombel', 'gender_target'));
        $this->assertSame(
            ['lembaga_id', 'tahun_pelajaran_id', 'nama'],
            $this->indexColumns('rombel_unique')
        );

        Schema::dropIfExists('rombel');
        Schema::dropIfExists('tahun_pelajaran');
        Schema::dropIfExists('lembaga');
    }

    private function prepareTables(): void
    {
        Schema::dropIfExists('rombel');
        Schema::dropIfExists('tahun_pelajaran');
        Schema::dropIfExists('lembaga');

        Schema::create('lembaga', function (Blueprint $table) {
            $table->id();
        });

        Schema::create('tahun_pelajaran', function (Blueprint $table) {
            $table->id();
        });

        Schema::create('rombel', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lembaga_id');
            $table->unsignedBigInteger('tahun_pelajaran_id');
            $table->string('nama', 50);
            $table->integer('kapasitas')->default(30);
        });
    }

    private function indexColumns(string $indexName): array
    {
        if (DB::getDriverName() === 'sqlite') {
            return collect(DB::select("PRAGMA index_info('{$indexName}')"))
                ->sortBy('seqno')
                ->pluck('name')
                ->values()
                ->all();
        }

        return collect(DB::select(
            'SELECT column_name
             FROM information_schema.statistics
             WHERE table_schema = DATABASE()
               AND table_name = ?
               AND index_name = ?
             ORDER BY seq_in_index',
            ['rombel', $indexName]
        ))
            ->pluck('column_name')
            ->values()
            ->all();
    }
}
