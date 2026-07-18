<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
