<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'discount_percentage',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'starts_at'           => 'datetime',
        'ends_at'             => 'datetime',
        'is_active'           => 'boolean',
    ];

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at',   '>=', now());
    }

    // ── Helpers ───────────────────────────────────────────────────

    /** Localised display name. */
    public function localName(): string
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }

    /** True when this season's time window overlaps with another season's range. */
    public function overlapsWithAny(?int $exceptId = null): ?self
    {
        return static::where('is_active', true)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->where('starts_at', '<', $this->ends_at)
            ->where('ends_at',   '>', $this->starts_at)
            ->first();
    }
}
