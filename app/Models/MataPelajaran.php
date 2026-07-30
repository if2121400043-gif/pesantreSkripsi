<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MataPelajaran extends Model
{
    protected $table = 'mata_pelajaran';
    protected $fillable = [
        'lembaga_id', 'kode', 'nama', 'tingkat', 'urutan', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['nama_mapel', 'kode_mapel', 'kelompok_mapel'];

    public function getNamaMapelAttribute()
    {
        return $this->nama;
    }

    public function getKodeMapelAttribute()
    {
        return $this->kode;
    }

    public function getKelompokMapelAttribute()
    {
        return $this->tingkat;
    }

    public function lembaga(): BelongsTo
    {
        return $this->belongsTo(Lembaga::class);
    }
}
