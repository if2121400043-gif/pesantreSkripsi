<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JenisPelanggaran extends Model
{
    protected $table = 'jenis_pelanggaran';
    protected $fillable = [
        'pesantren_id', 'nama', 'kategori', 'poin'
    ];

    public function pesantren(): BelongsTo
    {
        return $this->belongsTo(Pesantren::class);
    }
}
