<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatStatusSantri extends Model
{
    protected $table = 'riwayat_status_santri';

    protected $fillable = [
        'peserta_didik_id', 'status_lama', 'status_baru',
        'tanggal_perubahan', 'keterangan', 'diubah_oleh'
    ];

    protected $casts = [
        'tanggal_perubahan' => 'date',
    ];

    public function pesertaDidik(): BelongsTo
    {
        return $this->belongsTo(PesertaDidik::class);
    }

    public function pengubah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }
}
