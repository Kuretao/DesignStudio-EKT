<?php

namespace App\Models;

use App\Support\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Service extends Model
{
    use HasTranslations;

    protected $guarded = [];

    protected array $translatable = [
        'title',
        'eyebrow',
        'text',
        'price',
        'timeline',
        'pdf_title',
        'deliverables',
        'benefits',
        'process',
    ];

    protected static function booted(): void
    {
        static::saved(static function (Service $service): void {
            if (! $service->wasChanged('slug')) {
                return;
            }

            $oldSlug = (string) $service->getOriginal('slug');
            $newSlug = (string) $service->slug;

            if ($oldSlug === '' || $newSlug === '' || $oldSlug === $newSlug) {
                return;
            }

            MenuItem::query()
                ->where('menu_area', MenuItem::AREA_SERVICES)
                ->whereIn('href', [
                    '/' . ltrim($oldSlug, '/'),
                    ltrim($oldSlug, '/'),
                ])
                ->update([
                    'href' => '/' . ltrim($newSlug, '/'),
                    'updated_at' => now(),
                ]);
        });
    }

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'is_home_item' => 'boolean',
        ];
    }

    public function getEffectiveImageAttribute(): ?string
    {
        if (! empty($this->image_file)) {
            return Storage::disk('public')->url($this->image_file);
        }

        return $this->image ?: null;
    }

    public function getEffectivePdfAttribute(): ?string
    {
        if (! empty($this->pdf_file)) {
            return Storage::disk('public')->url($this->pdf_file);
        }

        return null;
    }
}
