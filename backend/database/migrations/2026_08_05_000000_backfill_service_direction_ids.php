<?php

use App\Models\MenuItem;
use App\Models\Service;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('services', 'service_direction_id')) {
            return;
        }

        $servicesBySlug = Service::query()
            ->whereNotNull('slug')
            ->get(['id', 'slug'])
            ->keyBy(static fn (Service $service): string => ltrim((string) $service->slug, '/'));

        MenuItem::query()
            ->where('menu_area', MenuItem::AREA_SERVICES)
            ->whereNotNull('parent_id')
            ->whereNotNull('href')
            ->orderBy('position')
            ->get(['id', 'parent_id', 'href'])
            ->each(static function (MenuItem $item) use ($servicesBySlug): void {
                $slug = ltrim((string) $item->href, '/');

                if ($slug === '') {
                    return;
                }

                $service = $servicesBySlug->get($slug);

                if ($service === null) {
                    return;
                }

                Service::query()
                    ->whereKey($service->id)
                    ->update(['service_direction_id' => $item->parent_id]);
            });
    }

    public function down(): void
    {
        //
    }
};
