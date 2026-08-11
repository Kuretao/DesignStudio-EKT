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
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

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
        $key = (string) ($this->getResource()->getItem()?->key ?? '');

        if ($this->isMediaKey($key)) {
            return [
                Box::make('Изображения блока', [
                    ID::make()->sortable(),
                    Text::make('Группа', 'group')->sortable(),
                    Text::make('Ключ', 'key')->required()->sortable(),
                    Text::make('Название в админке', 'label')->required(),
                    Image::make('Загрузить изображение', 'media_file')
                        ->disk('public')
                        ->dir('page-content')
                        ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'avif'])
                        ->removable()
                        ->hint('Загрузите новый файл или нажмите «Выбрать из галереи». Это изображение имеет приоритет над списком кадров ниже.'),
                    Textarea::make('Кадры из галереи / URL', 'value_ru')
                        ->customAttributes([
                            'data-gallery-lines' => '1',
                            'data-gallery-media' => 'image',
                        ])
                        ->hint('Для одного изображения оставьте одну строку. Для плавной смены кадров добавьте несколько изображений, по одному пути в строке.'),
                    Textarea::make('Комментарий для редактора', 'description')
                        ->hint('Подсказка о том, где используется изображение.'),
                    Number::make('Позиция', 'position')->sortable(),
                    Switcher::make('Активно', 'is_active'),
                ]),
            ];
        }

        return [Box::make(CmsFieldSets::for('ui_text'))];
    }

    private function isMediaKey(string $key): bool
    {
        return preg_match('/(?:^|\.)(?:image|images|backgroundImage|backgroundImages|cardImage|cardImages|media)(?:\d+)?$/i', $key) === 1;
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'key' => ['required', 'string', 'max:255', Rule::unique('ui_texts', 'key')->ignore($item->getKey())],
            'group' => ['nullable', 'string', 'max:255'],
            'label' => ['required', 'string', 'max:255'],
            'value_ru' => ['nullable', 'string'],
            'value_en' => ['nullable', 'string'],
            'media_file' => ['nullable'],
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
