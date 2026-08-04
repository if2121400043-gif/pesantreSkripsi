<?php

namespace App\Services;

use App\Events\AttendanceCorrected;
use App\Events\AttendanceRecorded;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\AttendanceLog;
use App\Models\JenisPresensi;
use App\Models\PesertaDidik;
use App\Models\TahunPelajaran;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * AttendanceService — Central business logic for the 3-layer attendance system.
 *
 * Semua operasi presensi HARUS melalui service ini.
 * Controller tidak boleh langsung manipulasi model Attendance/AttendanceLog.
 */
class AttendanceService
{
    // =========================================================================
    // Public API
    // =========================================================================

    /**
     * Record attendance for a single student.
     *
     * Creates an immutable log (Layer 1) and upserts the daily summary (Layer 2).
     *
     * @param  int         $studentId        ID peserta_didik
     * @param  int         $jenisPresensiId  ID jenis_presensi
     * @param  string      $date             Tanggal (Y-m-d)
     * @param  string      $status           HADIR|SAKIT|IZIN|ALPA
     * @param  array       $metadata         Additional data: rombel_id, asrama_id, keterangan, ip_address, user_agent, device
     * @param  int         $userId           ID user yang mencatat
     * @return Attendance  The daily summary record
     *
     * @throws \RuntimeException jika business rule violation
     */
    public function recordAttendance(
        int $studentId,
        int $jenisPresensiId,
        string $date,
        string $status,
        array $metadata,
        int $userId
    ): Attendance {
        // Normalize status
        $status = $this->normalizeStatus($status);

        // Validate business rules
        $this->validateBusinessRules($studentId, $jenisPresensiId, $date);

        return DB::transaction(function () use ($studentId, $jenisPresensiId, $date, $status, $metadata, $userId) {

            // Layer 1: Create immutable log
            $log = $this->createAttendanceLog(
                $studentId, $jenisPresensiId, $date, $status, $metadata, $userId
            );

            // Layer 2: Upsert daily summary
            $attendance = $this->upsertAttendanceSummary(
                $studentId, $jenisPresensiId, $date, $status, $metadata, $userId
            );

            // Fire event
            event(new AttendanceRecorded($attendance, $log));

            return $attendance;
        });
    }

    /**
     * Record attendance for multiple students in bulk (e.g., class roll call).
     *
     * Wraps everything in a single database transaction for atomicity.
     *
     * @param  array  $studentsData  Array of [student_id => ['status' => ..., 'keterangan' => ...]]
     * @param  int    $jenisPresensiId
     * @param  string $date
     * @param  array  $sharedMetadata  rombel_id, asrama_id, ip_address, user_agent, device
     * @param  int    $userId
     * @return Collection<Attendance>
     */
    public function bulkRecordAttendance(
        array $studentsData,
        int $jenisPresensiId,
        string $date,
        array $sharedMetadata,
        int $userId
    ): Collection {
        return DB::transaction(function () use ($studentsData, $jenisPresensiId, $date, $sharedMetadata, $userId) {

            $results = collect();

            foreach ($studentsData as $studentId => $data) {
                $status = $this->normalizeStatus($data['status']);
                $metadata = array_merge($sharedMetadata, [
                    'keterangan' => $data['keterangan'] ?? null,
                ]);

                try {
                    $attendance = $this->recordAttendance(
                        (int) $studentId,
                        $jenisPresensiId,
                        $date,
                        $status,
                        $metadata,
                        $userId
                    );
                    $results->push($attendance);
                } catch (\Exception $e) {
                    Log::warning("Bulk attendance skip for student {$studentId}: {$e->getMessage()}");
                    // Continue processing other students
                }
            }

            return $results;
        });
    }

