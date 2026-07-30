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
        });
    }

    public function down(): void
    {
        Schema::table('rombel', function (Blueprint $table) {
            $table->dropColumn('gender_target');
        });
    }
};
