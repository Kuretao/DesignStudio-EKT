<?php

use App\Models\UiText;
use App\Support\DefaultUiTexts;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        collect(DefaultUiTexts::rows())
            ->where('group', 'services-page')
            ->values()
            ->each(function (array $row, int $index): void {
                UiText::query()->firstOrCreate(
                    ['key' => $row['key']],
                    [
                        ...$row,
                        'position' => $index,
                        'is_active' => true,
                    ]
                );
            });
    }

    public function down(): void
    {
        UiText::query()->where('group', 'services-page')->delete();
    }
};
