<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataKesehatan extends Model
{
    protected $table = 'data_kesehatan';
    protected $fillable = [
        'orang_id', 'riwayat_penyakit', 'alergi', 'obat_rutin', 'golongan_darah_rhesus',
        'memiliki_disabilitas', 'tingkat_disabilitas', 'jenis_disabilitas',
        'kebutuhan_khusus', 'nama_kontak_darurat', 'telepon_kontak_darurat', 'hubungan_kontak_darurat'
    ];

    protected $casts = [
        'memiliki_disabilitas' => 'boolean',
    ];

    public function orang(): BelongsTo
    {
        return $this->belongsTo(Orang::class);
    }
}
