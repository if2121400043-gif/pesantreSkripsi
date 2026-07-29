<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesertaMukimTahun extends Model
{
    protected $table = 'peserta_mukim_tahun';
    protected $fillable = [
        'peserta_didik_id', 'tahun_pelajaran_id', 'kamar_id', 'status_mukim',
        'tanggal_masuk', 'tanggal_keluar', 'keterangan'
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
    ];

    public function pesertaDidik(): BelongsTo
    {
        return $this->belongsTo(PesertaDidik::class);
    }

    public function tahunPelajaran(): BelongsTo
    {
        return $this->belongsTo(TahunPelajaran::class);
    }

    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class);
    }

    public function getIsAktifAttribute(): bool
    {
        return $this->status_mukim === 'MUKIM' && is_null($this->tanggal_keluar);
    }
}
