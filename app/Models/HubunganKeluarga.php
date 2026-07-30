<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HubunganKeluarga extends Model
{
    protected $table = 'hubungan_keluarga';
    protected $fillable = [
        'orang_id', 'keluarga_id', 'hubungan', 'is_mahrom',
        'boleh_jemput', 'boleh_kunjungi', 'boleh_komunikasi', 'is_wali_utama', 'catatan'
    ];

    protected $casts = [
        'is_mahrom' => 'boolean',
        'boleh_jemput' => 'boolean',
        'boleh_kunjungi' => 'boolean',
        'boleh_komunikasi' => 'boolean',
        'is_wali_utama' => 'boolean',
    ];

    public function anak(): BelongsTo
    {
        return $this->belongsTo(Orang::class, 'orang_id');
    }

    public function orangTuaAtauWali(): BelongsTo
    {
        return $this->belongsTo(Orang::class, 'keluarga_id');
    }
}
