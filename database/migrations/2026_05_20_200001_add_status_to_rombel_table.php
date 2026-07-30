<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rombel', function (Blueprint $table) {
            $table->enum('status', ['AKTIF', 'SELESAI'])->default('AKTIF')->after('kapasitas');
        });
    }

    public function down(): void
    {
        Schema::table('rombel', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
