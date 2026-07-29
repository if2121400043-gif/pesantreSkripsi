<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatJabatanPegawai extends Model
{
    protected $table = 'riwayat_jabatan_pegawai';

    protected $fillable = [
        'pegawai_id', 'jabatan', 'jenis_pegawai', 'status_kepegawaian',
        'tanggal_mulai', 'tanggal_selesai', 'keterangan'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }
}
