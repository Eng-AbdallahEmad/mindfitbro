<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FxRate extends Model
{
    protected $fillable = ['currency', 'rate_to_egp', 'source', 'fetched_at'];

    protected $casts = [
        'rate_to_egp' => 'decimal:6',
        'fetched_at' => 'datetime',
    ];

    public function ageInHours(): float
    {
        return $this->fetched_at->diffInHours(now());
    }
}
