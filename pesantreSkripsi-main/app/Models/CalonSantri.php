<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;


class CalonSantri extends Model
{
 
    protected $table = 'calon_santri';
    
    protected $fillable = [
        'gelombang_id', 'no_pendaftaran', 'nama_lengkap', 'jenis_kelamin',
        'tempat_lahir', 'tanggal_lahir', 'nik', 'no_kk', 'asal_sekolah', 'nisn',
        // Data Ayah
        'nama_ayah', 'nik_ayah', 'tahun_lahir_ayah', 'pendidikan_ayah',
        'pekerjaan_ayah', 'penghasilan_ayah', 'no_hp_ayah',
        // Data Ibu
        'nama_ibu', 'nik_ibu', 'tahun_lahir_ibu', 'pendidikan_ibu',
        'pekerjaan_ibu', 'penghasilan_ibu', 'no_hp_ibu',
        // Wali & Kontak
        'telepon_wali', 'tinggal_bersama',
        'nama_wali', 'nik_wali', 'tahun_lahir_wali', 'pendidikan_wali',
        'pekerjaan_wali', 'penghasilan_wali', 'no_hp_wali', 'hubungan_wali',
        // Lainnya
        'alamat', 'lembaga_tujuan_id',
        'status', 'catatan_verifikasi', 'diverifikasi_oleh', 'tanggal_verifikasi'
    ];

    // Attribute Casting (Otomatisasi Tipe Data)
    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_verifikasi' => 'datetime',
    ];

    /**
     * Model Events
     */
    protected static function booted()
    {
        static::creating(function ($calon) {
            // Otomatis generate nomor pendaftaran jika kosong
            if (empty($calon->no_pendaftaran)) {
                $calon->no_pendaftaran = self::generateNoPendaftaran($calon->gelombang_id);
            }
            
            // Set status default jika kosong
            if (empty($calon->status)) {
                $calon->status = 'BARU_MASUK';
            }
        });
    }

    // ── MUTATORS (Penjaga Kerapihan Data) ──

    public function setNamaLengkapAttribute($value)
    {
        $this->attributes['nama_lengkap'] = Str::title(trim($value));
    }

    public function setTempatLahirAttribute($value)
    {
        $this->attributes['tempat_lahir'] = Str::title(trim($value));
    }

    public function setNamaAyahAttribute($value)
    {
        $this->attributes['nama_ayah'] = Str::title(trim($value));
    }

    public function setNamaIbuAttribute($value)
    {
        $this->attributes['nama_ibu'] = Str::title(trim($value));
    }

    public function setAsalSekolahAttribute($value)
    {
        // Asal sekolah biasanya disingkat (misal: SDN), 
        // tapi spasi berlebih dibuang
        $this->attributes['asal_sekolah'] = trim($value);
    }

    public function setNamaWaliAttribute($value)
    {
        $this->attributes['nama_wali'] = $value ? Str::title(trim($value)) : null;
    }

    // ──────────────────────────────────────

    // 
    public static function generateNoPendaftaran($gelombangId)
    {
        $tahun = date('Y'); // Mengambil tahun saat ini
        
        // Menghitung jumlah pendaftar di gelombang tersebut, lalu ditambah 1
        $count = self::where('gelombang_id', $gelombangId)->count() + 1;
        
        // Menghasilkan format: PSB-2026-0001
        return 'PSB-' . $tahun . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
    // ──────────────────────────

    public function gelombang(): BelongsTo
    {
        return $this->belongsTo(GelombangPsb::class, 'gelombang_id');
    }

    public function lembagaTujuan(): BelongsTo
    {
        return $this->belongsTo(Lembaga::class, 'lembaga_tujuan_id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function dokumen(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DokumenPsb::class);
    }
}