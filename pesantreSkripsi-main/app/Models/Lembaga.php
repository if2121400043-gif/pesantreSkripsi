<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lembaga extends Model
{
    protected $table = 'lembaga';
    protected $fillable = [
        'pesantren_id', 'nama', 'singkatan', 'jenjang', 'tipe', 'npsn', 'is_active', 'urutan'
    ];

    public function pesantren(): BelongsTo
    {
        return $this->belongsTo(Pesantren::class);
    }

    public function rombel(): HasMany
    {
        return $this->hasMany(Rombel::class);
    }

    public function mataPelajaran(): HasMany
    {
        return $this->hasMany(MataPelajaran::class);
    }
}
