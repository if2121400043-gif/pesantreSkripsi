<?php

namespace App\Events;

use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event yang di-fire setiap kali koreksi presensi dilakukan.
 */
class AttendanceCorrected
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Attendance $attendance,
        public readonly AttendanceCorrection $correction
    ) {}
}
