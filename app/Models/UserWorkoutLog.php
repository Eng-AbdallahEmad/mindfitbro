<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWorkoutLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'workout_day_id',
        'date',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workoutDay()
    {
        return $this->belongsTo(WorkoutDay::class);
    }
}