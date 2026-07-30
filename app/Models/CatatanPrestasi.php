<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatatanPrestasi extends Model
{
    protected $table = 'catatan_prestasi';
    protected $fillable = [
        'peserta_didik_id', 'tahun_pelajaran_id', 'judul', 'tingkat', 'tanggal', 'keterangan'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function pesertaDidik(): BelongsTo
    {
        return $this->belongsTo(PesertaDidik::class);
    }

    public function tahunPelajaran(): BelongsTo
    {
        return $this->belongsTo(TahunPelajaran::class);
    }
}
