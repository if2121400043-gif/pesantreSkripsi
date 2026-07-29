<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PesertaDidik;
use App\Models\HubunganKeluarga;
use App\Models\Lembaga;
use App\Models\Rombel;
use App\Services\WhatsAppService;
use App\Jobs\SendWhatsAppMessage;
use Illuminate\Http\Request;

class BroadcastWaController extends Controller
{
    public function index()
    {
        $lembagas = Lembaga::where('is_active', true)->orderBy('urutan')->get();
        $rombels = Rombel::with('lembaga')->where('status', 'AKTIF')->orderBy('nama')->get();

        // Hitung total wali santri aktif yang punya nomor HP
        $totalWali = $this->getRecipients('semua')->count();

        return view('admin.broadcast.index', compact('lembagas', 'rombels', 'totalWali'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'target' => 'required|in:semua,lembaga,rombel',
            'lembaga_id' => 'required_if:target,lembaga|nullable|exists:lembaga,id',
            'rombel_id' => 'required_if:target,rombel|nullable|exists:rombel,id',
            'pesan' => 'required|string|min:10|max:2000',
        ]);

        set_time_limit(0); // Opsi A: Tanpa batas waktu

        $recipients = $this->getRecipients(
            $request->target,
            $request->lembaga_id,
            $request->rombel_id
        );

        if ($recipients->isEmpty()) {
            return back()->with('error', 'Tidak ada penerima yang ditemukan. Pastikan data keluarga/wali sudah lengkap.');
        }

        $portalUrl = url('/portal/beranda');
        $dispatched = 0;

        foreach ($recipients as $index => $recipient) {
            // Replace placeholder variables
            $pesan = str_replace(
                ['{nama_santri}', '{nama_wali}', '{link_portal}'],
                [$recipient['nama_santri'], $recipient['nama_wali'], $portalUrl],
                $request->pesan
            );

            SendWhatsAppMessage::dispatch('custom', $recipient['phone'], [
                'message' => $pesan,
            ])->delay(now()->addSeconds($index * 3)); // Jeda 3 detik antar pesan

            $dispatched++;
        }

        return back()->with('success', "📢 Broadcast dijadwalkan! {$dispatched} pesan sedang dikirim di latar belakang.");
    }

    /**
     * Ambil daftar penerima (wali santri aktif) beserta nomor HP.
     */
    private function getRecipients($target, $lembagaId = null, $rombelId = null)
    {
        $query = PesertaDidik::with([
            'orang.keluarga' => function ($q) {
                $q->where('is_wali_utama', true)->with('orangTuaAtauWali.user');
            }
        ])->where('status', 'AKTIF');

        if ($target === 'lembaga' && $lembagaId) {
            $query->whereHas('riwayatRombel.rombel', function ($q) use ($lembagaId) {
                $q->where('lembaga_id', $lembagaId)->where('status', 'AKTIF');
            });
        } elseif ($target === 'rombel' && $rombelId) {
            $query->whereHas('riwayatRombel', function ($q) use ($rombelId) {
                $q->where('rombel_id', $rombelId)->where('status', 'AKTIF');
            });
        }

        $pesertaList = $query->get();
        $recipients = collect();
        $processedPhones = []; // Hindari duplikat (1 wali punya banyak anak)

        foreach ($pesertaList as $peserta) {
            // Gunakan method terpusat di model PesertaDidik
            $phone = $peserta->getWaliPhone();
            $namaWali = 'Bapak/Ibu Wali';

            if (!$phone || in_array($phone, $processedPhones)) continue;

            // Cari nama wali
            $hubungan = $peserta->orang?->keluarga?->first();

            if ($hubungan && $hubungan->orangTuaAtauWali) {
                $namaWali = $hubungan->orangTuaAtauWali->nama_lengkap;
            }

            $recipients->push([
                'phone' => $phone,
                'nama_santri' => $peserta->orang->nama_lengkap ?? '-',
                'nama_wali' => $namaWali,
            ]);

            $processedPhones[] = $phone;
        }

        return $recipients;
    }

    // Catatan: Logika pencarian nomor HP wali telah dipindahkan ke
    // App\Models\PesertaDidik::getWaliPhone() agar konsisten di seluruh sistem.
}
