<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\HubunganKeluarga;
use App\Models\User;

class PesertaDidik extends Model
{
    use SoftDeletes;

    protected $table = 'peserta_didik';
    
    protected $fillable = [
        'orang_id', 'nis', 'nisn', 'tanggal_masuk', 'tanggal_keluar', 'status', 'catatan'
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
    ];

    public function orang(): BelongsTo
    {
        return $this->belongsTo(Orang::class);
    }

    public function riwayatLembaga(): HasMany
    {
        return $this->hasMany(PesertaLembagaTahun::class);
    }

    public function riwayatRombel(): HasMany
    {
        return $this->hasMany(RiwayatRombelPeserta::class);
    }

    public function riwayatMukim(): HasMany
    {
        return $this->hasMany(PesertaMukimTahun::class);
    }

    public function tagihan(): HasMany
    {
        return $this->hasMany(Tagihan::class);
    }

    public function presensi(): HasMany
    {
        return $this->hasMany(PresensiKelas::class);
    }

    public function nilaiRapor(): HasMany
    {
        return $this->hasMany(NilaiRapor::class);
    }

    public function pelanggaran(): HasMany
    {
        return $this->hasMany(CatatanPelanggaran::class);
    }

    public function prestasi(): HasMany
    {
        return $this->hasMany(CatatanPrestasi::class);
    }

    public function perizinan(): HasMany
    {
        return $this->hasMany(PerizinanKeluar::class);
    }

    public function riwayatStatus(): HasMany
    {
        return $this->hasMany(RiwayatStatusSantri::class);
    }

    public function riwayatMutasi(): HasMany
    {
        return $this->hasMany(RiwayatMutasi::class);
    }

    // ═══════════════════════════════════════════════════════════
    // METHOD TERPUSAT: Cari Nomor WhatsApp Wali Santri
    //
    // Urutan pencarian (dari paling akurat):
    //   1. Nomor telepon di profil Orang wali utama
    //   2. Username akun User wali (username = nomor HP berdasarkan konvensi sistem)
    //   3. Nomor telepon di profil Orang santri itu sendiri (fallback terakhir)
    //
    // Gunakan method ini di semua controller agar perilakunya konsisten.
    // Contoh penggunaan: $pesertaDidik->getWaliPhone()
    // ═══════════════════════════════════════════════════════════
    public function getWaliPhone(): ?string
    {
        $hubungan = null;

        // Check if relation is loaded to prevent N+1 query
        if ($this->relationLoaded('orang') && $this->orang->relationLoaded('keluarga')) {
            $hubungan = $this->orang->keluarga->first(function($h) {
                return $h->is_wali_utama;
            });
        } else {
            $hubungan = HubunganKeluarga::where('orang_id', $this->orang_id)
                ->where('is_wali_utama', true)
                ->first();
        }

        if ($hubungan) {
            $wali = $hubungan->orangTuaAtauWali;

            // 1a. Cek nomor telepon di profil Orang wali
            if ($wali && $wali->telepon) {
                return $wali->telepon;
            }

            // 1b. Fallback: Cek username akun User wali (username = nomor HP)
            if ($wali) {
                $userWali = $wali->relationLoaded('user') ? $wali->user : User::where('orang_id', $wali->id)->first();
                if ($userWali && preg_match('/^0\d{9,}$/', $userWali->username)) {
                    return $userWali->username;
                }
            }
        }

        // 2. Fallback akhir: Nomor telepon santri itu sendiri
        return $this->orang?->telepon ?? null;
    }

    public function getRombelAktifAttribute()
    {
        $activeTahun = TahunPelajaran::where('is_active', true)->first();
        if (!$activeTahun) return null;

        $riwayat = $this->riwayatRombel()
            ->where('tahun_pelajaran_id', $activeTahun->id)
            ->where('status', 'AKTIF')
            ->first();

        return $riwayat ? $riwayat->rombel : null;
    }

    public function getKamarAktifAttribute()
    {
        $activeTahun = TahunPelajaran::where('is_active', true)->first();
        if (!$activeTahun) return null;

        $riwayat = $this->riwayatMukim()
            ->where('tahun_pelajaran_id', $activeTahun->id)
            ->where('status_mukim', 'MUKIM')
            ->first();

        return $riwayat ? $riwayat->kamar : null;
    }

    public function getLembagaAttribute()
    {
        return $this->rombelAktif ? $this->rombelAktif->lembaga : null;
    }
}

