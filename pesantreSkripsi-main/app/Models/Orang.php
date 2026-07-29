<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Orang extends Model
{
    use SoftDeletes;

    protected $table = 'orang';
    
    protected $fillable = [
        'niup', 'nama_lengkap', 'nama_panggilan', 'jenis_kelamin', 
        'tempat_lahir', 'tanggal_lahir', 'nik', 'no_kk', 'no_paspor', 
        'golongan_darah', 'kewarganegaraan', 'anak_ke', 'jumlah_saudara', 
        'alamat_lengkap', 'rt', 'rw', 'desa_id', 'kode_pos', 
        'telepon', 'email', 'foto', 'is_active'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Model Events
     */
    protected static function booted()
    {
        static::creating(function ($orang) {
            // Jika NIUP belum diisi, generate otomatis
            if (empty($orang->niup)) {
                $orang->niup = self::generateNiup();
            }
            
            // Set default active jika tidak ditentukan
            if (!isset($orang->is_active)) {
                $orang->is_active = true;
            }
        });
    }

    // ── MUTATORS (Satpam Input) ──
    
    public function setNamaLengkapAttribute($value)
    {
        // Otomatis Title Case: "ahmad subardjo" -> "Ahmad Subardjo"
        $this->attributes['nama_lengkap'] = Str::title(trim($value));
    }

    public function setNamaPanggilanAttribute($value)
    {
        $this->attributes['nama_panggilan'] = Str::title(trim($value));
    }

    public function setTempatLahirAttribute($value)
    {
        $this->attributes['tempat_lahir'] = Str::title(trim($value));
    }

    public function setEmailAttribute($value)
    {
        // Email selalu huruf kecil
        $this->attributes['email'] = strtolower(trim($value));
    }

    // ────────────────────────────

    /**
     * Accessor: $orang->nama resolves to nama_lengkap.
     */
    public function getNamaAttribute(): ?string
    {
        return $this->nama_lengkap;
    }

    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class);
    }

    public function pesertaDidik(): HasOne
    {
        return $this->hasOne(PesertaDidik::class);
    }

    public function pegawai(): HasOne
    {
        return $this->hasOne(Pegawai::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function kesehatan(): HasOne
    {
        return $this->hasOne(DataKesehatan::class);
    }

    public function hubunganSebagaiOrangTua(): HasMany
    {
        return $this->hasMany(HubunganKeluarga::class, 'keluarga_id');
    }

    public function keluarga(): HasMany
    {
        return $this->hasMany(HubunganKeluarga::class, 'orang_id');
    }

    /**
     * Generate NIUP otomatis: NIUP-YYYY-XXXXXX
     */
    public static function generateNiup(): string
    {
        $year = date('Y');
        $last = self::where('niup', 'like', "{$year}-%")->orderBy('id', 'desc')->first();
        $seq = $last ? intval(substr($last->niup, -6)) + 1 : 1;
        return $year . '-' . str_pad($seq, 6, '0', STR_PAD_LEFT);
    }
}
