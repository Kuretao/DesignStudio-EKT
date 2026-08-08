<?php

use App\Models\MenuItem;
use App\Models\Service;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $publishedServiceHrefs = Service::query()
            ->where('is_published', true)
            ->pluck('slug')
            ->filter(static fn (mixed $slug): bool => filled($slug))
            ->map(static fn (string $slug): string => '/' . ltrim($slug, '/'))
            ->all();

        MenuItem::query()
            ->where('menu_area', MenuItem::AREA_SERVICES)
            ->whereNotNull('parent_id')
            ->whereNotNull('href')
            ->get()
            ->filter(static function (MenuItem $item) use ($publishedServiceHrefs): bool {
                $href = $item->siteHref();

                return filled($href)
                    && ! in_array('/' . ltrim($href, '/'), $publishedServiceHrefs, true);
            })
            ->each(static fn (MenuItem $item): ?bool => $item->delete());
    }

    public function down(): void
    {
        //
    }
};
