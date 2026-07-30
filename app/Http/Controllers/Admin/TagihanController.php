<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\KomponenBiaya;
use App\Models\PesertaDidik;
use App\Models\TahunPelajaran;
use App\Models\Lembaga;
use App\Models\Pembayaran;
use App\Services\WhatsAppService;
use App\Jobs\SendWhatsAppMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagihanController extends Controller
{
    public function index(Request $request)
    {
        $query = Tagihan::with(['pesertaDidik.orang', 'komponenBiaya', 'tahunPelajaran']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('tahun_pelajaran_id')) {
            $query->where('tahun_pelajaran_id', $request->tahun_pelajaran_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pesertaDidik.orang', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('niup', 'like', "%{$search}%");
            });
        }

        $tagihans = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        
        $tahuns = TahunPelajaran::orderBy('tanggal_mulai', 'desc')->get();

        // Count unpaid for blast button badge
        $totalBelumLunas = Tagihan::whereIn('status', ['BELUM_BAYAR', 'SEBAGIAN'])->count();

        return view('admin.tagihan.index', compact('tagihans', 'tahuns', 'totalBelumLunas'));
    }

    public function create()
    {
        $komponenBiayas = KomponenBiaya::with('pesantren')->where('is_active', true)->get();
        $tahuns = TahunPelajaran::where('is_active', true)->get();
        $lembagas = Lembaga::where('is_active', true)->get();
        
        return view('admin.tagihan.create', compact('komponenBiayas', 'tahuns', 'lembagas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'komponen_biaya_id' => 'required|exists:komponen_biaya,id',
            'tahun_pelajaran_id' => 'required|exists:tahun_pelajaran,id',
            'lembaga_id' => 'nullable|exists:lembaga,id',
            'bulan' => 'nullable|string|max:7',
            'jatuh_tempo' => 'nullable|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $komponen = KomponenBiaya::findOrFail($request->komponen_biaya_id);
        
        $pesertaQuery = PesertaDidik::where('status', 'AKTIF');
        
        if ($request->filled('lembaga_id')) {
            $pesertaQuery->whereHas('riwayatRombel.rombel', function($q) use ($request) {
                $q->where('lembaga_id', $request->lembaga_id)
                  ->where('tahun_pelajaran_id', $request->tahun_pelajaran_id)
                  ->where('status', 'AKTIF');
            });
        }
        
        $targetPesertaIds = $pesertaQuery->pluck('id');
        
        if ($targetPesertaIds->isEmpty()) {
            return back()->with('error', 'Tidak ada Santri aktif yang ditemukan untuk kriteria tersebut.');
        }

        $count = 0;
        $waCount = 0;
        
        DB::beginTransaction();
        try {
            foreach ($targetPesertaIds as $pesertaId) {
                $exists = Tagihan::where('peserta_didik_id', $pesertaId)
                    ->where('komponen_biaya_id', $komponen->id)
                    ->where('tahun_pelajaran_id', $request->tahun_pelajaran_id);
                    
                if ($komponen->jenis === 'BULANAN' && $request->filled('bulan')) {
                    $exists->where('bulan', $request->bulan);
                }
                
                if (!$exists->exists()) {
                    $tagihan = Tagihan::create([
                        'peserta_didik_id' => $pesertaId,
                        'komponen_biaya_id' => $komponen->id,
                        'tahun_pelajaran_id' => $request->tahun_pelajaran_id,
                        'bulan' => $request->bulan,
                        'nominal' => $komponen->nominal,
                        'diskon' => 0,
                        'total' => $komponen->nominal,
                        'status' => 'BELUM_BAYAR',
                        'jatuh_tempo' => $request->jatuh_tempo,
                    ]);
                    $count++;

                    // ── Kirim Notifikasi WA: Tagihan Baru (via Queue) ──
                    $peserta = PesertaDidik::with('orang')->find($pesertaId);
                    $phone = $peserta?->getWaliPhone();
                    $santriNama = $peserta?->orang?->nama_lengkap ?? '-';
                    if ($phone) {
                        SendWhatsAppMessage::dispatch('tagihan_baru', $phone, [
                            'santri_nama'   => $santriNama,
                            'komponen_nama' => $komponen->nama,
                            'nominal'       => $komponen->nominal,
                            'bulan'         => $request->bulan,
                            'jatuh_tempo'   => $request->jatuh_tempo ? \Carbon\Carbon::parse($request->jatuh_tempo)->format('d/m/Y') : null,
                        ])->delay(now()->addSeconds($waCount * 3)); // Jeda 3 detik antar pesan
                        $waCount++;
                    }
                }
            }
            DB::commit();
            return redirect()->route('admin.tagihan.index')->with('success', "Berhasil men-generate {$count} tagihan. {$waCount} notifikasi WA sedang dikirim di latar belakang.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat men-generate tagihan: ' . $e->getMessage());
        }
    }

    public function show(Tagihan $tagihan)
    {
        $tagihan->load(['pesertaDidik.orang', 'komponenBiaya', 'tahunPelajaran']);
        
        $pembayarans = Pembayaran::with('kasir')->where('tagihan_id', $tagihan->id)->orderBy('tanggal_bayar', 'desc')->get();
        $totalDibayar = $pembayarans->sum('jumlah');
        $sisaTagihan = max(0, $tagihan->total - $totalDibayar);
        
        return view('admin.tagihan.show', compact('tagihan', 'pembayarans', 'totalDibayar', 'sisaTagihan'));
    }

    // ═══════════════════════════════════════════════════
    // KIRIM PENGINGAT WA (Per-Tagihan)
    // ═══════════════════════════════════════════════════
    public function sendWaReminder(Tagihan $tagihan)
    {
        $tagihan->load(['pesertaDidik.orang', 'komponenBiaya']);

        $totalDibayar = Pembayaran::where('tagihan_id', $tagihan->id)->sum('jumlah');
        $sisaTagihan = max(0, $tagihan->total - $totalDibayar);

        if ($sisaTagihan <= 0) {
            return back()->with('error', 'Tagihan ini sudah lunas, tidak perlu pengingat.');
        }

        $phone = $tagihan->pesertaDidik->getWaliPhone();
        
        if (!$phone) {
            return back()->with('error', 'Nomor HP wali santri tidak ditemukan. Pastikan data keluarga sudah lengkap.');
        }

        SendWhatsAppMessage::dispatch('tagihan_reminder', $phone, [
            'santri_nama'   => $tagihan->pesertaDidik->orang->nama_lengkap ?? '-',
            'komponen_nama' => $tagihan->komponenBiaya->nama ?? '-',
            'sisa_tagihan'  => $sisaTagihan,
            'jatuh_tempo'   => $tagihan->jatuh_tempo ? $tagihan->jatuh_tempo->format('d/m/Y') : null,
        ]);

        return back()->with('success', "✅ Pengingat tagihan dijadwalkan untuk dikirim via WhatsApp ke {$phone}.");
    }

    // ═══════════════════════════════════════════════════
    // BLAST PENGINGAT WA (Semua Tagihan Belum Lunas)
    // ═══════════════════════════════════════════════════
    public function blastWaReminder(Request $request)
    {
        $tagihans = Tagihan::with(['pesertaDidik.orang', 'komponenBiaya'])
            ->whereIn('status', ['BELUM_BAYAR', 'SEBAGIAN'])
            ->get();

        if ($tagihans->isEmpty()) {
            return back()->with('error', 'Tidak ada tagihan yang belum lunas.');
        }

        $dispatched = 0;
        $failed = 0;

        foreach ($tagihans as $tagihan) {
            $phone = $tagihan->pesertaDidik->getWaliPhone();

            if (!$phone) {
                $failed++;
                continue;
            }

            $totalDibayar = Pembayaran::where('tagihan_id', $tagihan->id)->sum('jumlah');
            $sisaTagihan = max(0, $tagihan->total - $totalDibayar);

            if ($sisaTagihan <= 0) continue;

            SendWhatsAppMessage::dispatch('tagihan_reminder', $phone, [
                'santri_nama'   => $tagihan->pesertaDidik->orang->nama_lengkap ?? '-',
                'komponen_nama' => $tagihan->komponenBiaya->nama ?? '-',
                'sisa_tagihan'  => $sisaTagihan,
                'jatuh_tempo'   => $tagihan->jatuh_tempo ? $tagihan->jatuh_tempo->format('d/m/Y') : null,
            ])->delay(now()->addSeconds($dispatched * 3)); // Jeda 3 detik antar pesan

            $dispatched++;
        }
        $failedMsg = $failed > 0 ? " ({$failed} nomor tidak ditemukan)" : "";
        return back()->with('success', "📢 Blast dijadwalkan! {$dispatched} pesan sedang dikirim di latar belakang.{$failedMsg}");
    }

    public function destroy(Tagihan $tagihan)
    {
        if ($tagihan->status !== 'BELUM_BAYAR') {
            return back()->with('error', 'Tagihan yang sudah dibayar tidak dapat dihapus.');
        }
        
        $tagihan->delete();
        return back()->with('success', 'Tagihan berhasil dibatalkan/dihapus.');
    }

    // Catatan: Logika pencarian nomor HP wali telah dipindahkan ke
    // App\Models\PesertaDidik::getWaliPhone() agar konsisten di seluruh sistem.
}

