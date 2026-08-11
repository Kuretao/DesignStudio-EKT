<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table): void {
            if (! Schema::hasColumn('menu_items', 'show_in_services_hero')) {
                $table->boolean('show_in_services_hero')->default(false)->after('is_active');
            }

            if (! Schema::hasColumn('menu_items', 'services_hero_position')) {
                $table->unsignedTinyInteger('services_hero_position')->nullable()->after('show_in_services_hero');
            }
        });

        $selectedDirectionIds = DB::table('menu_items')
            ->where('menu_area', 'services')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->whereExists(static function ($query): void {
                $query->selectRaw('1')
                    ->from('services')
                    ->whereColumn('services.service_direction_id', 'menu_items.id')
                    ->where('services.is_published', true);
            })
            ->orderBy('position')
            ->orderBy('id')
            ->limit(3)
            ->pluck('id');

        foreach ($selectedDirectionIds as $index => $directionId) {
            DB::table('menu_items')
                ->where('id', $directionId)
                ->update([
                    'show_in_services_hero' => true,
                    'services_hero_position' => $index + 1,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table): void {
            foreach (['services_hero_position', 'show_in_services_hero'] as $column) {
                if (Schema::hasColumn('menu_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
