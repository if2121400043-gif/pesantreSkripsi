<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $fillable = [
        'tagihan_id', 'no_transaksi', 'jumlah', 'metode', 'bukti_bayar',
        'keterangan', 'kasir_id', 'tanggal_bayar',
        'midtrans_order_id', 'midtrans_transaction_id', 'midtrans_payment_type',
        'midtrans_status', 'midtrans_response',
    ];

    protected $casts = [
        'tanggal_bayar' => 'datetime',
        'midtrans_response' => 'array',
    ];

    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(Tagihan::class);
    }

    public function kasir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }
}
