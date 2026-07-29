<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = [
        'judul',
        'deskripsi',
        'tipe',
        'url',
        'kategori',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the full URL for images or the embed URL for YouTube videos.
     */
    public function getMediaUrlAttribute()
    {
        if ($this->tipe === 'IMAGE') {
            return asset(Storage::url($this->url));
        }

        if ($this->tipe === 'VIDEO') {
            return $this->getYouTubeEmbedUrl($this->url);
        }

        return $this->url;
    }

    /**
     * Extract YouTube ID and return embed URL.
     */
    private function getYouTubeEmbedUrl($url)
    {
        $id = $this->getYouTubeId($url);
        return $id ? "https://www.youtube.com/embed/{$id}" : $url;
    }

    /**
     * Helper to get YouTube Video ID from various URL formats.
     */
    public function getYouTubeId($url)
    {
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
        return $match[1] ?? null;
    }

    /**
     * Get YouTube Thumbnail.
     */
    public function getYouTubeThumbnailAttribute()
    {
        $id = $this->getYouTubeId($this->url);
        return $id ? "https://img.youtube.com/vi/{$id}/hqdefault.jpg" : null;
    }
}
