<?php

use App\Models\UiText;
use App\Support\DefaultUiTexts;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        collect(DefaultUiTexts::rows())
            ->filter(static fn (array $row): bool => preg_match('/^styleLab\.(materials|lights)\.[^.]+\.image$/', (string) $row['key']) === 1)
            ->each(static function (array $row): void {
                UiText::query()->firstOrCreate(
                    ['key' => $row['key']],
                    [
                        'group' => $row['group'],
                        'label' => $row['label'],
                        'value_ru' => $row['value_ru'],
                        'value_en' => $row['value_en'],
                        'description' => $row['description'] ?? null,
                        'position' => (int) UiText::query()->max('position') + 10,
                        'is_active' => true,
                    ]
                );
            });
    }

    public function down(): void
    {
        UiText::query()
            ->where('key', 'like', 'styleLab.materials.%.image')
            ->orWhere('key', 'like', 'styleLab.lights.%.image')
            ->delete();
    }
};
