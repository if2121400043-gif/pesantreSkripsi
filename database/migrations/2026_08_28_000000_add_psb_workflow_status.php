<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('calon_santri', 'status_workflow')) {
            Schema::table('calon_santri', function (Blueprint $table) {
                $table->string('status_workflow', 30)->nullable()->after('status')->index();
            });
        }

        DB::table('calon_santri')->whereNull('status_workflow')->update([
            'status_workflow' => DB::raw("CASE
                WHEN status = 'DITERIMA' THEN 'DITERIMA'
                WHEN status IN ('TIDAK_LULUS', 'DIBATALKAN') THEN 'DITOLAK'
                ELSE 'MENUNGGU_VERIFIKASI'
            END"),
        ]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('calon_santri', 'status_workflow')) {
            Schema::table('calon_santri', function (Blueprint $table) {
                $table->dropIndex(['status_workflow']);
                $table->dropColumn('status_workflow');
            });
        }
    }
};
