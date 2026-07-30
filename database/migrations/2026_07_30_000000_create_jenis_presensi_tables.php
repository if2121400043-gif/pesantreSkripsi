<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_presensi', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('kode', 30)->unique();
            $table->text('deskripsi')->nullable();
            $table->enum('target_gender', ['SEMUA', 'PUTRA', 'PUTRI'])->default('SEMUA');
            $table->enum('tipe_target', ['SEMUA_SANTRI', 'PER_ROMBEL', 'PER_ASRAMA'])->default('PER_ROMBEL');
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        // Add constraints and columns to presensi_kelas
        // MySQL requires dropping foreign keys before dropping unique index they depend on
        Schema::table('presensi_kelas', function (Blueprint $table) {
            // 1. Drop foreign keys that reference columns in the unique index first
            $table->dropForeign(['rombel_id']);
            $table->dropForeign(['peserta_didik_id']);
            
            // 2. Now we can safely drop the unique index
            $table->dropUnique('presensi_unique');
        });
        
        Schema::table('presensi_kelas', function (Blueprint $table) {
            // 3. Add new columns
            $table->foreignId('jenis_presensi_id')->nullable()->constrained('jenis_presensi')->cascadeOnDelete();
            $table->foreignId('asrama_id')->nullable()->constrained('asrama')->cascadeOnDelete();
            
            // 4. Make rombel_id nullable since not all presensi are tied to a rombel
            $table->unsignedBigInteger('rombel_id')->nullable()->change();
            
            // 5. Re-add foreign keys
            $table->foreign('rombel_id')->references('id')->on('rombel')->cascadeOnDelete();
            $table->foreign('peserta_didik_id')->references('id')->on('peserta_didik')->cascadeOnDelete();
        });
        
        // Insert default data
        $now = now();
        DB::table('jenis_presensi')->insert([
            ['nama' => 'Kegiatan Belajar Mengajar', 'kode' => 'KBM', 'target_gender' => 'SEMUA', 'tipe_target' => 'PER_ROMBEL', 'urutan' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Apel Pagi', 'kode' => 'APEL_PAGI', 'target_gender' => 'SEMUA', 'tipe_target' => 'SEMUA_SANTRI', 'urutan' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Shalat Subuh', 'kode' => 'SHALAT_SUBUH', 'target_gender' => 'SEMUA', 'tipe_target' => 'PER_ASRAMA', 'urutan' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Shalat Dzuhur', 'kode' => 'SHALAT_DZUHUR', 'target_gender' => 'SEMUA', 'tipe_target' => 'PER_ASRAMA', 'urutan' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Shalat Ashar', 'kode' => 'SHALAT_ASHAR', 'target_gender' => 'SEMUA', 'tipe_target' => 'PER_ASRAMA', 'urutan' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Shalat Maghrib', 'kode' => 'SHALAT_MAGHRIB', 'target_gender' => 'SEMUA', 'tipe_target' => 'PER_ASRAMA', 'urutan' => 6, 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Shalat Isya', 'kode' => 'SHALAT_ISYA', 'target_gender' => 'SEMUA', 'tipe_target' => 'PER_ASRAMA', 'urutan' => 7, 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Kelas Kitab', 'kode' => 'KELAS_KITAB', 'target_gender' => 'SEMUA', 'tipe_target' => 'PER_ROMBEL', 'urutan' => 8, 'created_at' => $now, 'updated_at' => $now],
        ]);
        
        // Update existing records to use the default KBM
        $kbmId = DB::table('jenis_presensi')->where('kode', 'KBM')->value('id');
        DB::table('presensi_kelas')->update(['jenis_presensi_id' => $kbmId]);
        
        // Make jenis_presensi_id not nullable after populating
        Schema::table('presensi_kelas', function (Blueprint $table) {
            $table->foreignId('jenis_presensi_id')->nullable(false)->change();
            
            // Add new unique constraint
            $table->unique(['peserta_didik_id', 'jenis_presensi_id', 'tanggal'], 'presensi_unique');
        });
    }

    public function down(): void
    {
        Schema::table('presensi_kelas', function (Blueprint $table) {
            $table->dropUnique('presensi_unique');
            $table->dropForeign(['jenis_presensi_id']);
            $table->dropForeign(['asrama_id']);
            $table->dropColumn('jenis_presensi_id');
            $table->dropColumn('asrama_id');
            
            $table->foreignId('rombel_id')->nullable(false)->change();
            $table->unique(['peserta_didik_id', 'rombel_id', 'tanggal'], 'presensi_unique');
        });
        
        Schema::dropIfExists('jenis_presensi');
    }
};
