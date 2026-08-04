<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AttendanceCorrection — Layer 3: Correction Audit Trail
 * 
 * Setiap koreksi presensi dicatat di sini. Record ini IMMUTABLE — 
 * sekali dibuat, tidak boleh diubah atau dihapus.
 * Menyimpan snapshot nilai lama dan baru untuk audit trail lengkap.
 */
class AttendanceCorrection extends Model
{
    public $timestamps = false; // Hanya pakai created_at

    protected $table = 'attendance_corrections';

    protected $fillable = [
        'attendance_id',
        'old_status',
        'new_status',
        'old_check_in',
        'new_check_in',
        'old_check_out',
        'new_check_out',
        'old_keterangan',
        'new_keterangan',
        'reason',
        'corrected_by',
        'approved_by',
        'approved_at',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'old_check_in' => 'datetime:H:i:s',
        'new_check_in' => 'datetime:H:i:s',
        'old_check_out' => 'datetime:H:i:s',
        'new_check_out' => 'datetime:H:i:s',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function correctedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'corrected_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * Check if this correction has been approved.
     */
    public function isApproved(): bool
    {
        return $this->approved_by !== null && $this->approved_at !== null;
    }

    /**
     * Get a human-readable summary of what changed.
     */
    public function getChangeSummaryAttribute(): string
    {
        $changes = [];

        if ($this->old_status !== $this->new_status) {
            $changes[] = "Status: {$this->old_status} → {$this->new_status}";
        }

        if ($this->old_keterangan !== $this->new_keterangan) {
            $changes[] = "Keterangan diubah";
        }

        if ($this->old_check_in != $this->new_check_in) {
            $changes[] = "Jam masuk diubah";
        }

        if ($this->old_check_out != $this->new_check_out) {
            $changes[] = "Jam keluar diubah";
        }

        return implode('; ', $changes) ?: 'Tidak ada perubahan';
    }
}
