<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['title', 'thumbnail_url', 'video_url', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    public function getThumbnailSrcAttribute(): string
    {
        if (!$this->thumbnail_url) {
            return asset('assets/imgs/video-thumb-1.png');
        }
        if (str_starts_with($this->thumbnail_url, 'http')) {
            return $this->thumbnail_url;
        }
        return asset($this->thumbnail_url);
    }
}
