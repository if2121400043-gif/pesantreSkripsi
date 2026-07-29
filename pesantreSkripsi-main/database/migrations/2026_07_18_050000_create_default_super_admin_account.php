<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $roleId = DB::table('roles')->where('nama', 'SUPER_ADMIN')->value('id');

        if (! $roleId) {
            $roleId = DB::table('roles')->insertGetId([
                'nama' => 'SUPER_ADMIN',
                'label' => 'Super Admin',
                'redirect_url' => '/admin/dashboard',
                'deskripsi' => 'Administrator Utama',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $orangId = DB::table('orang')->where('niup', 'NIUP-2026-000001')->value('id');

        if (! $orangId) {
            $orangId = DB::table('orang')->insertGetId([
                'niup' => 'NIUP-2026-000001',
                'nama_lengkap' => 'Administrator Sistem',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Jombang',
                'tanggal_lahir' => '1990-01-01',
                'desa_id' => DB::table('desa')->value('id'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $userId = DB::table('users')->where('username', 'admin')->orWhere('email', 'admin@pesantren.id')->value('id');

        if (! $userId) {
            $userId = DB::table('users')->insertGetId([
                'orang_id' => $orangId,
                'username' => 'admin',
                'email' => 'admin@pesantren.id',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('user_role')->updateOrInsert(
            ['user_id' => $userId, 'role_id' => $roleId],
            [
                'is_default' => true,
                'is_active' => true,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        $userId = DB::table('users')->where('username', 'admin')->orWhere('email', 'admin@pesantren.id')->value('id');

        if ($userId) {
            DB::table('user_role')->where('user_id', $userId)->delete();
            DB::table('users')->where('id', $userId)->delete();
        }

        DB::table('orang')->where('niup', 'NIUP-2026-000001')->delete();
        DB::table('roles')->where('nama', 'SUPER_ADMIN')->delete();
    }
};
