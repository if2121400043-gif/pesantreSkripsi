<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerizinanKeluar extends Model
{
    protected $table = 'perizinan_keluar';
    protected $fillable = [
        'peserta_didik_id', 'jenis', 'waktu_keluar', 'waktu_kembali_rencana',
        'waktu_kembali_aktual', 'dijemput_oleh', 'hubungan_penjemput', 'alasan',
        'status', 'disetujui_oleh', 'tanggal_persetujuan', 'catatan_persetujuan'
    ];

    protected $casts = [
        'waktu_keluar' => 'datetime',
        'waktu_kembali_rencana' => 'datetime',
        'waktu_kembali_aktual' => 'datetime',
        'tanggal_persetujuan' => 'datetime',
    ];

    public function pesertaDidik(): BelongsTo
    {
        return $this->belongsTo(PesertaDidik::class);
    }

    public function penyetuju(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
}
