<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'type', 'value', 'is_active', 'expires_at', 'max_uses'];

    protected $casts = [
        'value'      => 'decimal:2',
        'is_active'  => 'boolean',
        'expires_at' => 'datetime',
        'max_uses'   => 'integer',
    ];

    /**
     * Statuses that represent a genuinely CONSUMED use of a coupon. Under
     * Paymob (Batch 6, C3), an order row is created BEFORE payment — so
     * `awaiting_payment` / `payment_failed` must NOT burn coupon capacity for
     * an abandoned or declined checkout, the way they would have under the
     * old manual flow's whereNotIn('status', ['rejected','cancelled'])
     * check. `pending_review` is kept for legacy manual-flow rows still
     * moving through that path. `rejected`, `cancelled`, and `refunded` are
     * explicitly excluded too — none of those represent a kept sale.
     */
    public const CONSUMED_STATUSES = [
        Subscription::STATUS_PENDING_REVIEW,
        Subscription::STATUS_APPROVED,
        Subscription::STATUS_ACTIVE,
        Subscription::STATUS_EXPIRED,
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

    /**
     * Single source of truth for "how many times has this code actually
     * been consumed" — used both by findActive() (checkout pricing) and the
     * atomic lockForUpdate() re-check in PurchaseController, so the two can
     * never drift apart.
     */
    public function usageCount(): int
    {
        return Subscription::whereRaw('UPPER(coupon_code) = ?', [strtoupper($this->code)])
            ->whereIn('status', self::CONSUMED_STATUSES)
            ->count();
    }

    public static function findActive(string $code): ?self
    {
        $coupon = static::active()->whereRaw('UPPER(code) = ?', [strtoupper($code)])->first();

        if (!$coupon) return null;

        if ($coupon->max_uses !== null && $coupon->usageCount() >= $coupon->max_uses) {
            return null;
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
