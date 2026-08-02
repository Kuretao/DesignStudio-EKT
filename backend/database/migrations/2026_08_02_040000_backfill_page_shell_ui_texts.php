<?php

use App\Support\DefaultUiTexts;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        foreach (array_chunk(DefaultUiTexts::rows(), 100) as $chunk) {
            DB::table('ui_texts')->insertOrIgnore(array_map(static function (array $row) use ($now): array {
                return [
                    ...$row,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }, $chunk));
        }
    }

    public function down(): void
    {
        // Не удаляем тексты: редактор мог уже изменить эти строки после деплоя.
    }
};
