<?php

namespace App\Models;

use App\Support\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
    use HasTranslations;

    protected $guarded = [];

    protected array $translatable = [
        'title',
        'category',
        'location',
        'description',
        'featured_label',
        'featured_title',
        'featured_description',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function getEffectiveImageAttribute(): ?string
    {
        return ! empty($this->image_file)
            ? Storage::disk('public')->url($this->image_file)
            : ($this->image ?: null);
    }

    public function getEffectiveBeforeImageAttribute(): ?string
    {
        return ! empty($this->before_image_file)
            ? Storage::disk('public')->url($this->before_image_file)
            : ($this->before_image ?: null);
    }

    public function getEffectiveAfterImageAttribute(): ?string
    {
        return ! empty($this->after_image_file)
            ? Storage::disk('public')->url($this->after_image_file)
            : ($this->after_image ?: null);
    }

    public function getEffectiveFeaturedImageAttribute(): ?string
    {
        return ! empty($this->featured_image_file)
            ? Storage::disk('public')->url($this->featured_image_file)
            : ($this->featured_image ?: null);
    }
}