    /**
     * Correct an existing attendance record.
     *
     * Creates a correction record (Layer 3), updates the summary (Layer 2),
     * and creates a new log entry (Layer 1) for the correction event.
     *
     * @param  Attendance $attendance  The existing attendance summary to correct
     * @param  array      $newData     ['status' => ..., 'keterangan' => ..., 'check_in' => ..., 'check_out' => ...]
     * @param  string     $reason      Reason for the correction
     * @param  int        $userId      ID user yang melakukan koreksi
     * @param  array      $metadata    ip_address, user_agent
     * @return AttendanceCorrection
     */
    public function correctAttendance(
        Attendance $attendance,
        array $newData,
        string $reason,
        int $userId,
        array $metadata = []
    ): AttendanceCorrection {
        $newStatus = $this->normalizeStatus($newData['status'] ?? $attendance->status);

        return DB::transaction(function () use ($attendance, $newData, $newStatus, $reason, $userId, $metadata) {

            // Layer 3: Create correction record with old values snapshot
            $correction = AttendanceCorrection::create([
                'attendance_id' => $attendance->id,
                'old_status' => $attendance->status,
                'new_status' => $newStatus,
                'old_check_in' => $attendance->check_in,
                'new_check_in' => $newData['check_in'] ?? $attendance->check_in,
                'old_check_out' => $attendance->check_out,
                'new_check_out' => $newData['check_out'] ?? $attendance->check_out,
                'old_keterangan' => $attendance->keterangan,
                'new_keterangan' => $newData['keterangan'] ?? $attendance->keterangan,
                'reason' => $reason,
                'corrected_by' => $userId,
                'ip_address' => $metadata['ip_address'] ?? null,
                'user_agent' => $metadata['user_agent'] ?? null,
                'created_at' => now(),
            ]);

            // Layer 2: Update the summary record
            $attendance->update([
                'status' => $newStatus,
                'keterangan' => $newData['keterangan'] ?? $attendance->keterangan,
                'check_in' => $newData['check_in'] ?? $attendance->check_in,
                'check_out' => $newData['check_out'] ?? $attendance->check_out,
                'updated_by' => $userId,
            ]);

            // Layer 1: Log the correction event
            $this->createAttendanceLog(
                $attendance->student_id,
                $attendance->jenis_presensi_id,
                $attendance->attendance_date->format('Y-m-d'),
                $newStatus,
                array_merge($metadata, [
                    'rombel_id' => $attendance->rombel_id,
                    'asrama_id' => $attendance->asrama_id,
                    'keterangan' => "KOREKSI: {$reason}",
                    'device' => $metadata['device'] ?? 'WEB',
                ]),
                $userId
            );

            // Fire event
            event(new AttendanceCorrected($attendance, $correction));

            return $correction;
        });
    }

    // =========================================================================
    // Business Rule Validation
    // =========================================================================

    /**
     * Validate all business rules before recording attendance.
     *
     * @throws \RuntimeException if any rule is violated
     */
    public function validateBusinessRules(int $studentId, int $jenisPresensiId, string $date): void
    {
        $this->validateStudentActive($studentId);
        $this->validateAcademicYearActive();
        $this->validateSessionActive($jenisPresensiId);
    }

    private function validateStudentActive(int $studentId): void
    {
        $student = PesertaDidik::find($studentId);

        if (!$student) {
            throw new \RuntimeException("Peserta didik dengan ID {$studentId} tidak ditemukan.");
        }

        if ($student->status !== 'AKTIF') {
            throw new \RuntimeException(
                "Peserta didik {$student->orang?->nama_lengkap} tidak berstatus aktif (status: {$student->status})."
            );
        }
    }

    private function validateAcademicYearActive(): void
    {
        $tahun = TahunPelajaran::where('is_active', true)->first();

        if (!$tahun) {
            throw new \RuntimeException('Tidak ada tahun pelajaran yang aktif. Hubungi administrator.');
        }
    }

    private function validateSessionActive(int $jenisPresensiId): void
    {
        $jenis = JenisPresensi::find($jenisPresensiId);

        if (!$jenis) {
            throw new \RuntimeException("Jenis presensi dengan ID {$jenisPresensiId} tidak ditemukan.");
        }

        if (!$jenis->is_active) {
            throw new \RuntimeException("Jenis presensi '{$jenis->nama}' tidak aktif.");
        }
    }

