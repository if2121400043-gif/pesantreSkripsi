<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TahunPelajaran extends Model
{
    protected $table = 'tahun_pelajaran';
    protected $fillable = [
        'pesantren_id', 'nama', 'tanggal_mulai', 'tanggal_selesai', 'is_active'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
    ];

    public function pesantren(): BelongsTo
    {
        return $this->belongsTo(Pesantren::class);
    }
}
