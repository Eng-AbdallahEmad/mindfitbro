<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'is_yearly',
        'coupon_code',
        'subtotal',
        'coupon_discount',
        'yearly_discount',
        'total',
    ];

    protected $casts = [
        'is_yearly'        => 'boolean',
        'subtotal'         => 'decimal:2',
        'coupon_discount'  => 'decimal:2',
        'yearly_discount'  => 'decimal:2',
        'total'            => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}