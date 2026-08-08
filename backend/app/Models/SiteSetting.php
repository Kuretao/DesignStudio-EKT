<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'maintenance_enabled' => 'boolean',
            'animations_enabled' => 'boolean',
            'smooth_scroll_enabled' => 'boolean',
            'page_reveal_enabled' => 'boolean',
        ];
    }
}
