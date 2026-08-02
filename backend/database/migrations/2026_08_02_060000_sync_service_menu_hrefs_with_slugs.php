<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $normalize = static function (?string $value): string {
            $value = html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $value = str_replace('ё', 'е', mb_strtolower($value));
            $value = preg_replace('/\s+/u', ' ', trim($value)) ?? '';
            $value = preg_replace('/\d+$/u', '', $value) ?? '';

            return trim($value);
        };

        $services = DB::table('services')
            ->select(['slug', 'title', 'title_ru'])
            ->whereNotNull('slug')
            ->get()
            ->map(static fn (object $service): array => [
                'slug' => '/' . ltrim((string) $service->slug, '/'),
                'title' => $normalize($service->title_ru ?: $service->title),
            ])
            ->filter(static fn (array $service): bool => $service['slug'] !== '/' && $service['title'] !== '')
            ->values();

        DB::table('menu_items')
            ->where('menu_area', 'services')
            ->orderBy('id')
            ->get()
            ->each(static function (object $item) use ($services, $normalize): void {
                $label = $normalize($item->label_ru ?: $item->label);

                if ($label === '') {
                    return;
                }

                $service = $services->first(static fn (array $service): bool => $service['title'] === $label);

                if ($service === null || $item->href === $service['slug']) {
                    return;
                }

                DB::table('menu_items')
                    ->where('id', $item->id)
                    ->update([
                        'href' => $service['slug'],
                        'updated_at' => now(),
                    ]);
            });
    }

    public function down(): void
    {
        // Do not roll menu links back: slugs may have changed again after deploy.
    }
};
