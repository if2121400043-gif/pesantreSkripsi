<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenPsb extends Model
{
    protected $table = 'dokumen_psb';
    protected $fillable = [
        'calon_santri_id', 'jenis_dokumen', 'file_path', 'is_verified'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    public function calonSantri(): BelongsTo
    {
        return $this->belongsTo(CalonSantri::class);
    }
}
