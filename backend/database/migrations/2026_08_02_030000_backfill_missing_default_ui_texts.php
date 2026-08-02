<?php

use App\Support\DefaultUiTexts;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $position = (int) DB::table('ui_texts')->max('position');
        $now = now();

        foreach (DefaultUiTexts::rows() as $row) {
            $position++;

            DB::table('ui_texts')->insertOrIgnore([
                'key' => $row['key'],
                'group' => $row['group'],
                'label' => $row['label'],
                'value_ru' => $row['value_ru'] ?? null,
                'value_en' => $row['value_en'] ?? null,
                'description' => $row['description'] ?? null,
                'position' => $position,
                'is_active' => $row['is_active'] ?? true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
};
