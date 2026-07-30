<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create table wilayah_pesantren (Zona / Daerah Pesantren)
        Schema::create('wilayah_pesantren', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesantren_id')->constrained('pesantren')->cascadeOnDelete();
            $table->string('nama', 100);
            $table->string('kode', 20)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P', 'CAMPURAN'])->default('L');
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Add wilayah_pesantren_id to asrama table
        Schema::table('asrama', function (Blueprint $table) {
            $table->foreignId('wilayah_pesantren_id')->nullable()->after('pesantren_id')->constrained('wilayah_pesantren')->nullOnDelete();
        });

        // Seed default Wilayah Pesantren for existing data
        $pesantrenId = DB::table('pesantren')->value('id');
        if ($pesantrenId) {
            $wilayahPutraId = DB::table('wilayah_pesantren')->insertGetId([
                'pesantren_id' => $pesantrenId,
                'nama' => 'Wilayah Sunan Giri (Putra)',
                'kode' => 'WSG-L',
                'jenis_kelamin' => 'L',
                'keterangan' => 'Kompleks Asrama Santri Putra Pusat',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $wilayahPutriId = DB::table('wilayah_pesantren')->insertGetId([
                'pesantren_id' => $pesantrenId,
                'nama' => 'Wilayah Khadijah (Putri)',
                'kode' => 'WKH-P',
                'jenis_kelamin' => 'P',
                'keterangan' => 'Kompleks Asrama Santri Putri',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign existing asrama to these Wilayah
            DB::table('asrama')->where('jenis_kelamin', 'L')->update(['wilayah_pesantren_id' => $wilayahPutraId]);
            DB::table('asrama')->where('jenis_kelamin', 'P')->update(['wilayah_pesantren_id' => $wilayahPutriId]);
            DB::table('asrama')->whereNull('wilayah_pesantren_id')->update(['wilayah_pesantren_id' => $wilayahPutraId]);
        }
    }

    public function down(): void
    {
        Schema::table('asrama', function (Blueprint $table) {
            $table->dropForeign(['wilayah_pesantren_id']);
            $table->dropColumn('wilayah_pesantren_id');
        });

        Schema::dropIfExists('wilayah_pesantren');
    }
};
