<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Berita extends Model
{
    protected $table = 'berita';

    protected $fillable = [
        'judul', 'slug', 'ringkasan', 'konten', 'gambar_cover', 
        'is_published', 'penulis_id', 'view_count', 'published_at'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($berita) {
            if (empty($berita->slug)) {
                $berita->slug = Str::slug($berita->judul);
            }
            if ($berita->is_published && empty($berita->published_at)) {
                $berita->published_at = now();
            }
        });
        
        static::updating(function ($berita) {
            if ($berita->isDirty('is_published') && $berita->is_published && empty($berita->published_at)) {
                $berita->published_at = now();
            }
        });
    }

    public function penulis(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penulis_id');
    }

    // 
    public function getTanggalFormatAttribute()
    {
        return $this->published_at ? $this->published_at->translatedFormat('d F Y') : '-';
    }
}
