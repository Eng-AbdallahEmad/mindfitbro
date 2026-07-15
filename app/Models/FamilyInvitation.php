<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyInvitation extends Model
{
    protected $fillable = [
        'subscription_id',
        'inviter_user_id',
        'invitee_email',
        'invitee_name',
        'coupon_id',
        'status',
        'sent_at',
        'redeemed_at',
    ];

    protected $casts = [
        'sent_at'      => 'datetime',
        'redeemed_at'  => 'datetime',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'inviter_user_id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function markExpired(): void
    {
        $this->update(['status' => 'expired']);
    }

    public function markRedeemed(): void
    {
        $this->update(['status' => 'redeemed', 'redeemed_at' => now()]);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
