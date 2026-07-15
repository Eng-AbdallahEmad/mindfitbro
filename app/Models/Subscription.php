<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    // New-flow statuses
    const STATUS_PENDING_REVIEW = 'pending_review';
    const STATUS_APPROVED       = 'approved';
    const STATUS_ACTIVE         = 'active';
    const STATUS_EXPIRED        = 'expired';
    const STATUS_REJECTED       = 'rejected';
    // Legacy status (pre-Phase A records)
    const STATUS_CANCELLED      = 'cancelled';

    protected $fillable = [
        'user_id',
        'guest_name',
        'guest_email',
        'guest_token',
        'plan_id',
        'start_date',
        'end_date',
        'status',
        'plans_snapshot',
        'duration_months',
        'subtotal',
        'coupon_discount',
        'total',
        'coupon_code',
        'currency',
        'payment_method_key',
        'receipt_path',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'journey_started_at',
    ];

    protected $casts = [
        'start_date'      => 'date',
        'end_date'        => 'date',
        'plans_snapshot'  => 'array',
        'duration_months' => 'integer',
        'subtotal'        => 'decimal:3',
        'coupon_discount' => 'decimal:3',
        'total'           => 'decimal:3',
        'reviewed_at'          => 'datetime',
        'journey_started_at'   => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function meetingBookings()
    {
        return $this->hasMany(MeetingBooking::class);
    }

    public function familyInvitations()
    {
        return $this->hasMany(FamilyInvitation::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (Subscription $subscription) {
            $subscription->meetingBookings()->delete();
        });
    }
}