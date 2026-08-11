<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UiText extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function valueRu(): string
    {
        if (filled($this->media_file)) {
            return Storage::disk('public')->url((string) $this->media_file);
        }

        return (string) ($this->value_ru ?? '');
    }

    public function valueEn(): ?string
    {
        return filled($this->value_en) ? $this->value_en : null;
    }
}
