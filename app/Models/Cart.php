<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'is_yearly' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
