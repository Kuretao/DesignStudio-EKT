<?php

use App\Models\MenuItem;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        MenuItem::query()
            ->where('menu_area', MenuItem::AREA_SERVICES)
            ->whereNull('parent_id')
            ->where('is_active', false)
            ->get()
            ->filter(static fn (MenuItem $item): bool => $item->children()
                ->where('menu_area', MenuItem::AREA_SERVICES)
                ->where('is_active', true)
                ->exists())
            ->each(static fn (MenuItem $item) => $item
                ->forceFill(['is_active' => true])
                ->saveQuietly());
    }

    public function down(): void
    {
        //
    }
};