    // =========================================================================
    // Private Helpers
    // =========================================================================

    /**
     * Create an immutable attendance log (Layer 1).
     */
    private function createAttendanceLog(
        int $studentId,
        int $jenisPresensiId,
        string $date,
        string $status,
        array $metadata,
        int $userId
    ): AttendanceLog {
        return AttendanceLog::create([
            'student_id' => $studentId,
            'attendance_date' => $date,
            'event_type' => 'STATUS',
            'event_time' => now()->format('H:i:s'),
            'status' => $status,
            'jenis_presensi_id' => $jenisPresensiId,
            'rombel_id' => $metadata['rombel_id'] ?? null,
            'asrama_id' => $metadata['asrama_id'] ?? null,
            'keterangan' => $metadata['keterangan'] ?? null,
            'device' => $metadata['device'] ?? 'WEB',
            'ip_address' => $metadata['ip_address'] ?? null,
            'user_agent' => $metadata['user_agent'] ?? null,
            'latitude' => $metadata['latitude'] ?? null,
            'longitude' => $metadata['longitude'] ?? null,
            'photo' => $metadata['photo'] ?? null,
            'created_by' => $userId,
        ]);
    }

    /**
     * Upsert the daily attendance summary (Layer 2).
     *
     * Jika sudah ada record untuk student+session+date, update-nya.
     * Jika belum ada, buat baru.
     */
    private function upsertAttendanceSummary(
        int $studentId,
        int $jenisPresensiId,
        string $date,
        string $status,
        array $metadata,
        int $userId
    ): Attendance {
        $existing = Attendance::where('student_id', $studentId)
            ->where('jenis_presensi_id', $jenisPresensiId)
            ->whereDate('attendance_date', $date)
            ->first();

        if ($existing) {
            // Record exists → this is effectively a correction
            // Create correction trail before updating
            if ($existing->status !== $status || $existing->keterangan !== ($metadata['keterangan'] ?? null)) {
                AttendanceCorrection::create([
                    'attendance_id' => $existing->id,
                    'old_status' => $existing->status,
                    'new_status' => $status,
                    'old_check_in' => $existing->check_in,
                    'new_check_in' => $existing->check_in,
                    'old_check_out' => $existing->check_out,
                    'new_check_out' => $existing->check_out,
                    'old_keterangan' => $existing->keterangan,
                    'new_keterangan' => $metadata['keterangan'] ?? null,
                    'reason' => 'Update via input ulang presensi',
                    'corrected_by' => $userId,
                    'ip_address' => $metadata['ip_address'] ?? null,
                    'user_agent' => $metadata['user_agent'] ?? null,
                    'created_at' => now(),
                ]);
            }

            $existing->update([
                'status' => $status,
                'rombel_id' => $metadata['rombel_id'] ?? $existing->rombel_id,
                'asrama_id' => $metadata['asrama_id'] ?? $existing->asrama_id,
                'keterangan' => $metadata['keterangan'] ?? null,
                'updated_by' => $userId,
            ]);

            return $existing->fresh();
        }

        // New record
        return Attendance::create([
            'student_id' => $studentId,
            'attendance_date' => $date,
            'jenis_presensi_id' => $jenisPresensiId,
            'rombel_id' => $metadata['rombel_id'] ?? null,
            'asrama_id' => $metadata['asrama_id'] ?? null,
            'status' => $status,
            'keterangan' => $metadata['keterangan'] ?? null,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);
    }

    /**
     * Normalize status value: ALPHA → ALPA.
     */
    private function normalizeStatus(string $status): string
    {
        $status = strtoupper(trim($status));

        if ($status === 'ALPHA') {
            $status = 'ALPA';
        }

        if (!in_array($status, ['HADIR', 'SAKIT', 'IZIN', 'ALPA'])) {
            throw new \InvalidArgumentException("Status presensi tidak valid: {$status}");
        }

        return $status;
    }
}
