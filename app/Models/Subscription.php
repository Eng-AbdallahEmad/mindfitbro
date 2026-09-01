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

    /**
     * Customer-facing reference shown on the payment-result page and any
     * future receipt — deliberately NOT the raw auto-increment id (never
     * exposed in the UI). Computed, not stored: fully deterministic from
     * created_at + id, so no migration/backfill is needed and it can never
     * drift from the row it describes.
     */
    public function invoiceNumber(): string
    {
        $year = $this->created_at?->format('Y') ?? now()->format('Y');

        return "MFB-{$year}-" . str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Human-readable payment method label for display only. Paymob doesn't
     * let us distinguish card vs. wallet without persisting source_data.type
     * from the webhook (out of scope — webhook logic is untouched here), so
     * Paymob orders get one generic label. Manual orders key
     * payment_method_key by CURRENCY (config/payment.php 'manual', one
     * account per currency) — 'EGP' is InstaPay, everything else is a bank
     * transfer.
     */
    public function paymentMethodLabel(): string
    {
        if ($this->payment_gateway === self::GATEWAY_MANUAL) {
            return match ($this->payment_method_key) {
                'EGP'   => 'InstaPay',
                default => 'تحويل بنكي',
            };
        }

        return 'بطاقة / محفظة إلكترونية (Paymob)';
    }

    /**
     * When THIS review actually started — payment_intended_at, refreshed on
     * a mid-flight switch-to-manual, not created_at (which could be days
     * earlier if the row started as a card attempt). Null for anything that
     * isn't currently an unreviewed manual order — staleness is meaningless
     * outside that one state.
     */
    public function reviewWaitingSince(): ?\Carbon\Carbon
    {
        if ($this->payment_gateway !== self::GATEWAY_MANUAL || $this->status !== self::STATUS_PENDING_REVIEW) {
            return null;
        }

        return $this->payment_intended_at ?? $this->created_at;
    }

    /**
     * Surfacing only (step 6) — never used to auto-reject or auto-expire
     * anything, purely a display signal for the admin list/detail pages.
     */
    public function reviewStalenessLevel(): ?string
    {
        $since = $this->reviewWaitingSince();

        if (! $since) {
            return null;
        }

        $hours = $since->diffInHours(now());

        if ($hours >= (int) config('payment.manual_review_thresholds.urgent_hours', 168)) {
            return 'urgent';
        }

        if ($hours >= (int) config('payment.manual_review_thresholds.warning_hours', 48)) {
            return 'warning';
        }

        return 'normal';
    }

    protected static function booted(): void
    {
        static::deleting(function (Subscription $subscription) {
            $subscription->meetingBookings()->delete();
        });
    }
}