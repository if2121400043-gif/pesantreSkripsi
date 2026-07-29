<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $desaId = DB::table('desa')->where('kode', '3517092001')->value('id')
            ?? DB::table('desa')->value('id');

        $pesantrenId = DB::table('pesantren')->where('nspp', '510035170001')->value('id');

        if (! $pesantrenId) {
            $pesantrenId = DB::table('pesantren')->insertGetId([
                'nama' => 'Pondok Pesantren Nurul Furqon',
                'nspp' => '510035170001',
                'alamat' => 'Jl. Pesantren No. 1, Kepuhkembeng',
                'desa_id' => $desaId,
                'kode_pos' => '61481',
                'telepon' => '0321-123456',
                'email' => 'info@nurulfurqon.id',
                'tahun_berdiri' => 1990,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $lembagaData = [
            [
                'pesantren_id' => $pesantrenId,
                'nama' => 'Madrasah Tsanawiyah (MTs) Nurul Furqon',
                'singkatan' => 'MTs',
                'jenjang' => 'SMP',
                'tipe' => 'FORMAL',
                'urutan' => 1,
            ],
            [
                'pesantren_id' => $pesantrenId,
                'nama' => 'Madrasah Aliyah (MA) Nurul Furqon',
                'singkatan' => 'MA',
                'jenjang' => 'SMA',
                'tipe' => 'FORMAL',
                'urutan' => 2,
            ],
            [
                'pesantren_id' => $pesantrenId,
                'nama' => 'Madrasah Diniyah (Madin) Nurul Furqon',
                'singkatan' => 'MADIN',
                'jenjang' => 'MADIN',
                'tipe' => 'NON_FORMAL',
                'urutan' => 3,
            ],
        ];

        foreach ($lembagaData as $item) {
            DB::table('lembaga')->updateOrInsert(
                ['pesantren_id' => $item['pesantren_id'], 'nama' => $item['nama']],
                $item + ['created_at' => now(), 'updated_at' => now()]
            );
        }

        DB::table('tahun_pelajaran')->updateOrInsert(
            ['pesantren_id' => $pesantrenId, 'nama' => '2026/2027'],
            [
                'tanggal_mulai' => '2026-07-15',
                'tanggal_selesai' => '2027-06-20',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $roles = [
            ['nama' => 'OPERATOR_LEMBAGA', 'label' => 'Operator Lembaga', 'redirect_url' => '/operator/dashboard', 'deskripsi' => 'Operator tiap lembaga sekolah'],
            ['nama' => 'GURU', 'label' => 'Guru / Ustadz', 'redirect_url' => '/guru/dashboard', 'deskripsi' => 'Guru mapel & wali kelas'],
            ['nama' => 'PANITIA_PSB', 'label' => 'Panitia PSB', 'redirect_url' => '/panitia-psb/dashboard', 'deskripsi' => 'Panitia Penerimaan Santri Baru'],
            ['nama' => 'BENDAHARA', 'label' => 'Bendahara', 'redirect_url' => '/bendahara/dashboard', 'deskripsi' => 'Bendahara Keuangan'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['nama' => $role['nama']],
                $role + ['is_active' => true, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        $roleIds = DB::table('roles')->whereIn('nama', ['SUPER_ADMIN', 'OPERATOR_LEMBAGA', 'GURU', 'PANITIA_PSB', 'BENDAHARA'])
            ->pluck('id', 'nama');

        $permissions = [
            ['nama' => 'master-data.view', 'label' => 'Lihat Master Data', 'grup' => 'master-data'],
            ['nama' => 'master-data.manage', 'label' => 'Kelola Master Data', 'grup' => 'master-data'],
            ['nama' => 'peserta-didik.view', 'label' => 'Lihat Peserta Didik', 'grup' => 'kepesantrenan'],
            ['nama' => 'peserta-didik.manage', 'label' => 'Kelola Peserta Didik', 'grup' => 'kepesantrenan'],
            ['nama' => 'akademik.view', 'label' => 'Lihat Akademik', 'grup' => 'akademik'],
            ['nama' => 'akademik.manage', 'label' => 'Kelola Akademik', 'grup' => 'akademik'],
            ['nama' => 'keuangan.view', 'label' => 'Lihat Keuangan', 'grup' => 'keuangan'],
            ['nama' => 'keuangan.manage', 'label' => 'Kelola Keuangan', 'grup' => 'keuangan'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['nama' => $permission['nama']],
                $permission + ['created_at' => now(), 'updated_at' => now()]
            );
        }

        $permissionIds = DB::table('permissions')->whereIn('nama', array_column($permissions, 'nama'))
            ->pluck('id', 'nama');

        $attachments = [
            'SUPER_ADMIN' => array_keys($permissionIds->toArray()),
            'OPERATOR_LEMBAGA' => ['peserta-didik.view', 'peserta-didik.manage', 'akademik.view', 'akademik.manage'],
            'GURU' => ['peserta-didik.view', 'akademik.view'],
            'PANITIA_PSB' => ['peserta-didik.view', 'peserta-didik.manage'],
            'BENDAHARA' => ['keuangan.view', 'keuangan.manage'],
        ];

        foreach ($attachments as $roleName => $permissionNames) {
            $roleId = $roleIds[$roleName] ?? null;

            if (! $roleId) {
                continue;
            }

            foreach ($permissionNames as $permissionName) {
                $permissionId = $permissionIds[$permissionName] ?? null;

                if (! $permissionId) {
                    continue;
                }

                DB::table('role_permission')->updateOrInsert(
                    ['role_id' => $roleId, 'permission_id' => $permissionId],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        $jenisPelanggarans = [
            ['nama' => 'Terlambat mengikuti jamaah shalat fardhu', 'kategori' => 'RINGAN', 'poin' => 5],
            ['nama' => 'Membuang sampah sembarangan', 'kategori' => 'RINGAN', 'poin' => 5],
            ['nama' => 'Tidak memakai atribut / seragam lengkap', 'kategori' => 'RINGAN', 'poin' => 5],
            ['nama' => 'Membuat kegaduhan di kamar / kelas', 'kategori' => 'RINGAN', 'poin' => 10],
            ['nama' => 'Keluar area pesantren tanpa surat izin resmi', 'kategori' => 'SEDANG', 'poin' => 15],
            ['nama' => 'Membawa barang elektronik (HP/MP3 player) tanpa izin', 'kategori' => 'SEDANG', 'poin' => 20],
            ['nama' => 'Tidak mengikuti kegiatan madrasah diniyah / pengajian tanpa alasan', 'kategori' => 'SEDANG', 'poin' => 15],
            ['nama' => 'Berinteraksi berlebihan dengan lawan jenis non-mahram', 'kategori' => 'SEDANG', 'poin' => 25],
            ['nama' => 'Mencuri barang milik orang lain', 'kategori' => 'BERAT', 'poin' => 50],
            ['nama' => 'Berkelahi / melakukan kekerasan fisik', 'kategori' => 'BERAT', 'poin' => 75],
            ['nama' => 'Mengonsumsi rokok di lingkungan pesantren', 'kategori' => 'BERAT', 'poin' => 50],
            ['nama' => 'Kabur / meninggalkan pesantren tanpa izin berhari-hari', 'kategori' => 'BERAT', 'poin' => 100],
        ];

        foreach ($jenisPelanggarans as $jenisPelanggaran) {
            DB::table('jenis_pelanggaran')->updateOrInsert(
                ['pesantren_id' => $pesantrenId, 'nama' => $jenisPelanggaran['nama']],
                $jenisPelanggaran + ['pesantren_id' => $pesantrenId, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    public function down(): void
    {
        DB::table('jenis_pelanggaran')->whereIn('nama', [
            'Terlambat mengikuti jamaah shalat fardhu',
            'Membuang sampah sembarangan',
            'Tidak memakai atribut / seragam lengkap',
            'Membuat kegaduhan di kamar / kelas',
            'Keluar area pesantren tanpa surat izin resmi',
            'Membawa barang elektronik (HP/MP3 player) tanpa izin',
            'Tidak mengikuti kegiatan madrasah diniyah / pengajian tanpa alasan',
            'Berinteraksi berlebihan dengan lawan jenis non-mahram',
            'Mencuri barang milik orang lain',
            'Berkelahi / melakukan kekerasan fisik',
            'Mengonsumsi rokok di lingkungan pesantren',
            'Kabur / meninggalkan pesantren tanpa izin berhari-hari',
        ])->delete();

        DB::table('role_permission')->delete();
        DB::table('permissions')->whereIn('nama', [
            'master-data.view',
            'master-data.manage',
            'peserta-didik.view',
            'peserta-didik.manage',
            'akademik.view',
            'akademik.manage',
            'keuangan.view',
            'keuangan.manage',
        ])->delete();

        DB::table('roles')->whereIn('nama', ['OPERATOR_LEMBAGA', 'GURU', 'PANITIA_PSB', 'BENDAHARA'])->delete();
        DB::table('tahun_pelajaran')->where('nama', '2026/2027')->delete();
        DB::table('lembaga')->whereIn('nama', [
            'Madrasah Tsanawiyah (MTs) Nurul Furqon',
            'Madrasah Aliyah (MA) Nurul Furqon',
            'Madrasah Diniyah (Madin) Nurul Furqon',
        ])->delete();
        DB::table('pesantren')->where('nspp', '510035170001')->delete();
    }
};
