<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TraineeAssessment extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_id',
        'date_of_birth',
        'current_weight',
        'target_weight',
        'height',
        'primary_goal',
        'experience_level',
        'workout_days_per_week',
        'session_duration_minutes',
        'training_details',
        'nutrition',
        'health',
        'lifestyle',
        'declaration_accepted_at',
        'signature_text',
        'submitted_at',
    ];

    protected $casts = [
        'date_of_birth'            => 'date',
        'current_weight'           => 'decimal:2',
        'target_weight'            => 'decimal:2',
        'height'                   => 'decimal:2',
        'workout_days_per_week'    => 'integer',
        'session_duration_minutes' => 'integer',
        'training_details'         => 'array',
        'nutrition'                => 'array',
        'health'                   => 'array',
        'lifestyle'                => 'array',
        'declaration_accepted_at'  => 'datetime',
        'submitted_at'             => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function isSubmitted(): bool
    {
        return $this->submitted_at !== null;
    }
}
