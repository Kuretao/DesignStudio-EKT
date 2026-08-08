<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Vacancy extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::saving(static function (Vacancy $vacancy): void {
            foreach ([
                'title',
                'employment',
                'department',
                'format',
                'experience',
                'location',
                'salary',
                'description',
                'requirements',
                'responsibilities',
                'perks',
            ] as $field) {
                $ruField = $field . '_ru';

                if (blank($vacancy->{$ruField}) && filled($vacancy->{$field})) {
                    $vacancy->{$ruField} = $vacancy->{$field};
                }

                if (blank($vacancy->{$field}) && filled($vacancy->{$ruField})) {
                    $vacancy->{$field} = $vacancy->{$ruField};
                }
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function localizedField(string $field, string $locale): ?string
    {
        $localized = $field . '_' . $locale;

        return filled($this->{$localized})
            ? $this->{$localized}
            : (filled($this->{$field}) ? $this->{$field} : null);
    }

    public function fieldRu(string $field): ?string
    {
        return $this->localizedField($field, 'ru');
    }

    public function fieldEn(string $field): ?string
    {
        return filled($this->{$field . '_en'}) ? $this->{$field . '_en'} : null;
    }

    public function getEffectiveImageAttribute(): ?string
    {
        return filled($this->image_file)
            ? Storage::disk('public')->url($this->image_file)
            : ($this->image ?: null);
    }
}
