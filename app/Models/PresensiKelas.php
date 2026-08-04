<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @deprecated Gunakan model Attendance, AttendanceLog, dan AttendanceCorrection.
 * Model ini dipertahankan untuk backward compatibility dengan tabel presensi_kelas legacy.
 * Tabel presensi_kelas TIDAK dihapus, tetapi tidak digunakan oleh flow baru.
 */
class PresensiKelas extends Model
{
    protected $table = 'presensi_kelas';
    protected $fillable = [
        'peserta_didik_id', 'jenis_presensi_id', 'rombel_id', 'asrama_id',
        'tanggal', 'status', 'keterangan', 'dicatat_oleh'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function pesertaDidik(): BelongsTo
    {
        return $this->belongsTo(PesertaDidik::class);
    }

    public function jenisPresensi(): BelongsTo
    {
        return $this->belongsTo(JenisPresensi::class);
    }

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class);
    }

    public function asrama(): BelongsTo
    {
        return $this->belongsTo(Asrama::class);
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
