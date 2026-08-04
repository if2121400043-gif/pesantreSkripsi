<?php

namespace App\Events;

use App\Models\Attendance;
use App\Models\AttendanceLog;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event yang di-fire setiap kali presensi baru dicatat.
 */
class AttendanceRecorded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Attendance $attendance,
        public readonly AttendanceLog $log
    ) {}
}
