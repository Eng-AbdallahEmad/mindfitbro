<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Season;

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

    // Paymob-flow statuses
    const STATUS_AWAITING_PAYMENT = 'awaiting_payment';
    const STATUS_PAYMENT_FAILED   = 'payment_failed';
    const STATUS_REFUNDED         = 'refunded';

    const GATEWAY_PAYMOB = 'paymob';
    const GATEWAY_MANUAL = 'manual';

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
        'season_id',
        'season_name',
        'season_discount_percentage',
        'season_discount',
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
        'crm_email',
        'crm_password',
        'paymob_order_id',
        'paymob_order_ids',
        'paymob_transaction_id',
        'paymob_intention_id',
        'billing_phone',
        'charged_currency',
        'charged_amount_cents',
        'fx_rate',
        'fx_rate_source',
        'payment_intended_at',
        'paid_at',
        'payment_failure_reason',
        'payment_gateway',
    ];

    protected $casts = [
        'start_date'                 => 'date',
        'end_date'                   => 'date',
        'plans_snapshot'             => 'array',
        'duration_months'            => 'integer',
        'subtotal'                   => 'decimal:3',
        'season_discount_percentage' => 'decimal:2',
        'season_discount'            => 'decimal:3',
        'coupon_discount'            => 'decimal:3',
        'total'                      => 'decimal:3',
        'reviewed_at'                => 'datetime',
        'journey_started_at'         => 'datetime',
        'crm_password'               => 'encrypted',
        'payment_intended_at'        => 'datetime',
        'paid_at'                    => 'datetime',
        'charged_amount_cents'       => 'integer',
        'fx_rate'                    => 'decimal:6',
        'paymob_order_ids'           => 'array',
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

    public function traineeAssessment()
    {
        return $this->hasOne(TraineeAssessment::class);
    }

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function scopeAwaitingPayment($query)
    {
        return $query->where('status', self::STATUS_AWAITING_PAYMENT);
    }

    public function scopePaid($query)
    {
        return $query->whereNotNull('paid_at');
    }

    public function scopeViaPaymob($query)
    {
        return $query->where('payment_gateway', self::GATEWAY_PAYMOB);
    }

    public function scopeViaManual($query)
    {
        return $query->where('payment_gateway', self::GATEWAY_MANUAL);
    }

    public function isPaid(): bool
    {
        return !is_null($this->paid_at);
    }

    public function isAwaitingPayment(): bool
    {
        return $this->status === self::STATUS_AWAITING_PAYMENT;
    }

    /**
     * Single source of truth for cents -> EGP display conversion. Nothing
     * else in the app should divide charged_amount_cents by 100 by hand.
     */
    public function chargedAmountEgp(): ?float
    {
        return is_null($this->charged_amount_cents)
            ? null
            : $this->charged_amount_cents / 100;
    }

    protected static function booted(): void
    {
        static::deleting(function (Subscription $subscription) {
            $subscription->meetingBookings()->delete();
        });
    }
}