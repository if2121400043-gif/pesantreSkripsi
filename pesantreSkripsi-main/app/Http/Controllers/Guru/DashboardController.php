<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\JadwalPelajaran;
use App\Models\Rombel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get the Pegawai record for the currently authenticated user.
     */
    private function getPegawai()
    {
        $user = auth()->user();
        if (!$user || !$user->orang_id) return null;

        return Pegawai::where('orang_id', $user->orang_id)->first();
    }

    /**
     * Konversi nama hari Indonesia (Carbon) ke enum database.
     * Carbon menggunakan 'Minggu' sedangkan database menggunakan 'AHAD'.
     */
    private function hariToEnum(string $hariCarbon): string
    {
        $map = [
            'Senin'     => 'SENIN',
            'Selasa'    => 'SELASA',
            'Rabu'      => 'RABU',
            'Kamis'     => 'KAMIS',
            'Jumat'     => 'JUMAT',
            'Sabtu'     => 'SABTU',
            'Minggu'    => 'AHAD', // ⚠️ Pesantren memakai istilah 'AHAD', bukan 'MINGGU'
            // English fallbacks in case system locale is not 'id'
            'Monday'    => 'SENIN',
            'Tuesday'   => 'SELASA',
            'Wednesday' => 'RABU',
            'Thursday'  => 'KAMIS',
            'Friday'    => 'JUMAT',
            'Saturday'  => 'SABTU',
            'Sunday'    => 'AHAD',
        ];

        return $map[$hariCarbon] ?? strtoupper($hariCarbon);
    }

    public function index()
    {
        $pegawai = $this->getPegawai();
        
        if (!$pegawai) {
            // Fallback if the user has GURU role but no Pegawai record
            return view('guru.dashboard_empty');
        }

        $hariIni = Carbon::now()->locale('id')->dayName; // e.g., 'Senin', 'Minggu'
        $hariEnum = $this->hariToEnum($hariIni); // e.g., 'SENIN', 'AHAD'

        // Get schedules for today
        $jadwalHariIni = JadwalPelajaran::with(['mataPelajaran', 'rombel'])
            ->where('pegawai_id', $pegawai->id)
            ->where('hari', $hariEnum)
            ->orderBy('jam_mulai')
            ->get();

        // Get total classes taught
        $totalKelasDiajar = JadwalPelajaran::where('pegawai_id', $pegawai->id)
            ->distinct('rombel_id')
            ->count('rombel_id');

        // Check if teacher is wali kelas
        $waliKelas = Rombel::where('wali_kelas_id', $pegawai->id)->get();

        return view('guru.dashboard', compact('pegawai', 'jadwalHariIni', 'totalKelasDiajar', 'waliKelas', 'hariIni', 'hariEnum'));
    }
}
