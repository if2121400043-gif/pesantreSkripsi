<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // =====================================================================
        // Layer 1: attendance_logs — Immutable event store
        // Every scan/event submitted by users. Never updated or deleted.
        // =====================================================================
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('peserta_didik')->restrictOnDelete();
            $table->date('attendance_date');
            $table->enum('event_type', ['STATUS', 'IN', 'OUT'])->default('STATUS');
            $table->time('event_time')->nullable();
            $table->enum('status', ['HADIR', 'SAKIT', 'IZIN', 'ALPA'])->default('HADIR');
            $table->foreignId('jenis_presensi_id')->constrained('jenis_presensi')->restrictOnDelete();
            $table->foreignId('rombel_id')->nullable()->constrained('rombel')->nullOnDelete();
            $table->foreignId('asrama_id')->nullable()->constrained('asrama')->nullOnDelete();
            $table->text('keterangan')->nullable();

            // Device tracking (nullable for extensibility — QR, RFID, GPS, Face Recognition)
            $table->string('device', 50)->nullable()->comment('e.g. WEB, QR, RFID, FACE_RECOGNITION');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('photo')->nullable()->comment('Path to photo evidence');

            // Audit trail
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            // Indexes for performance
            $table->index(['student_id', 'attendance_date'], 'idx_log_student_date');
            $table->index(['jenis_presensi_id', 'attendance_date'], 'idx_log_session_date');
            $table->index('attendance_date', 'idx_log_date');
        });

        // =====================================================================
        // Layer 2: attendance — Daily summary per student per session
        // One record per student per jenis_presensi per day.
        // =====================================================================
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('peserta_didik')->restrictOnDelete();
            $table->date('attendance_date');
            $table->foreignId('jenis_presensi_id')->constrained('jenis_presensi')->restrictOnDelete();
            $table->foreignId('rombel_id')->nullable()->constrained('rombel')->nullOnDelete();
            $table->foreignId('asrama_id')->nullable()->constrained('asrama')->nullOnDelete();
            $table->enum('status', ['HADIR', 'SAKIT', 'IZIN', 'ALPA'])->default('HADIR');

            // Hybrid check-in/check-out (nullable — only used for sessions that require it)
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->unsignedSmallInteger('late_minutes')->default(0);
            $table->unsignedSmallInteger('early_leave_minutes')->default(0);
            $table->unsignedSmallInteger('working_minutes')->default(0);

            $table->text('keterangan')->nullable();

            // Audit trail
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // One summary per student per session per day
            $table->unique(['student_id', 'jenis_presensi_id', 'attendance_date'], 'attendance_unique');

            // Indexes for performance
            $table->index(['attendance_date'], 'idx_attendance_date');
            $table->index(['rombel_id', 'attendance_date'], 'idx_attendance_rombel_date');
            $table->index(['asrama_id', 'attendance_date'], 'idx_attendance_asrama_date');
        });

        // =====================================================================
        // Layer 3: attendance_corrections — Correction audit trail
        // Every manual correction is logged here. Original data preserved.
        // =====================================================================
        Schema::create('attendance_corrections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained('attendance')->cascadeOnDelete();

            // Old values (snapshot before correction)
            $table->enum('old_status', ['HADIR', 'SAKIT', 'IZIN', 'ALPA']);
            $table->time('old_check_in')->nullable();
            $table->time('old_check_out')->nullable();
            $table->text('old_keterangan')->nullable();

            // New values (after correction)
            $table->enum('new_status', ['HADIR', 'SAKIT', 'IZIN', 'ALPA']);
            $table->time('new_check_in')->nullable();
            $table->time('new_check_out')->nullable();
            $table->text('new_keterangan')->nullable();

            // Correction metadata
            $table->text('reason')->comment('Reason for the correction');
            $table->foreignId('corrected_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            // Device/security tracking
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();

            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index('attendance_id', 'idx_correction_attendance');
        });

        // =====================================================================
        // Data Migration: presensi_kelas → attendance_logs + attendance
        // Preserves all existing data. presensi_kelas table is NOT dropped.
        // =====================================================================
        $this->migrateExistingData();
    }

    /**
     * Migrate existing presensi_kelas data to the new 3-layer structure.
     */
    private function migrateExistingData(): void
    {
        $existingRecords = DB::table('presensi_kelas')->get();

        if ($existingRecords->isEmpty()) {
            return;
        }

        $logInserts = [];
        $summaryInserts = [];
        $now = now();

        foreach ($existingRecords as $record) {
            // Normalize status: ALPHA → ALPA
            $status = $record->status === 'ALPHA' ? 'ALPA' : $record->status;

            // Layer 1: Insert into attendance_logs (immutable event record)
            $logInserts[] = [
                'student_id' => $record->peserta_didik_id,
                'attendance_date' => $record->tanggal,
                'event_type' => 'STATUS',
                'event_time' => null,
                'status' => $status,
                'jenis_presensi_id' => $record->jenis_presensi_id,
                'rombel_id' => $record->rombel_id,
                'asrama_id' => $record->asrama_id ?? null,
                'keterangan' => $record->keterangan,
                'device' => 'WEB',
                'ip_address' => null,
                'user_agent' => 'Legacy Migration',
                'latitude' => null,
                'longitude' => null,
                'photo' => null,
                'created_by' => $record->dicatat_oleh ?? 1,
                'created_at' => $record->created_at ?? $now,
                'updated_at' => $record->updated_at ?? $now,
            ];

            // Layer 2: Insert into attendance (daily summary)
            $summaryInserts[] = [
                'student_id' => $record->peserta_didik_id,
                'attendance_date' => $record->tanggal,
                'jenis_presensi_id' => $record->jenis_presensi_id,
                'rombel_id' => $record->rombel_id,
                'asrama_id' => $record->asrama_id ?? null,
                'status' => $status,
                'check_in' => null,
                'check_out' => null,
                'late_minutes' => 0,
                'early_leave_minutes' => 0,
                'working_minutes' => 0,
                'keterangan' => $record->keterangan,
                'created_by' => $record->dicatat_oleh ?? 1,
                'updated_by' => $record->dicatat_oleh ?? 1,
                'created_at' => $record->created_at ?? $now,
                'updated_at' => $record->updated_at ?? $now,
            ];
        }

        // Batch insert in chunks of 500 for performance
        foreach (array_chunk($logInserts, 500) as $chunk) {
            DB::table('attendance_logs')->insert($chunk);
        }

        foreach (array_chunk($summaryInserts, 500) as $chunk) {
            DB::table('attendance')->insert($chunk);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_corrections');
        Schema::dropIfExists('attendance');
        Schema::dropIfExists('attendance_logs');
    }
};
