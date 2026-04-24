<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'start_date',
        'end_date',
        'status',
        'plans_snapshot',
        'is_yearly',
        'subtotal',
        'coupon_discount',
        'yearly_discount',
        'total',
        'coupon_code',
    ];

    protected $casts = [
        'start_date'      => 'date',
        'end_date'        => 'date',
        'plans_snapshot'  => 'array',
        'is_yearly'       => 'boolean',
        'subtotal'        => 'decimal:2',
        'coupon_discount' => 'decimal:2',
        'yearly_discount' => 'decimal:2',
        'total'           => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function meetingBookings()
    {
        return $this->hasMany(MeetingBooking::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (Subscription $subscription) {
            $subscription->meetingBookings()->delete();
        });
    }
}