<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\UiText\Pages;

use App\MoonShine\Resources\UiText\UiTextResource;
use App\MoonShine\Support\CmsFieldSets;
use Illuminate\Validation\Rule;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Components\Layout\Box;

/**
 * @extends FormPage<UiTextResource>
 */
class UiTextFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [Box::make(CmsFieldSets::for('ui_text'))];
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'key' => ['required', 'string', 'max:255', Rule::unique('ui_texts', 'key')->ignore($item->getKey())],
            'group' => ['nullable', 'string', 'max:255'],
            'label' => ['required', 'string', 'max:255'],
            'value_ru' => ['nullable', 'string'],
            'value_en' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'position' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function validationMessages(): array
    {
        return [
            'key.required' => 'Укажите системный ключ текста.',
            'key.unique' => 'Такой ключ уже существует. Откройте существующую запись вместо создания дубля.',
            'label.required' => 'Укажите понятное название текста для редактора.',
            'position.required' => 'Укажите позицию текста в списке.',
            'position.integer' => 'Позиция должна быть целым числом.',
        ];
    }
}
