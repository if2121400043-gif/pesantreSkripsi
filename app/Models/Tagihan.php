<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tagihan extends Model
{
    protected $table = 'tagihan';
    protected $fillable = [
        'peserta_didik_id', 'komponen_biaya_id', 'tahun_pelajaran_id', 'bulan',
        'nominal', 'diskon', 'total', 'status', 'jatuh_tempo'
    ];

    protected $casts = [
        'jatuh_tempo' => 'date',
    ];

    public function pesertaDidik(): BelongsTo
    {
        return $this->belongsTo(PesertaDidik::class);
    }

    public function komponenBiaya(): BelongsTo
    {
        return $this->belongsTo(KomponenBiaya::class);
    }

    public function tahunPelajaran(): BelongsTo
    {
        return $this->belongsTo(TahunPelajaran::class);
    }

    public function pembayaran(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    /**
     * Memperbarui status tagihan berdasarkan pembayaran yang valid.
     * Mengembalikan nilai sisa tagihan terbaru.
     */
    public function refreshPaymentStatus(): float
    {
        // Hanya hitung pembayaran kasir (midtrans_status IS NULL)
        // atau pembayaran Midtrans yang benar-benar sukses (settlement/capture)
        $validStatuses = ['settlement', 'capture'];

        if ($this->relationLoaded('pembayaran')) {
            $totalDibayar = $this->pembayaran
                ->filter(fn ($pembayaran) => $pembayaran->midtrans_status === null
                    || in_array($pembayaran->midtrans_status, $validStatuses, true))
                ->sum('jumlah');
        } else {
            $totalDibayar = $this->pembayaran()
                ->where(function ($query) use ($validStatuses) {
                    $query->whereNull('midtrans_status')
                          ->orWhereIn('midtrans_status', $validStatuses);
                })
                ->sum('jumlah');
        }

        $sisaTagihan = max(0, (float) $this->total - (float) $totalDibayar);

        $newStatus = $sisaTagihan <= 0 ? 'LUNAS' : ($totalDibayar > 0 ? 'SEBAGIAN' : 'BELUM_BAYAR');

        if ($this->status !== $newStatus) {
            $this->status = $newStatus;
            if ($this->exists) {
                $this->save();
            }
        }

        return $sisaTagihan;
    }
}
