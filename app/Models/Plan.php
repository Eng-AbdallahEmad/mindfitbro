<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    // ── Style variant → button CSS classes ───────────────────────────
    const VARIANTS = [
        'outline' => 'border-2 border-primary text-primary hover:bg-blue-50',
        'solid'   => 'bg-primary text-white hover:bg-primaryDark',
        'accent'  => 'bg-accent text-darkBg hover:bg-yellow-300',
    ];

    // Default icon styling per variant (used when icon_bg / icon_color are null)
    const VARIANT_ICON_BG = [
        'outline' => 'bg-blue-50',
        'solid'   => 'bg-primary',
        'accent'  => 'bg-accent/20',
    ];

    const VARIANT_ICON_COLOR = [
        'outline' => 'text-primary',
        'solid'   => 'text-accent',
        'accent'  => 'text-yellow-600',
    ];

    protected $fillable = [
        'key',
        'name',
        'icon',
        'icon_bg',
        'icon_color',
        'desc',
        'price',
        'popular',
        'style_variant',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'popular'   => 'boolean',
        'is_active' => 'boolean',
        'price'     => 'decimal:3',
    ];

    // ── Accessors ─────────────────────────────────────────────────────

    /** Tailwind classes for the subscribe button */
    public function getButtonClassesAttribute(): string
    {
        return self::VARIANTS[$this->style_variant ?? 'outline'] ?? self::VARIANTS['outline'];
    }

    /** Tailwind bg class for the icon box — uses DB value if set, falls back to variant default */
    public function getIconBgClassAttribute(): string
    {
        return $this->icon_bg ?: (self::VARIANT_ICON_BG[$this->style_variant ?? 'outline'] ?? 'bg-blue-50');
    }

    /** Tailwind color class for the icon — uses DB value if set, falls back to variant default */
    public function getIconColorClassAttribute(): string
    {
        return $this->icon_color ?: (self::VARIANT_ICON_COLOR[$this->style_variant ?? 'outline'] ?? 'text-primary');
    }

    /** Icon name with safe default */
    public function getIconNameAttribute(): string
    {
        return $this->icon ?: 'star';
    }

    // ── Relations ─────────────────────────────────────────────────────

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function features()
    {
        return $this->belongsToMany(Feature::class)
            ->withPivot('is_included', 'sort_order')
            ->withTimestamps()
            ->orderBy('feature_plan.sort_order');
    }

    public function prices()
    {
        return $this->hasMany(PlanPrice::class);
    }

    public function priceFor(string $currency, int $durationMonths = 3): ?PlanPrice
    {
        return $this->prices->first(
            fn ($p) => $p->currency === $currency && (int) $p->duration_months === $durationMonths
        );
    }

    public function sarPrice(int $durationMonths = 3): float
    {
        return (float) ($this->priceFor('SAR', $durationMonths)?->price ?? $this->price ?? 0);
    }
}
