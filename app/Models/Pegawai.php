<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pegawai extends Model
{
    use SoftDeletes;

    protected $table = 'pegawai';
    
    protected $fillable = [
        'orang_id', 'nip', 'nuptk', 'jenis_pegawai', 'jabatan', 
        'status_kepegawaian', 'tanggal_masuk', 'tanggal_keluar', 
        'pendidikan_terakhir', 'jurusan_pendidikan', 'is_active', 'catatan'
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
        'is_active' => 'boolean',
    ];

    public function orang(): BelongsTo
    {
        return $this->belongsTo(Orang::class);
    }

    public function waliKelas(): HasMany
    {
        return $this->hasMany(Rombel::class, 'wali_kelas_id');
    }

    public function jadwalMengajar(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    public function riwayatJabatan(): HasMany
    {
        return $this->hasMany(RiwayatJabatanPegawai::class);
    }
}
