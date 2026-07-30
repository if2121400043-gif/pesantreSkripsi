<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kamar extends Model
{
    protected $table = 'kamar';
    protected $fillable = [
        'asrama_id', 'nama', 'lantai', 'kapasitas', 'is_active'
    ];

    public function asrama(): BelongsTo
    {
        return $this->belongsTo(Asrama::class);
    }

    public function penghuni(): HasMany
    {
        return $this->hasMany(PesertaMukimTahun::class);
    }
}
