<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request untuk input presensi oleh Admin (multi-jenis: KBM, Shalat, Asrama, dll).
 */
class StoreAdminAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization ditangani oleh middleware CheckRole
    }

    public function rules(): array
    {
        return [
            'jenis_presensi_id' => 'required|exists:jenis_presensi,id',
            'tanggal' => 'required|date|before_or_equal:today',
            'presensi' => 'required|array|min:1',
            'presensi.*.status' => 'required|string|in:HADIR,SAKIT,IZIN,ALPA,ALPHA',
            'presensi.*.keterangan' => 'nullable|string|max:500',
            'rombel_id' => 'nullable|exists:rombel,id',
            'asrama_id' => 'nullable|exists:asrama,id',
        ];
    }

    public function messages(): array
    {
        return [
            'jenis_presensi_id.required' => 'Jenis presensi wajib dipilih.',
            'jenis_presensi_id.exists' => 'Jenis presensi tidak ditemukan.',
            'tanggal.required' => 'Tanggal presensi wajib diisi.',
            'tanggal.before_or_equal' => 'Tanggal presensi tidak boleh di masa depan.',
            'presensi.required' => 'Data presensi wajib diisi.',
            'presensi.min' => 'Minimal satu data presensi harus diisi.',
            'presensi.*.status.required' => 'Status presensi wajib dipilih.',
            'presensi.*.status.in' => 'Status presensi tidak valid.',
            'presensi.*.keterangan.max' => 'Keterangan maksimal 500 karakter.',
            'rombel_id.exists' => 'Kelas tidak ditemukan.',
            'asrama_id.exists' => 'Asrama tidak ditemukan.',
        ];
    }

    /**
     * Normalize status values after validation.
     */
    public function normalizedPresensi(): array
    {
        $presensi = $this->input('presensi', []);

        foreach ($presensi as $studentId => &$data) {
            if (strtoupper($data['status']) === 'ALPHA') {
                $data['status'] = 'ALPA';
            }
        }

        return $presensi;
    }
}
