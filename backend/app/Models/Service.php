<?php

namespace App\Models;

use App\Support\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'compare_eyebrow',
        'compare_title',
        'compare_text',
        'deliverables',
        'benefits',
        'process',
    ];

    protected static function booted(): void
    {
        static::saved(static function (Service $service): void {
            if (! $service->wasChanged('slug')) {
                $service->syncServiceNavigationItem();

                return;
            }

            $oldSlug = (string) $service->getOriginal('slug');
            $newSlug = (string) $service->slug;

            if ($oldSlug === '' || $newSlug === '' || $oldSlug === $newSlug) {
                $service->syncServiceNavigationItem($oldSlug ?: null);

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

            $service->syncServiceNavigationItem($oldSlug);
        });

        static::deleted(static function (Service $service): void {
            $service->deleteServiceNavigationItem();
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

    public function getEffectiveCompareBeforeImageAttribute(): ?string
    {
        if (! empty($this->compare_before_image_file)) {
            return Storage::disk('public')->url($this->compare_before_image_file);
        }

        return $this->compare_before_image ?: null;
    }

    public function getEffectiveCompareAfterImageAttribute(): ?string
    {
        if (! empty($this->compare_after_image_file)) {
            return Storage::disk('public')->url($this->compare_after_image_file);
        }

        return $this->compare_after_image ?: null;
    }

    public function direction(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'service_direction_id');
    }

    private function syncServiceNavigationItem(?string $oldSlug = null): void
    {
        $href = '/' . ltrim((string) $this->slug, '/');

        if (blank($this->slug)) {
            return;
        }

        $item = MenuItem::query()
            ->where('menu_area', MenuItem::AREA_SERVICES)
            ->where(function ($query) use ($href, $oldSlug): void {
                $query->where('href', $href)
                    ->orWhere('href', ltrim($href, '/'));

                if (filled($oldSlug)) {
                    $query->orWhere('href', '/' . ltrim((string) $oldSlug, '/'))
                        ->orWhere('href', ltrim((string) $oldSlug, '/'));
                }
            })
            ->first();

        if (! $this->service_direction_id) {
            if ($item !== null && $item->parent_id !== null) {
                $item->parent_id = null;
                $item->is_active = false;
                $item->saveQuietly();
            }

            return;
        }

        $direction = MenuItem::query()
            ->whereKey($this->service_direction_id)
            ->where('menu_area', MenuItem::AREA_SERVICES)
            ->whereNull('parent_id')
            ->first();

        if ($direction === null) {
            return;
        }

        $item ??= new MenuItem();
        $item->menu_area = MenuItem::AREA_SERVICES;
        $item->parent_id = $direction->id;
        $item->href = $href;
        $item->label = $this->fieldRu('title');
        $item->label_ru = $this->fieldRu('title');
        $item->label_en = $this->fieldEn('title');
        $item->description = null;
        $item->description_ru = null;
        $item->description_en = null;
        $item->position = $this->position ?: ((int) MenuItem::query()
            ->where('menu_area', MenuItem::AREA_SERVICES)
            ->where('parent_id', $direction->id)
            ->max('position') + 10);
        $item->is_active = (bool) $this->is_published;
        $item->saveQuietly();
    }

    private function deleteServiceNavigationItem(): void
    {
        $slug = (string) $this->slug;

        if (blank($slug)) {
            return;
        }

        MenuItem::query()
            ->where('menu_area', MenuItem::AREA_SERVICES)
            ->whereIn('href', [
                '/' . ltrim($slug, '/'),
                ltrim($slug, '/'),
            ])
            ->delete();
    }
}
