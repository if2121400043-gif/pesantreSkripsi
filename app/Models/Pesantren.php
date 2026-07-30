<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pesantren extends Model
{
    protected $table = 'pesantren';
    protected $fillable = [
        'nama', 'nspp', 'alamat', 'desa_id', 'kode_pos', 'telepon', 
        'email', 'website', 'logo', 'nama_pimpinan', 'tahun_berdiri', 'visi', 'misi', 'sejarah'
    ];

    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class);
    }

    public function lembaga(): HasMany
    {
        return $this->hasMany(Lembaga::class);
    }

    public function tahunPelajaran(): HasMany
    {
        return $this->hasMany(TahunPelajaran::class);
    }

    public function asrama(): HasMany
    {
        return $this->hasMany(Asrama::class);
    }
}
