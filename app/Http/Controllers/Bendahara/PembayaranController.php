<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Jobs\SendWhatsAppMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PembayaranController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'tagihan_id' => 'required|exists:tagihan,id',
            'jumlah' => 'required|numeric|min:1',
            'metode' => 'required|in:TUNAI,TRANSFER,QRIS,LAINNYA',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $tagihan = Tagihan::findOrFail($request->tagihan_id);
        
        $totalDibayar = Pembayaran::where('tagihan_id', $tagihan->id)->sum('jumlah');
        $sisaTagihan = max(0, $tagihan->total - $totalDibayar);
        
        if ($sisaTagihan <= 0) {
            return back()->with('error', 'Tagihan ini sudah lunas.');
        }
        
        $jumlahBayar = min($request->jumlah, $sisaTagihan);

        DB::beginTransaction();
        try {
            $pembayaran = Pembayaran::create([
                'tagihan_id' => $tagihan->id,
                'no_transaksi' => 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                'jumlah' => $jumlahBayar,
                'metode' => $request->metode,
                'keterangan' => $request->keterangan,
                'kasir_id' => auth()->id(),
                'tanggal_bayar' => now(),
            ]);

            $newTotalDibayar = $totalDibayar + $jumlahBayar;
            $newSisa = max(0, $tagihan->total - $newTotalDibayar);
            
            if ($newTotalDibayar >= $tagihan->total) {
                $tagihan->update(['status' => 'LUNAS']);
            } else {
                $tagihan->update(['status' => 'SEBAGIAN']);
            }

            DB::commit();

            try {
                $tagihan->load(['pesertaDidik.orang', 'komponenBiaya']);
                $peserta = $tagihan->pesertaDidik;
                $phone = $peserta?->getWaliPhone();

                if ($phone) {
                    SendWhatsAppMessage::dispatch('pembayaran_sukses', $phone, [
                        'santri_nama' => $peserta->orang->nama_lengkap,
                        'komponen_nama' => $tagihan->komponenBiaya->nama,
                        'jumlah_bayar' => $jumlahBayar,
                        'sisa_tagihan' => $newSisa,
                        'no_transaksi' => $pembayaran->no_transaksi,
                    ]);
                }
            } catch (\Exception $waEx) {
                \Illuminate\Support\Facades\Log::warning('WA notif pembayaran gagal: ' . $waEx->getMessage());
            }

            return redirect()->route('bendahara.tagihan.show', $tagihan->id)->with('success', 'Pembayaran sebesar Rp ' . number_format($jumlahBayar, 0, ',', '.') . ' berhasil diterima.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses pembayaran.');
        }
    }
    
    public function destroy(Pembayaran $pembayaran)
    {
        $tagihan = $pembayaran->tagihan;
        
        DB::beginTransaction();
        try {
            $pembayaran->delete();
            
            $totalDibayar = Pembayaran::where('tagihan_id', $tagihan->id)->sum('jumlah');
            
            if ($totalDibayar <= 0) {
                $tagihan->update(['status' => 'BELUM_BAYAR']);
            } else if ($totalDibayar < $tagihan->total) {
                $tagihan->update(['status' => 'SEBAGIAN']);
            }
            
            DB::commit();
            return back()->with('success', 'Transaksi pembayaran berhasil dibatalkan.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat mematalkan transaksi.');
        }
    }
    
    public function kuitansi(Pembayaran $pembayaran)
    {
        $pembayaran->load(['tagihan.pesertaDidik.orang', 'tagihan.komponenBiaya', 'kasir']);
        $pesantren = \App\Models\Pesantren::first();
        
        return view('admin.tagihan.kuitansi_print', compact('pembayaran', 'pesantren'));
    }
}
