<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Desa extends Model
{
    protected $table = 'desa';
    protected $fillable = ['kode', 'nama', 'kecamatan_id'];

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function orang(): HasMany
    {
        return $this->hasMany(Orang::class);
    }

    public function pesantren(): HasMany
    {
        return $this->hasMany(Pesantren::class);
    }
}
