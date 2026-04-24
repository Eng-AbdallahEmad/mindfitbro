<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
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
}
