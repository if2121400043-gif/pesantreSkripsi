<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresensiKelas extends Model
{
    protected $table = 'presensi_kelas';
    protected $fillable = [
        'peserta_didik_id', 'rombel_id', 'tanggal', 'status', 'keterangan', 'dicatat_oleh'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function pesertaDidik(): BelongsTo
    {
        return $this->belongsTo(PesertaDidik::class);
    }

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class);
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
