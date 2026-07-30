<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiRapor extends Model
{
    protected $table = 'nilai_rapor';
    protected $fillable = [
        'peserta_didik_id', 'rombel_id', 'mata_pelajaran_id', 'tahun_pelajaran_id',
        'semester', 'nilai_tugas', 'nilai_uts', 'nilai_uas', 'nilai_akhir', 'predikat', 'catatan_guru'
    ];

    public function pesertaDidik(): BelongsTo
    {
        return $this->belongsTo(PesertaDidik::class);
    }

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class);
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function tahunPelajaran(): BelongsTo
    {
        return $this->belongsTo(TahunPelajaran::class);
    }
}
