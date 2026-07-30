<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WilayahPesantren extends Model
{
    protected $table = 'wilayah_pesantren';
    
    protected $fillable = [
        'pesantren_id', 'nama', 'kode', 'jenis_kelamin', 'keterangan', 'is_active'
    ];

    public function pesantren(): BelongsTo
    {
        return $this->belongsTo(Pesantren::class);
    }

    public function asrama(): HasMany
    {
        return $this->hasMany(Asrama::class, 'wilayah_pesantren_id');
    }
}
