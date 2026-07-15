<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanPrice extends Model
{
    const CURRENCIES      = ['SAR', 'EGP', 'TND', 'USD'];
    const DURATIONS       = [3, 6];

    protected $fillable = ['plan_id', 'currency', 'duration_months', 'price'];

    protected $casts = [
        'price'           => 'decimal:3',
        'duration_months' => 'integer',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
