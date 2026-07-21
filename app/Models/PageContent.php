<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PageContent extends Model
{
    protected $fillable = ['page', 'field_key', 'value_ar', 'value_en'];

    const CACHE_TTL = 86400; // 1 day — invalidated immediately on admin save

    private static function cacheKey(string $page): string
    {
        return "page_content.$page";
    }

    /**
     * All field rows for a page, keyed by field_key. Cached per page,
     * matching the SeasonService/HomeService remember+forget convention.
     */
    public static function forPage(string $page): Collection
    {
        return Cache::remember(self::cacheKey($page), self::CACHE_TTL, function () use ($page) {
            return static::where('page', $page)->get()->keyBy('field_key');
        });
    }

    public static function get(string $page, string $key, string $locale, string $default = ''): string
    {
        $row = static::forPage($page)->get($key);

        if (!$row) {
            return $default;
        }

        $value = $locale === 'ar' ? $row->value_ar : $row->value_en;

        return ($value !== null && $value !== '') ? $value : $default;
    }

    /**
     * Newline-delimited list fields (same convention as Setting's marquee_items_ar/en).
     */
    public static function items(string $page, string $key, string $locale, array $default = []): array
    {
        $value = static::get($page, $key, $locale, '');

        if ($value === '') {
            return $default;
        }

        return array_values(array_filter(
            array_map('trim', explode("\n", $value)),
            fn ($line) => $line !== ''
        ));
    }

    public static function forgetCache(string $page): void
    {
        Cache::forget(self::cacheKey($page));
    }
}
