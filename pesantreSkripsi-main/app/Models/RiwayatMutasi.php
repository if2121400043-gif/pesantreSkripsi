<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatMutasi extends Model
{
    protected $table = 'riwayat_mutasi';

    protected $fillable = [
        'peserta_didik_id', 'tahun_pelajaran_id', 'jenis_mutasi',
        'dari_posisi', 'ke_posisi', 'tanggal_mutasi', 'keterangan', 'diinput_oleh'
    ];

    protected $casts = [
        'tanggal_mutasi' => 'date',
    ];

    public function pesertaDidik(): BelongsTo
    {
        return $this->belongsTo(PesertaDidik::class, 'peserta_didik_id');
    }

    public function tahunPelajaran(): BelongsTo
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahun_pelajaran_id');
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diinput_oleh');
    }
}
