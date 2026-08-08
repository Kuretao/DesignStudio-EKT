<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Review\Pages;

use App\MoonShine\Resources\Review\ReviewResource;
use App\MoonShine\Support\CmsFieldSets;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Components\Layout\Box;

/**
 * @extends FormPage<ReviewResource>
 */
class ReviewFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [Box::make(CmsFieldSets::for('review'))];
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'name_ru' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'service_ru' => ['nullable', 'string', 'max:255'],
            'service_en' => ['nullable', 'string', 'max:255'],
            'title_ru' => ['nullable', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'date_ru' => ['nullable', 'string', 'max:255'],
            'date_en' => ['nullable', 'string', 'max:255'],
            'text_ru' => ['nullable', 'string', 'max:10000'],
            'text_en' => ['nullable', 'string', 'max:10000'],
            'admin_reply_ru' => ['nullable', 'string', 'max:10000'],
            'admin_reply_en' => ['nullable', 'string', 'max:10000'],
            'image' => ['nullable', 'string', 'max:2048'],
            'position' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }

    public function validationMessages(): array
    {
        return [
            'name_ru.required' => 'Укажите имя автора отзыва.',
            'position.required' => 'Укажите позицию отзыва в списке.',
            'position.integer' => 'Позиция должна быть целым числом.',
            '*.max' => 'Одно из полей отзыва превышает допустимую длину.',
        ];
    }
}
