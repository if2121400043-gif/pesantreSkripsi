<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rombel', function (Blueprint $table) {
            $table->enum('gender_target', ['CAMPUR', 'PUTRA', 'PUTRI'])->default('CAMPUR')->after('kapasitas');
            $table->dropUnique('rombel_unique');
            $table->unique(['lembaga_id', 'tahun_pelajaran_id', 'nama', 'gender_target'], 'rombel_unique');
        });
    }

    public function down(): void
    {
        Schema::table('rombel', function (Blueprint $table) {
            $table->dropUnique('rombel_unique');
            $table->unique(['lembaga_id', 'tahun_pelajaran_id', 'nama'], 'rombel_unique');
            $table->dropColumn('gender_target');
        });
    }
};
