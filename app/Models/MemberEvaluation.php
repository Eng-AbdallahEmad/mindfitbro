<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberEvaluation extends Model
{
    protected $fillable = [
        'user_id',
        'coach_id',
        'weight',
        'height',
        'body_fat_percentage',
        'muscle_mass',
        'fitness_level',
        'notes',
        'evaluated_at',
    ];

    protected $casts = [
        'evaluated_at'        => 'date',
        'weight'              => 'float',
        'height'              => 'float',
        'body_fat_percentage' => 'float',
        'muscle_mass'         => 'float',
    ];

    public function member()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }
}
