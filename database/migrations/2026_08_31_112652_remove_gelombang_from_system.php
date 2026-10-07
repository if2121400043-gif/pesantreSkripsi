<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if calon_santri table has gelombang_id before dropping
        if (Schema::hasColumn('calon_santri', 'gelombang_id')) {
            // Drop foreign key first, use try-catch because FK name may differ
            try {
                Schema::table('calon_santri', function (Blueprint $table) {
                    $table->dropForeign(['gelombang_id']);
                });
            } catch (\Exception $e) {
                // FK doesn't exist or has different name, safe to continue
            }

            Schema::table('calon_santri', function (Blueprint $table) {
                $table->dropColumn('gelombang_id');
            });
        }

        // Drop gelombang_psb table
        Schema::dropIfExists('gelombang_psb');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system', function (Blueprint $table) {
            //
        });
    }
};
