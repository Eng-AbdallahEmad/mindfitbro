<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'phone',
        'email',
        'gender',
        'password',
        'role',
        'status',
        'terms_accepted_at',
        'profile_completed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'    => 'datetime',
            'terms_accepted_at'    => 'datetime',
            'profile_completed_at' => 'datetime',
            'password'             => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (User $user) {
            // Delete receipt files before subscriptions are cascade-deleted by the DB
            $user->subscriptions()
                ->whereNotNull('receipt_path')
                ->pluck('receipt_path')
                ->each(fn ($path) => Storage::disk('local')->delete($path));

            // meeting_bookings.user_id and subscription_id are both nullOnDelete —
            // delete the rows outright (cover user_id path + subscription_id path)
            $subscriptionIds = $user->subscriptions()->pluck('id');
            if ($subscriptionIds->isNotEmpty()) {
                MeetingBooking::whereIn('subscription_id', $subscriptionIds)->delete();
            }
            $user->meetingBookings()->delete();

            // subscriptions.user_id is nullOnDelete (changed for guest checkout) —
            // delete explicitly so they don't linger as orphaned "guest" records.
            // coach_ratings and family_invitations cascade from subscription delete.
            $user->subscriptions()->delete();

            // carts.user_id is nullOnDelete — delete carts (cart_items cascade via DB)
            $user->carts()->delete();
        });
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function programs()
    {
        return $this->hasMany(Program::class);
    }

    public function workoutLogs()
    {
        return $this->hasMany(UserWorkoutLog::class);
    }

    public function weightLogs()
    {
        return $this->hasMany(WeightLog::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function meetingBookings()
    {
        return $this->hasMany(MeetingBooking::class);
    }

    public function evaluationsAsCoach()
    {
        return $this->hasMany(MemberEvaluation::class, 'coach_id');
    }

    public function coachRatings()
    {
        return $this->hasMany(CoachRating::class, 'coach_id');
    }

    public function traineeAssessments()
    {
        return $this->hasMany(TraineeAssessment::class);
    }

    // Average rating received as a coach (0.0–5.0, null if no ratings)
    public function getAvgRatingAttribute(): ?float
    {
        $avg = $this->coachRatings()->avg('stars');
        return $avg !== null ? round((float) $avg, 1) : null;
    }
}
