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
            'is_selected' => 'boolean',
            'is_virtual_tour' => 'boolean',
            'is_published' => 'boolean',
            'story_chapters' => 'array',
            'virtual_tour_scenes' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(static function (self $project): void {
            $project->normalizeGalleryPathsInUploadFields();
        });

        static::saved(static function (self $project): void {
            if (! $project->is_selected) {
                return;
            }

            static::query()
                ->whereKeyNot($project->getKey())
                ->where('is_selected', true)
                ->update(['is_selected' => false]);
        });
    }

    private function normalizeGalleryPathsInUploadFields(): void
    {
        $pairs = [
            ['image_file', 'image', 'projects/'],
            ['before_image_file', 'before_image', 'projects/'],
            ['after_image_file', 'after_image', 'projects/'],
            ['featured_image_file', 'featured_image', 'projects/featured/'],
            ['featured_gallery_image_1_file', 'featured_gallery_image_1', 'projects/featured/'],
            ['featured_gallery_image_2_file', 'featured_gallery_image_2', 'projects/featured/'],
            ['featured_gallery_image_3_file', 'featured_gallery_image_3', 'projects/featured/'],
        ];

        foreach ($pairs as [$fileField, $urlField, $uploadDirectory]) {
            $value = trim((string) $this->getAttribute($fileField));

            if ($value === '' || str_starts_with($value, $uploadDirectory)) {
                continue;
            }

            $this->setAttribute($urlField, $value);
            $this->setAttribute($fileField, null);
        }
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

    public function getEffectiveFeaturedGalleryImagesAttribute(): array
    {
        return collect([
            $this->effectiveFeaturedGalleryImage(1),
            $this->effectiveFeaturedGalleryImage(2),
            $this->effectiveFeaturedGalleryImage(3),
        ])->filter()->values()->all();
    }

    private function effectiveFeaturedGalleryImage(int $index): ?string
    {
        $file = $this->getAttribute("featured_gallery_image_{$index}_file");
        $url = $this->getAttribute("featured_gallery_image_{$index}");

        return ! empty($file)
            ? Storage::disk('public')->url($file)
            : ($url ?: null);
    }
}
