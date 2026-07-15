<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoachRating extends Model
{
    protected $fillable = [
        'subscription_id',
        'user_id',
        'coach_id',
        'stars',
        'comment',
    ];

    protected $casts = [
        'stars' => 'integer',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }
}
