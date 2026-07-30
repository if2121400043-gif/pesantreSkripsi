<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asrama extends Model
{
    protected $table = 'asrama';
    protected $fillable = [
        'pesantren_id', 'nama', 'kode', 'jenis_kelamin', 'kapasitas', 'keterangan', 'is_active'
    ];

    public function pesantren(): BelongsTo
    {
        return $this->belongsTo(Pesantren::class);
    }

    public function kamar(): HasMany
    {
        return $this->hasMany(Kamar::class);
    }
}
