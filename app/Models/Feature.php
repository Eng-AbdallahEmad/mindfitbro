<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function plans()
    {
        return $this->belongsToMany(Plan::class)
            ->withPivot('is_included', 'sort_order')
            ->withTimestamps();
    }
}