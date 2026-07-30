<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rombel extends Model
{
    protected $table = 'rombel';
    protected $fillable = [
        'lembaga_id', 'tahun_pelajaran_id', 'nama', 'tingkat', 'wali_kelas_id', 'kapasitas', 'status', 'gender_target'
    ];

    public function lembaga(): BelongsTo
    {
        return $this->belongsTo(Lembaga::class);
    }

    public function tahunPelajaran(): BelongsTo
    {
        return $this->belongsTo(TahunPelajaran::class);
    }

    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'wali_kelas_id');
    }

    public function riwayatPeserta(): HasMany
    {
        return $this->hasMany(RiwayatRombelPeserta::class);
    }

    public function jadwalPelajaran(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class);
    }
}
