<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GelombangPsb extends Model
{
    protected $table = 'gelombang_psb';
    protected $fillable = [
        'pesantren_id', 'tahun_pelajaran_id', 'nama', 'tanggal_buka',
        'tanggal_tutup', 'tanggal_seleksi_awal', 'tanggal_seleksi_akhir',
        'tanggal_daftar_ulang_awal', 'tanggal_daftar_ulang_akhir',
        'kuota', 'biaya_pendaftaran', 'is_active'
    ];

    protected $casts = [
        'tanggal_buka' => 'date',
        'tanggal_tutup' => 'date',
        'tanggal_seleksi_awal' => 'date',
        'tanggal_seleksi_akhir' => 'date',
        'tanggal_daftar_ulang_awal' => 'date',
        'tanggal_daftar_ulang_akhir' => 'date',
        'is_active' => 'boolean',
    ];

    public function pesantren(): BelongsTo
    {
        return $this->belongsTo(Pesantren::class);
    }

    public function tahunPelajaran(): BelongsTo
    {
        return $this->belongsTo(TahunPelajaran::class);
    }

    public function pendaftar(): HasMany
    {
        return $this->hasMany(CalonSantri::class, 'gelombang_id');
    }

    public function calonSantri(): HasMany
    {
        return $this->hasMany(CalonSantri::class, 'gelombang_id');
    }
}
