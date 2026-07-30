<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatatanPelanggaran extends Model
{
    protected $table = 'catatan_pelanggaran';
    protected $fillable = [
        'peserta_didik_id', 'jenis_pelanggaran_id', 'tahun_pelajaran_id',
        'tanggal', 'keterangan', 'tindakan', 'dicatat_oleh'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function pesertaDidik(): BelongsTo
    {
        return $this->belongsTo(PesertaDidik::class);
    }

    public function jenisPelanggaran(): BelongsTo
    {
        return $this->belongsTo(JenisPelanggaran::class);
    }

    public function tahunPelajaran(): BelongsTo
    {
        return $this->belongsTo(TahunPelajaran::class);
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
