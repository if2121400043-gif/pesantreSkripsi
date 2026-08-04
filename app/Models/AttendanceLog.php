<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AttendanceLog — Layer 1: Immutable Event Store
 * 
 * Setiap event presensi dicatat di sini dan TIDAK BOLEH di-update atau di-delete.
 * Jika ada koreksi, buat record baru di attendance_corrections, bukan mengubah log ini.
 */
class AttendanceLog extends Model
{
    protected $table = 'attendance_logs';

    protected $fillable = [
        'student_id',
        'attendance_date',
        'event_type',
        'event_time',
        'status',
        'jenis_presensi_id',
        'rombel_id',
        'asrama_id',
        'keterangan',
        'device',
        'ip_address',
        'user_agent',
        'latitude',
        'longitude',
        'photo',
        'created_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'event_time' => 'datetime:H:i:s',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function pesertaDidik(): BelongsTo
    {
        return $this->belongsTo(PesertaDidik::class, 'student_id');
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    public function scopeForDate($query, string $date)
    {
        return $query->whereDate('attendance_date', $date);
    }

    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForSession($query, int $jenisPresensiId)
    {
        return $query->where('jenis_presensi_id', $jenisPresensiId);
    }

    public function scopeForRombel($query, int $rombelId)
    {
        return $query->where('rombel_id', $rombelId);
    }

    public function scopeForAsrama($query, int $asramaId)
    {
        return $query->where('asrama_id', $asramaId);
    }
}
