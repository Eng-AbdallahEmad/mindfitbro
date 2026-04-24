<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'day_name',
        'day_order',
        'type',
    ];

    protected $casts = [
        'day_order' => 'integer',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function workoutLogs()
    {
        return $this->hasMany(UserWorkoutLog::class);
    }
}