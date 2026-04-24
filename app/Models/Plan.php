<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'icon',
        'icon_bg',
        'icon_color',
        'desc',
        'price',
        'yearly_price',
        'popular',
        'btn_class',
        'duration_days',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'popular'   => 'boolean',
        'is_active' => 'boolean',
        'price'     => 'decimal:2',
    ];

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

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}