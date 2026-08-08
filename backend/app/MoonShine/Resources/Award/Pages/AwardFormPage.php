<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Award\Pages;

use App\MoonShine\Resources\Award\AwardResource;
use App\MoonShine\Support\CmsFieldSets;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Components\Layout\Box;

/**
 * @extends FormPage<AwardResource>
 */
class AwardFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [Box::make(CmsFieldSets::for('award'))];
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'title_ru' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'issuer_ru' => ['nullable', 'string', 'max:255'],
            'issuer_en' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'string', 'max:32'],
            'description_ru' => ['nullable', 'string', 'max:5000'],
            'description_en' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'string', 'max:2048'],
            'image_file' => ['nullable'],
            'position' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function validationMessages(): array
    {
        return [
            'title_ru.required' => 'Укажите русский заголовок награды.',
            'position.required' => 'Укажите позицию награды в списке.',
            'position.integer' => 'Позиция должна быть целым числом.',
            '*.max' => 'Одно из полей награды превышает допустимую длину.',
        ];
    }
}
