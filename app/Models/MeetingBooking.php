<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MeetingBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'meeting_date',
        'meeting_time',
        'meet_link',
        'status',
        'notes',
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'meeting_time' => 'datetime:H:i',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}