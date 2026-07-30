<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisPresensi extends Model
{
    protected $table = 'jenis_presensi';

    protected $fillable = [
        'nama', 'kode', 'deskripsi', 'target_gender', 'tipe_target',
        'jam_mulai', 'jam_selesai', 'is_active', 'urutan'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function presensi(): HasMany
    {
        return $this->hasMany(PresensiKelas::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForGender($query, string $gender)
    {
        return $query->where(function ($q) use ($gender) {
            $q->where('target_gender', 'SEMUA')
              ->orWhere('target_gender', $gender);
        });
    }
}
