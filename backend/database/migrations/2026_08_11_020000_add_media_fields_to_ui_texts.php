<?php

use App\Support\DefaultUiTexts;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const MEDIA_KEYS = [
        'aboutFull.hero.backgroundImages',
        'aboutFull.hero.cardImages',
        'aboutFull.work.images1',
        'aboutFull.work.images2',
        'aboutFull.work.images3',
    ];

    public function up(): void
    {
        Schema::table('ui_texts', function (Blueprint $table): void {
            $table->string('media_file')->nullable()->after('value_en');
        });

        $position = (int) DB::table('ui_texts')->max('position');
        $now = now();

        collect(DefaultUiTexts::rows())
            ->whereIn('key', self::MEDIA_KEYS)
            ->values()
            ->each(function (array $row, int $index) use ($position, $now): void {
                DB::table('ui_texts')->updateOrInsert(
                    ['key' => $row['key']],
                    [
                        ...$row,
                        'position' => $position + $index + 1,
                        'is_active' => true,
                        'updated_at' => $now,
                        'created_at' => $now,
                    ]
                );
            });
    }

    public function down(): void
    {
        DB::table('ui_texts')->whereIn('key', self::MEDIA_KEYS)->delete();

        Schema::table('ui_texts', function (Blueprint $table): void {
            $table->dropColumn('media_file');
        });
    }
};
