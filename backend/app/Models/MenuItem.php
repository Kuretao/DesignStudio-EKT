<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class MenuItem extends Model
{
    public const AREA_MAIN = 'main';

    public const AREA_SERVICES = 'services';

    protected $guarded = [];

    protected $with = ['page'];

    protected static function booted(): void
    {
        static::saving(static function (MenuItem $menuItem): void {
            if (blank($menuItem->menu_area)) {
                $menuItem->menu_area = self::AREA_MAIN;
            }

            if ($menuItem->parent_id !== null) {
                $parent = self::query()->find($menuItem->parent_id);

                if ($parent !== null) {
                    $menuItem->menu_area = $parent->menu_area;
                }
            }

            if (blank($menuItem->label_ru) && filled($menuItem->label)) {
                $menuItem->label_ru = $menuItem->label;
            }

            if (blank($menuItem->description_ru) && filled($menuItem->description)) {
                $menuItem->description_ru = $menuItem->description;
            }

            if (blank($menuItem->label) && filled($menuItem->label_ru)) {
                $menuItem->label = $menuItem->label_ru;
            }

            if (blank($menuItem->description) && filled($menuItem->description_ru)) {
                $menuItem->description = $menuItem->description_ru;
            }

            if ($menuItem->page_id === null) {
                return;
            }

            $page = Page::query()->find($menuItem->page_id);

            if ($page === null) {
                return;
            }

            if (blank($menuItem->label_ru) && blank($menuItem->label)) {
                $menuItem->label_ru = $page->title;
            }

            if (blank($menuItem->label)) {
                $menuItem->label = $menuItem->label_ru ?: $page->title;
            }

            if (blank($menuItem->href)) {
                $menuItem->href = '/' . ltrim($page->slug, '/');
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function getEffectiveImageAttribute(): ?string
    {
        if (! empty($this->image_file)) {
            return Storage::disk('public')->url($this->image_file);
        }

        return $this->image ?: null;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('position');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function siteHref(): ?string
    {
        if ($this->page_id !== null) {
            $page = $this->relationLoaded('page')
                ? $this->getRelation('page')
                : $this->page()->first();

            if (! $page?->is_published) {
                return null;
            }

            return '/' . ltrim($page->slug, '/');
        }

        return filled($this->href) ? $this->href : null;
    }

    public function labelRu(): string
    {
        return $this->label_ru ?: $this->label;
    }

    public function labelEn(): ?string
    {
        return filled($this->label_en) ? $this->label_en : null;
    }

    public function descriptionRu(): ?string
    {
        return filled($this->description_ru) ? $this->description_ru : $this->description;
    }

    public function descriptionEn(): ?string
    {
        return filled($this->description_en) ? $this->description_en : null;
    }
}
