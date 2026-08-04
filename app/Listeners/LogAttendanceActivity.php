<?php

namespace App\Listeners;

use App\Events\AttendanceCorrected;
use App\Events\AttendanceRecorded;
use Illuminate\Support\Facades\Log;

/**
 * Listener yang mencatat aktivitas presensi ke log sistem.
 * 
 * Mendengarkan event AttendanceRecorded dan AttendanceCorrected
 * untuk audit trail di application log (storage/logs/laravel.log).
 */
class LogAttendanceActivity
{
    /**
     * Handle AttendanceRecorded event.
     */
    public function handleRecorded(AttendanceRecorded $event): void
    {
        $attendance = $event->attendance;
        $log = $event->log;

        Log::channel('daily')->info('Presensi dicatat', [
            'attendance_id' => $attendance->id,
            'student_id' => $attendance->student_id,
            'jenis_presensi_id' => $attendance->jenis_presensi_id,
            'date' => $attendance->attendance_date->format('Y-m-d'),
            'status' => $attendance->status,
            'created_by' => $attendance->created_by,
            'log_id' => $log->id,
            'device' => $log->device,
            'ip_address' => $log->ip_address,
        ]);
    }

    /**
     * Handle AttendanceCorrected event.
     */
    public function handleCorrected(AttendanceCorrected $event): void
    {
        $attendance = $event->attendance;
        $correction = $event->correction;

        Log::channel('daily')->warning('Presensi dikoreksi', [
            'attendance_id' => $attendance->id,
            'student_id' => $attendance->student_id,
            'date' => $attendance->attendance_date->format('Y-m-d'),
            'old_status' => $correction->old_status,
            'new_status' => $correction->new_status,
            'reason' => $correction->reason,
            'corrected_by' => $correction->corrected_by,
            'ip_address' => $correction->ip_address,
        ]);
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe($events): array
    {
        return [
            AttendanceRecorded::class => 'handleRecorded',
            AttendanceCorrected::class => 'handleCorrected',
        ];
    }
}
