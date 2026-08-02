<?php

use App\Support\DefaultUiTexts;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $position = (int) DB::table('ui_texts')->max('position');

        foreach (DefaultUiTexts::rows() as $row) {
            if (! str_starts_with((string) $row['key'], 'styleLab.')) {
                continue;
            }

            $position += 10;

            DB::table('ui_texts')->insertOrIgnore([
                'key' => $row['key'],
                'group' => $row['group'],
                'label' => $row['label'],
                'value_ru' => $row['value_ru'],
                'value_en' => $row['value_en'],
                'description' => $row['description'],
                'position' => $position,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        // Keep editor content: values may have been changed after deploy.
    }
};
