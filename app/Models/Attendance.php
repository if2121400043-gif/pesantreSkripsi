<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Attendance — Layer 2: Daily Summary
 * 
 * Satu record per siswa per jenis_presensi per hari.
 * Merupakan "source of truth" untuk laporan dan tampilan.
 * Update hanya dilakukan melalui AttendanceService yang juga mencatat correction.
 */
class Attendance extends Model
{
    protected $table = 'attendance';

    protected $fillable = [
        'student_id',
        'attendance_date',
        'jenis_presensi_id',
        'rombel_id',
        'asrama_id',
        'status',
        'check_in',
        'check_out',
        'late_minutes',
        'early_leave_minutes',
        'working_minutes',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in' => 'datetime:H:i:s',
        'check_out' => 'datetime:H:i:s',
        'late_minutes' => 'integer',
        'early_leave_minutes' => 'integer',
        'working_minutes' => 'integer',
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

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * All corrections ever made to this attendance record.
     */
    public function corrections(): HasMany
    {
        return $this->hasMany(AttendanceCorrection::class)->orderBy('created_at', 'desc');
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

    public function scopeForDateRange($query, string $start, string $end)
    {
        return $query->whereBetween('attendance_date', [$start, $end]);
    }

    public function scopeForMonth($query, int $year, int $month)
    {
        return $query->whereYear('attendance_date', $year)
                     ->whereMonth('attendance_date', $month);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * Check if this record has been corrected at least once.
     */
    public function hasCorrections(): bool
    {
        return $this->corrections()->exists();
    }
}
