<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ServiceDirectionController extends Controller
{
    public function updateHero(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'direction_ids' => ['nullable', 'array', 'max:3'],
            'direction_ids.*' => [
                'nullable',
                'integer',
                'distinct',
                Rule::exists('menu_items', 'id')->where(
                    static fn ($query) => $query
                        ->where('menu_area', MenuItem::AREA_SERVICES)
                        ->whereNull('parent_id')
                ),
            ],
        ], [
            'direction_ids.max' => 'В первом экране можно показать не больше трех направлений.',
            'direction_ids.*.distinct' => 'В каждом слоте нужно выбрать отдельное направление.',
            'direction_ids.*.exists' => 'Одно из выбранных направлений больше не существует.',
        ]);

        $directionIds = collect($data['direction_ids'] ?? [])
            ->filter(static fn (mixed $id): bool => filled($id))
            ->map(static fn (mixed $id): int => (int) $id)
            ->values();

        DB::transaction(function () use ($directionIds): void {
            MenuItem::query()
                ->where('menu_area', MenuItem::AREA_SERVICES)
                ->whereNull('parent_id')
                ->update([
                    'show_in_services_hero' => false,
                    'services_hero_position' => null,
                ]);

            $directionIds->each(function (int $directionId, int $index): void {
                MenuItem::query()
                    ->whereKey($directionId)
                    ->update([
                        'show_in_services_hero' => true,
                        'services_hero_position' => $index + 1,
                    ]);
            });
        });

        return back()->with('success', 'Карточки первого экрана сохранены.');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $direction = new MenuItem();
        $this->fillDirection($direction, $data, $request);
        $direction->save();

        $this->syncServices($direction, $data['service_ids'] ?? []);

        return back()->with('success', 'Направление создано.');
    }

    public function update(Request $request, MenuItem $direction): RedirectResponse
    {
        $this->abortUnlessServiceDirection($direction);

        $data = $this->validated($request);
        $this->fillDirection($direction, $data, $request);
        $direction->save();

        $this->syncServices($direction, $data['service_ids'] ?? []);

        return back()->with('success', 'Направление сохранено.');
    }

    public function destroy(MenuItem $direction): RedirectResponse
    {
        $this->abortUnlessServiceDirection($direction);

        Service::query()
            ->where('service_direction_id', $direction->id)
            ->get()
            ->each(function (Service $service): void {
                $service->service_direction_id = null;
                $service->save();
            });

        $direction->children()->delete();
        $direction->delete();

        return back()->with('success', 'Направление удалено, услуги остались без направления.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'label_ru' => ['required', 'string', 'max:255'],
            'label_en' => ['nullable', 'string', 'max:255'],
            'description_ru' => ['nullable', 'string', 'max:2000'],
            'description_en' => ['nullable', 'string', 'max:2000'],
            'image_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,avif', 'max:15360'],
            'image' => ['nullable', 'string', 'max:2048'],
            'image_alt_ru' => ['nullable', 'string', 'max:255'],
            'image_alt_en' => ['nullable', 'string', 'max:255'],
            'position' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
            'remove_image_file' => ['nullable', 'boolean'],
            'service_ids' => ['nullable', 'array'],
            'service_ids.*' => ['integer', Rule::exists('services', 'id')],
        ], [
            'label_ru.required' => 'Напишите название направления.',
            'position.required' => 'Укажите порядок направления.',
            'image_file.image' => 'Загрузите изображение направления в формате JPG, PNG, WEBP или AVIF.',
            'image_file.max' => 'Картинка направления слишком тяжелая. Максимум 15 МБ.',
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function fillDirection(MenuItem $direction, array $data, Request $request): void
    {
        $direction->menu_area = MenuItem::AREA_SERVICES;
        $direction->parent_id = null;
        $direction->page_id = null;
        $direction->href = $direction->href ?: $this->directionHref((string) $data['label_ru']);
        $direction->label = $data['label_ru'];
        $direction->label_ru = $data['label_ru'];
        $direction->label_en = $data['label_en'] ?? null;
        $direction->description = $data['description_ru'] ?? null;
        $direction->description_ru = $data['description_ru'] ?? null;
        $direction->description_en = $data['description_en'] ?? null;
        $direction->image = $data['image'] ?? null;
        $direction->image_alt_ru = $data['image_alt_ru'] ?? null;
        $direction->image_alt_en = $data['image_alt_en'] ?? null;
        $direction->position = (int) $data['position'];
        $direction->is_active = (bool) ($data['is_active'] ?? false);

        if ($request->boolean('remove_image_file') && filled($direction->image_file)) {
            Storage::disk('public')->delete((string) $direction->image_file);
            $direction->image_file = null;
        }

        if ($request->hasFile('image_file')) {
            if (filled($direction->image_file)) {
                Storage::disk('public')->delete((string) $direction->image_file);
            }

            $direction->image_file = $request->file('image_file')?->store('service-directions', 'public');
        }
    }

    private function directionHref(string $title): string
    {
        $slug = Str::slug($title);

        if ($slug === '') {
            $slug = 'direction-' . now()->timestamp;
        }

        $base = '/services#' . $slug;
        $href = $base;
        $counter = 2;

        while (MenuItem::query()
            ->where('menu_area', MenuItem::AREA_SERVICES)
            ->where('href', $href)
            ->exists()
        ) {
            $href = $base . '-' . $counter;
            $counter++;
        }

        return $href;
    }

    /**
     * @param array<int, mixed> $serviceIds
     */
    private function syncServices(MenuItem $direction, array $serviceIds): void
    {
        $serviceIds = collect($serviceIds)
            ->map(static fn (mixed $id): int => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        Service::query()
            ->where('service_direction_id', $direction->id)
            ->whereNotIn('id', $serviceIds)
            ->get()
            ->each(function (Service $service): void {
                $service->service_direction_id = null;
                $service->save();
            });

        Service::query()
            ->whereIn('id', $serviceIds)
            ->get()
            ->each(function (Service $service) use ($direction): void {
                $service->service_direction_id = $direction->id;
                $service->save();
            });
    }

    private function abortUnlessServiceDirection(MenuItem $direction): void
    {
        abort_unless(
            $direction->menu_area === MenuItem::AREA_SERVICES && $direction->parent_id === null,
            404
        );
    }
}
