<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date_of_birth',
        'height',
        'start_weight',
        'current_weight',
        'goal_weight',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth
            ? $this->date_of_birth->age  // Carbon بيحسبه تلقائي
            : null;
    }
}