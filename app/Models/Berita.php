<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Berita extends Model
{
    protected $table = 'berita';

    protected $fillable = [
        'judul', 'slug', 'tipe', 'ringkasan', 'konten', 'gambar_cover', 
        'is_published', 'is_pinned', 'penulis_id', 'view_count', 'published_at'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_pinned' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($berita) {
            if (empty($berita->slug)) {
                $berita->slug = Str::slug($berita->judul);
            }
            if (empty($berita->tipe)) {
                $berita->tipe = 'berita';
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

    // ── Scopes ──

    public function scopeBerita($query)
    {
        return $query->where('tipe', 'berita');
    }

    public function scopePengumuman($query)
    {
        return $query->where('tipe', 'pengumuman');
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // ── Relationships ──

    public function penulis(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penulis_id');
    }

    // ── Accessors ──

    public function getTanggalFormatAttribute()
    {
        return $this->published_at ? $this->published_at->translatedFormat('d F Y') : '-';
    }

    public function getTipeLabelAttribute()
    {
        return match($this->tipe) {
            'pengumuman' => 'Pengumuman',
            default => 'Berita',
        };
    }

    public function getIsPengumumanAttribute()
    {
        return $this->tipe === 'pengumuman';
    }
}
