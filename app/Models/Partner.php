<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = ['name', 'logo_path', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function getLogoSrcAttribute(): string
    {
        return asset($this->logo_path);
    }
}
