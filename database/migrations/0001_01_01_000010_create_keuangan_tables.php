<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Komponen Biaya
        Schema::create('komponen_biaya', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesantren_id')->constrained('pesantren')->cascadeOnDelete();
            $table->string('nama', 100)->comment('SPP, Uang Pangkal, Seragam');
            $table->enum('jenis', ['BULANAN', 'TAHUNAN', 'SEKALI'])->default('BULANAN');
            $table->decimal('nominal', 14, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tagihan per Peserta
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_didik_id')->constrained('peserta_didik')->cascadeOnDelete();
            $table->foreignId('komponen_biaya_id')->constrained('komponen_biaya')->cascadeOnDelete();
            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajaran')->cascadeOnDelete();
            $table->string('bulan', 7)->nullable()->comment('Format: 2026-01 untuk bulanan');
            $table->decimal('nominal', 14, 2);
            $table->decimal('diskon', 14, 2)->default(0);
            $table->decimal('total', 14, 2);
            $table->enum('status', ['BELUM_BAYAR', 'SEBAGIAN', 'LUNAS'])->default('BELUM_BAYAR');
            $table->date('jatuh_tempo')->nullable();
            $table->timestamps();

            $table->index(['peserta_didik_id', 'status']);
        });

        // Transaksi Pembayaran
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_id')->constrained('tagihan')->cascadeOnDelete();
            $table->string('no_transaksi', 30)->unique();
            $table->decimal('jumlah', 14, 2);
            $table->enum('metode', ['TUNAI', 'TRANSFER', 'QRIS', 'LAINNYA'])->default('TUNAI');
            $table->string('bukti_bayar')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('kasir_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tanggal_bayar');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('tagihan');
        Schema::dropIfExists('komponen_biaya');
    }
};
