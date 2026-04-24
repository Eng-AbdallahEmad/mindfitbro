<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_week_id',
        'day_name',
        'day_order',
        'type',
    ];

    public function programWeek()
    {
        return $this->belongsTo(ProgramWeek::class);
    }

    public function workoutLogs()
    {
        return $this->hasMany(UserWorkoutLog::class);
    }
}