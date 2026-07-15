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
        'price',
        'final_price',
        'currency',
    ];

    protected $casts = [
        'quantity'    => 'integer',
        'price'       => 'decimal:3',
        'final_price' => 'decimal:3',
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
