<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Promo\Pages;

use App\MoonShine\Resources\Promo\PromoResource;
use App\MoonShine\Support\CmsFieldSets;
use Illuminate\Validation\Rule;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Components\Layout\Box;

/**
 * @extends FormPage<PromoResource>
 */
class PromoFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [Box::make(CmsFieldSets::for('promo'))];
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:[a-z0-9-]*[a-z0-9])?$/', Rule::unique('promos', 'slug')->ignore($item->getKey())],
            'title_ru' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'badge_ru' => ['nullable', 'string', 'max:255'],
            'badge_en' => ['nullable', 'string', 'max:255'],
            'highlight_ru' => ['nullable', 'string', 'max:255'],
            'highlight_en' => ['nullable', 'string', 'max:255'],
            'valid_until_ru' => ['nullable', 'string', 'max:255'],
            'valid_until_en' => ['nullable', 'string', 'max:255'],
            'description_ru' => ['nullable', 'string', 'max:5000'],
            'description_en' => ['nullable', 'string', 'max:5000'],
            'conditions_ru' => ['nullable', 'string', 'max:10000'],
            'conditions_en' => ['nullable', 'string', 'max:10000'],
            'image' => ['nullable', 'string', 'max:2048'],
            'image_file' => ['nullable'],
            'position' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function validationMessages(): array
    {
        return [
            'slug.required' => 'Укажите адрес акции (slug).',
            'slug.regex' => 'Slug может содержать только латинские буквы, цифры и дефисы.',
            'slug.unique' => 'Такой адрес уже занят другой акцией.',
            'title_ru.required' => 'Укажите русский заголовок акции.',
            'position.required' => 'Укажите позицию акции в списке.',
            'position.integer' => 'Позиция должна быть целым числом.',
            '*.max' => 'Одно из полей превышает допустимую длину.',
        ];
    }
}
