<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalPelajaran extends Model
{
    protected $table = 'jadwal_pelajaran';
    protected $fillable = [
        'rombel_id', 'mata_pelajaran_id', 'pegawai_id', 'hari', 'jam_mulai', 'jam_selesai'
    ];

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class);
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function tahunPelajaran()
    {
        return $this->hasOneThrough(
            TahunPelajaran::class,
            Rombel::class,
            'id',
            'id',
            'rombel_id',
            'tahun_pelajaran_id'
        );
    }

    public function getTahunPelajaranIdAttribute()
    {
        return $this->attributes['tahun_pelajaran_id'] ?? $this->rombel?->tahun_pelajaran_id;
    }
}
