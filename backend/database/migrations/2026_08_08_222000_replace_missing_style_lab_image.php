<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('ui_texts')
            ->where('key', 'styleLab.styles.classic.image')
            ->where(function ($query): void {
                $query
                    ->where('value_ru', 'cms/peregorodka-bar-20260803074652-scjya6.webp')
                    ->orWhere('value_en', 'cms/peregorodka-bar-20260803074652-scjya6.webp');
            })
            ->update([
                'value_ru' => '/images/cms/warm-interior-after.webp',
                'value_en' => '/images/cms/warm-interior-after.webp',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // A deleted media reference must not be restored.
    }
};
