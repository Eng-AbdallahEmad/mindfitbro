<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['code', 'type', 'value', 'is_active', 'expires_at', 'max_uses'];

    protected $casts = [
        'value'      => 'decimal:2',
        'is_active'  => 'boolean',
        'expires_at' => 'datetime',
        'max_uses'   => 'integer',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'coupon_code', 'code');
    }

    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    public static function findActive(string $code): ?self
    {
        $coupon = static::active()->whereRaw('UPPER(code) = ?', [strtoupper($code)])->first();

        if (!$coupon) return null;

        if ($coupon->max_uses !== null) {
            $used = Subscription::whereRaw('UPPER(coupon_code) = ?', [strtoupper($code)])->count();
            if ($used >= $coupon->max_uses) return null;
        }

        return $coupon;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($this->type === 'percentage') {
            return round($subtotal * ($this->value / 100), 3);
        }

        return min((float) $this->value, $subtotal);
    }
}
