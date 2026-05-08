<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'coach_id',
        'attended_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'attended_at' => 'date',
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
