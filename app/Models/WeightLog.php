<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeightLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'weight',
        'logged_at',
    ];

    protected $casts = [
        'logged_at' => 'date',
        'weight' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}