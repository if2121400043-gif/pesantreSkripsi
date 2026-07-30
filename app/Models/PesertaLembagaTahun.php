<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesertaLembagaTahun extends Model
{
    protected $table = 'peserta_lembaga_tahun';
    protected $fillable = [
        'peserta_didik_id', 'lembaga_id', 'tahun_pelajaran_id', 'status', 'catatan'
    ];

    public function pesertaDidik(): BelongsTo
    {
        return $this->belongsTo(PesertaDidik::class);
    }

    public function lembaga(): BelongsTo
    {
        return $this->belongsTo(Lembaga::class);
    }

    public function tahunPelajaran(): BelongsTo
    {
        return $this->belongsTo(TahunPelajaran::class);
    }
}
