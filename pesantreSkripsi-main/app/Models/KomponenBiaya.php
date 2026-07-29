<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KomponenBiaya extends Model
{
    protected $table = 'komponen_biaya';
    protected $fillable = [
        'pesantren_id', 'nama', 'jenis', 'nominal', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function pesantren(): BelongsTo
    {
        return $this->belongsTo(Pesantren::class);
    }
}
