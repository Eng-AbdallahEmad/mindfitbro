<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'plan_id',
        'quantity',
        'monthly_price',
        'yearly_discount_rate',
        'final_price',
    ];

    protected $casts = [
        'quantity'              => 'integer',
        'monthly_price'         => 'decimal:2',
        'yearly_discount_rate'  => 'decimal:2',
        'final_price'           => 'decimal:2',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}