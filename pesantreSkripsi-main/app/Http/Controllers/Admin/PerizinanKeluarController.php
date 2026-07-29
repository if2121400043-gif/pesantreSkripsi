<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PerizinanKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerizinanKeluarController extends Controller
{
    public function index(Request $request)
    {
        $query = PerizinanKeluar::with(['pesertaDidik.orang', 'penyetuju']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pesertaDidik.orang', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('niup', 'like', "%{$search}%");
            });
        }

        // Default: Sort by newest created and waiting status first
        $perizinans = $query->orderByRaw("CASE status WHEN 'MENUNGGU' THEN 1 WHEN 'DISETUJUI' THEN 2 WHEN 'SELESAI' THEN 3 WHEN 'DITOLAK' THEN 4 ELSE 5 END")
                            ->orderBy('created_at', 'desc')
                            ->paginate(15)
                            ->withQueryString();

        return view('admin.kedisiplinan.perizinan.index', compact('perizinans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'peserta_didik_id' => 'required|exists:peserta_didik,id',
            'jenis' => 'required|in:PULANG,KELUAR_SEMENTARA,SAKIT,KEPERLUAN_KHUSUS',
            'waktu_keluar' => 'required|date',
            'waktu_kembali_rencana' => 'nullable|date|after:waktu_keluar',
            'dijemput_oleh' => 'nullable|string|max:150',
            'hubungan_penjemput' => 'nullable|string|max:50',
            'alasan' => 'nullable|string',
        ]);

        $validated['status'] = 'MENUNGGU';

        PerizinanKeluar::create($validated);

        return back()->with('success', 'Permohonan izin keluar berhasil dibuat dan menunggu persetujuan.');
    }

    public function update(Request $request, PerizinanKeluar $perizinan)
    {
        // Custom logic based on action type
        $action = $request->get('action'); // approve, reject, return

        if ($action === 'approve') {
            $perizinan->update([
                'status' => 'DISETUJUI',
                'disetujui_oleh' => Auth::id(),
                'tanggal_persetujuan' => now(),
                'catatan_persetujuan' => $request->get('catatan_persetujuan')
            ]);
            return back()->with('success', 'Izin keluar disetujui.');
        } 
        
        if ($action === 'reject') {
            $perizinan->update([
                'status' => 'DITOLAK',
                'disetujui_oleh' => Auth::id(),
                'tanggal_persetujuan' => now(),
                'catatan_persetujuan' => $request->get('catatan_persetujuan')
            ]);
            return back()->with('success', 'Izin keluar ditolak.');
        }

        if ($action === 'return') {
            $perizinan->update([
                'status' => 'SELESAI',
                'waktu_kembali_aktual' => now()
            ]);
            return back()->with('success', 'Santri telah dicatat kembali ke asrama.');
        }

        return back()->with('error', 'Aksi tidak valid.');
    }

    public function destroy(PerizinanKeluar $perizinan)
    {
        if ($perizinan->status === 'DISETUJUI' && !$perizinan->waktu_kembali_aktual) {
            return back()->with('error', 'Tidak dapat menghapus izin yang sedang berjalan.');
        }
        $perizinan->delete();
        return back()->with('success', 'Riwayat perizinan berhasil dihapus.');
    }
}
