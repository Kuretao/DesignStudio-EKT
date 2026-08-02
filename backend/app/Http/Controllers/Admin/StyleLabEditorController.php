<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\MoonShine\Pages\StyleLabEditorPage;
use App\Models\UiText;
use App\Support\DefaultUiTexts;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StyleLabEditorController extends Controller
{
    public function show(StyleLabEditorPage $page): Response
    {
        return response($page->standaloneHtml());
    }

    public function update(Request $request): RedirectResponse
    {
        $payload = $request->input('texts', []);

        if (! is_array($payload)) {
            return back()->withErrors(['texts' => 'Данные редактора не распознаны.']);
        }

        $defaults = collect(DefaultUiTexts::rows())
            ->filter(static fn (array $row): bool => str_starts_with((string) $row['key'], 'styleLab.'))
            ->keyBy('key');

        foreach ($payload as $key => $values) {
            if (! is_string($key) || ! str_starts_with($key, 'styleLab.') || ! is_array($values)) {
                continue;
            }

            $default = $defaults->get($key, [
                'key' => $key,
                'group' => 'style-lab',
                'label' => $key,
                'value_ru' => '',
                'value_en' => null,
                'description' => null,
            ]);

            $value = array_key_exists('value', $values)
                ? trim((string) $values['value'])
                : null;
            $valueRu = $value ?? trim((string) ($values['ru'] ?? ''));
            $valueEn = $value ?? trim((string) ($values['en'] ?? ''));

            UiText::query()->updateOrCreate(
                ['key' => $key],
                [
                    'group' => $default['group'] ?? 'style-lab',
                    'label' => $default['label'] ?? $key,
                    'value_ru' => $valueRu,
                    'value_en' => $valueEn !== '' ? $valueEn : null,
                    'description' => $default['description'] ?? null,
                    'position' => UiText::query()->where('key', $key)->value('position')
                        ?? ((int) UiText::query()->max('position') + 10),
                    'is_active' => true,
                ]
            );
        }

        return back()->with('style_lab_status', 'Style Lab сохранен.');
    }
}
