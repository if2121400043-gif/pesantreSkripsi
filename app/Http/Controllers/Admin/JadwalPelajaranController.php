<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\Rombel;
use App\Models\MataPelajaran;
use App\Models\Pegawai;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;

class JadwalPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $tahuns = TahunPelajaran::orderBy('tanggal_mulai', 'desc')->get();
        $tahunId = $request->get('tahun_pelajaran_id');

        // Default ke tahun aktif jika tidak dipilih
        if (!$tahunId) {
            $tahunAktif = TahunPelajaran::where('is_active', true)->first();
            $tahunId = $tahunAktif?->id;
        }

        $rombels = Rombel::with('lembaga', 'tahunPelajaran')
            ->when($tahunId, fn($q) => $q->where('tahun_pelajaran_id', $tahunId))
            ->orderBy('nama')
            ->get();

        $gurus = Pegawai::with('orang')
            ->where('is_active', true)
            ->whereIn('jenis_pegawai', ['GURU', 'USTADZ', 'PENGASUH'])
            ->get();

        $daftarHari = ['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'AHAD'];

        $hari = $request->get('hari');
        $rombelId = $request->get('rombel_id');
        $pegawaiId = $request->get('pegawai_id');

        // Query Jadwal Pelajaran based on 3 filters (Hari, Kelas, Guru)
        $query = JadwalPelajaran::with(['mataPelajaran', 'rombel.lembaga', 'guru.orang'])
            ->whereHas('rombel', function($q) use ($tahunId) {
                if ($tahunId) $q->where('tahun_pelajaran_id', $tahunId);
            });

        if ($hari) {
            $query->where('hari', $hari);
        }

        if ($rombelId) {
            $query->where('rombel_id', $rombelId);
        }

        if ($pegawaiId) {
            $query->where('pegawai_id', $pegawaiId);
        }

        $jadwals = $query->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        return view('admin.jadwal_pelajaran.index', compact(
            'rombels', 
            'gurus', 
            'daftarHari', 
            'jadwals', 
            'tahuns', 
            'tahunId', 
            'hari', 
            'rombelId', 
            'pegawaiId'
        ));
    }

    public function create(Request $request)
    {
        $rombelId = $request->get('rombel_id');
        $tahunId = $request->get('tahun_pelajaran_id');
        $hari = $request->get('hari', 'SENIN');
        $lastJamMulai = $request->get('last_jam_mulai', '07:30');
        $lastJamSelesai = $request->get('last_jam_selesai', '08:15');

        $rombel = null;
        $mapels = collect();

        if ($rombelId) {
            $rombel = Rombel::with('lembaga', 'tahunPelajaran')->find($rombelId);
            if ($rombel) {
                $mapels = MataPelajaran::where('is_active', true)
                    ->where(function($q) use ($rombel) {
                        $q->where('lembaga_id', $rombel->lembaga_id)
                          ->orWhereNull('lembaga_id');
                    })
                    ->orderBy('nama')
                    ->get();
            }
        }

        // Fallback: If no rombel selected or no lembaga-specific mapels found, load all active MataPelajaran
        if ($mapels->isEmpty()) {
            $mapels = MataPelajaran::where('is_active', true)->orderBy('nama')->get();
        }

        $gurus = Pegawai::with('orang')->where('is_active', true)->whereIn('jenis_pegawai', ['GURU', 'USTADZ', 'PENGASUH'])->get();
        $rombels = Rombel::with('lembaga')->orderBy('nama')->get();

        return view('admin.jadwal_pelajaran.create', compact(
            'rombel', 
            'rombels', 
            'mapels', 
            'gurus', 
            'rombelId', 
            'tahunId', 
            'hari', 
            'lastJamMulai', 
            'lastJamSelesai'
        ));
    }

    public function getOccupiedSchedules(Request $request)
    {
        $rombelId = $request->get('rombel_id');
        $hari = $request->get('hari');

        if (!$rombelId || !$hari) {
            return response()->json([]);
        }

        $schedules = JadwalPelajaran::with(['mataPelajaran', 'guru.orang'])
            ->where('rombel_id', $rombelId)
            ->where('hari', $hari)
            ->orderBy('jam_mulai')
            ->get()
            ->map(function ($j) {
                return [
                    'id' => $j->id,
                    'jam_mulai' => date('H:i', strtotime($j->jam_mulai)),
                    'jam_selesai' => date('H:i', strtotime($j->jam_selesai)),
                    'mapel' => $j->mataPelajaran->nama ?? 'Mata Pelajaran',
                    'guru' => $j->guru->orang->nama_lengkap ?? 'Belum Ditentukan',
                ];
            });

        return response()->json($schedules);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rombel_id' => 'required|exists:rombel,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'pegawai_id' => 'nullable|exists:pegawai,id',
            'hari' => 'required|in:SENIN,SELASA,RABU,KAMIS,JUMAT,SABTU,AHAD',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        // Overlap Check (Pengecekan bentrok jadwal di kelas & hari yang sama)
        $conflict = JadwalPelajaran::with(['mataPelajaran', 'guru.orang'])
            ->where('rombel_id', $validated['rombel_id'])
            ->where('hari', $validated['hari'])
            ->where(function ($query) use ($validated) {
                $query->where('jam_mulai', '<', $validated['jam_selesai'])
                      ->where('jam_selesai', '>', $validated['jam_mulai']);
            })
            ->first();

        if ($conflict) {
            $mapelExist = $conflict->mataPelajaran->nama ?? 'Mata Pelajaran';
            $guruExist = $conflict->guru->orang->nama_lengkap ?? 'Guru';
            $jamExist = date('H:i', strtotime($conflict->jam_mulai)) . ' - ' . date('H:i', strtotime($conflict->jam_selesai));

            return back()->withInput()->withErrors([
                'jam_mulai' => "Jadwal bentrok! Pada Hari {$validated['hari']} jam {$jamExist} di kelas ini sudah terisi Mata Pelajaran '{$mapelExist}' ({$guruExist})."
            ])->with('error', "Gagal! Jam {$validated['jam_mulai']} - {$validated['jam_selesai']} bentrok dengan '{$mapelExist}' ({$jamExist}).");
        }

        JadwalPelajaran::create($validated);
        $mapelSaved = MataPelajaran::find($validated['mata_pelajaran_id']);
        $mapelTitle = $mapelSaved->nama ?? 'Mata Pelajaran';

        $rombel = Rombel::find($validated['rombel_id']);

        // Next automatic time slot (+45 mins after jam_selesai)
        $nextJamMulai = $validated['jam_selesai'];
        $nextJamSelesai = date('H:i', strtotime($validated['jam_selesai'] . ' + 45 minutes'));

        // Redirect back to create page maintaining last selected rombel & hari
        return redirect()->route('admin.jadwal-pelajaran.create', [
            'rombel_id' => $validated['rombel_id'],
            'tahun_pelajaran_id' => $rombel?->tahun_pelajaran_id,
            'hari' => $validated['hari'],
            'last_jam_mulai' => $nextJamMulai,
            'last_jam_selesai' => $nextJamSelesai,
        ])->with('success', "Sesi jadwal '{$mapelTitle}' ({$validated['jam_mulai']} - {$validated['jam_selesai']}) berhasil disimpan! Silakan lanjut menginput sesi berikutnya di bawah ini.");
    }

    public function destroy($id)
    {
        $jadwal = $id instanceof JadwalPelajaran ? $id : JadwalPelajaran::find($id);

        if ($jadwal && $jadwal->exists) {
            $jadwal->delete();
            return back()->with('success', 'Jadwal pelajaran berhasil dihapus.');
        }

        return back()->with('error', 'Jadwal pelajaran tidak ditemukan.');
    }

    public function exportCsv(Request $request)
    {
        $tahunId = $request->get('tahun_pelajaran_id');
        $rombelId = $request->get('rombel_id');

        if (!$tahunId) {
            $tahunAktif = TahunPelajaran::where('is_active', true)->first();
            $tahunId = $tahunAktif?->id;
        }

        $rombels = Rombel::with(['lembaga', 'tahunPelajaran'])
            ->when($tahunId, fn($q) => $q->where('tahun_pelajaran_id', $tahunId))
            ->when($rombelId, fn($q) => $q->where('id', $rombelId))
            ->orderBy('nama')
            ->get();

        $filename = 'Roster_Jadwal_Pelajaran_' . date('Ymd_His') . '.csv';
        $days = ['SENIN', 'SELASA', 'RABU', 'KAMIS', 'SABTU', 'AHAD'];
        $daysHeader = ['Jam', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Sabtu', 'Ahad'];

        return response()->streamDownload(function() use ($rombels, $days, $daysHeader) {
            $file = fopen('php://output', 'w');
            
            // Output UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Main Roster Header
            fputcsv($file, ['ROSTER BELAJAR SANTRI PONDOK NURUL FURQON']);
            fputcsv($file, []);

            foreach ($rombels as $rombel) {
                // Fetch schedule entries for this rombel
                $jadwals = JadwalPelajaran::with(['mataPelajaran', 'guru.orang'])
                    ->where('rombel_id', $rombel->id)
                    ->orderBy('jam_mulai')
                    ->get();

                // Group unique Mapel & Guru legend for right side
                $legendItems = $jadwals->filter(fn($j) => $j->mataPelajaran && $j->guru?->orang)
                    ->unique(fn($j) => $j->mata_pelajaran_id . '-' . $j->pegawai_id)
                    ->values();

                // Standard Time Slots for Pesantren
                $timeSlots = [
                    '07.15 - 07.30',
                    '07.30 - 08.15',
                    '08.15 - 09.00',
                    '09.00 - 09.15',
                    '09.15 - 10.00',
                    '10.00 - 10.45',
                    '10.45 - 11.45',
                ];

                // Table Column Header (Matching Roster Excel Layout)
                $classTitle = str_starts_with(strtoupper($rombel->nama ?? ''), 'KELAS') ? strtoupper($rombel->nama) : 'KELAS ' . strtoupper($rombel->nama);
                $headerRow = array_merge($daysHeader, ['', $classTitle, 'Mapel', 'Nama Guru']);
                fputcsv($file, $headerRow);

                $legendIndex = 0;

                foreach ($timeSlots as $slotIdx => $slot) {
                    $row = [$slot];

                    // Check fixed time slots (Upacara / Apel / Istirahat)
                    if ($slotIdx === 0) {
                        // 07.15 - 07.30 (Upacara / Apel)
                        $row = array_merge($row, ['Upacara', 'Apel', 'Apel', 'Apel', 'Apel', 'Apel']);
                    } elseif ($slotIdx === 3) {
                        // 09.00 - 09.15 (Istirahat)
                        $row = array_merge($row, array_fill(0, 6, 'Istirahat'));
                    } else {
                        // Regular lesson slots
                        foreach ($days as $day) {
                            $dayJadwals = $jadwals->where('hari', $day)->values();
                            // Match by time or sequential order
                            $matched = $dayJadwals->first(function($j) use ($slot) {
                                $timeStr = date('H.i', strtotime($j->jam_mulai)) . ' - ' . date('H.i', strtotime($j->jam_selesai));
                                return $timeStr === $slot;
                            });

                            if (!$matched && $dayJadwals->isNotEmpty()) {
                                // Fallback by slot index
                                $idxInDay = $slotIdx > 3 ? $slotIdx - 2 : $slotIdx - 1;
                                $matched = $dayJadwals->get($idxInDay) ?? $dayJadwals->first();
                            }

                            $row[] = $matched->mataPelajaran->nama ?? '-';
                        }
                    }

                    // Append legend item on right side
                    $row[] = ''; // spacer column
                    if (isset($legendItems[$legendIndex])) {
                        $item = $legendItems[$legendIndex];
                        $row[] = '';
                        $row[] = $item->mataPelajaran->nama ?? '-';
                        $row[] = $item->guru->orang->nama_lengkap ?? '-';
                        $legendIndex++;
                    } else {
                        $row[] = '';
                        $row[] = '';
                        $row[] = '';
                    }

                    fputcsv($file, $row);
                }

                // Append remaining legend items if any
                while (isset($legendItems[$legendIndex])) {
                    $item = $legendItems[$legendIndex];
                    $row = array_fill(0, 7, '');
                    $row[] = '';
                    $row[] = '';
                    $row[] = $item->mataPelajaran->nama ?? '-';
                    $row[] = $item->guru->orang->nama_lengkap ?? '-';
                    fputcsv($file, $row);
                    $legendIndex++;
                }

                fputcsv($file, []); // Empty separator row between classes
            }

            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
