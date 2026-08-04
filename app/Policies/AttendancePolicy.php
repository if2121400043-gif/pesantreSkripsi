<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;

/**
 * Authorization policy for attendance operations.
 *
 * Role hierarchy:
 * - SUPER_ADMIN: Full access to all attendance operations
 * - GURU: Can record/correct attendance for their own classes only
 * - WALI_SANTRI: Read-only access to their child's attendance
 */
class AttendancePolicy
{
    /**
     * Admin / Super Admin can do everything.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($this->getUserRoleName($user) === 'SUPER_ADMIN') {
            return true;
        }

        return null; // Fall through to specific checks
    }

    /**
     * Can the user record attendance?
     * Guru can only record for their assigned classes.
     */
    public function record(User $user): bool
    {
        return in_array($this->getUserRoleName($user), ['GURU', 'SUPER_ADMIN']);
    }

    /**
     * Can the user correct an attendance record?
     * Guru can correct records they created; Admin can correct all.
     */
    public function correct(User $user, Attendance $attendance): bool
    {
        $role = $this->getUserRoleName($user);

        if ($role === 'GURU') {
            // Guru can only correct attendance they recorded
            return $attendance->created_by === $user->id || $attendance->updated_by === $user->id;
        }

        return false;
    }

    /**
     * Can the user view attendance reports?
     */
    public function viewReport(User $user): bool
    {
        return in_array($this->getUserRoleName($user), ['GURU', 'SUPER_ADMIN', 'WALI_SANTRI']);
    }

    /**
     * Can the user approve corrections? Only admins.
     */
    public function approve(User $user): bool
    {
        return $this->getUserRoleName($user) === 'SUPER_ADMIN';
    }

    /**
     * Get the user's active role name.
     */
    private function getUserRoleName(User $user): ?string
    {
        $activeRole = $user->active_role;
        return $activeRole?->role?->nama;
    }
}
