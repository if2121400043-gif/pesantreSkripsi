<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('roles')->insertOrIgnore([
            'nama' => 'WALI_SANTRI',
            'label' => 'Wali Santri',
            'redirect_url' => '/portal/beranda',
            'deskripsi' => 'Akses terbatas untuk orang tua/wali memantau perkembangan anak',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('roles')->where('nama', 'WALI_SANTRI')->delete();
    }
};
