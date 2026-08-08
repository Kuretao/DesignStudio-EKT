<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Faq\Pages;

use App\MoonShine\Resources\Faq\FaqResource;
use App\MoonShine\Support\CmsFieldSets;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Components\Layout\Box;

/**
 * @extends FormPage<FaqResource>
 */
class FaqFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [Box::make(CmsFieldSets::for('faq'))];
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'question_ru' => ['required', 'string', 'max:1000'],
            'question_en' => ['nullable', 'string', 'max:1000'],
            'answer_ru' => ['required', 'string', 'max:10000'],
            'answer_en' => ['nullable', 'string', 'max:10000'],
            'position' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }

    public function validationMessages(): array
    {
        return [
            'question_ru.required' => 'Напишите вопрос на русском языке.',
            'answer_ru.required' => 'Напишите ответ на русском языке.',
            'position.required' => 'Укажите позицию вопроса в списке.',
            'position.integer' => 'Позиция должна быть целым числом.',
            '*.max' => 'Вопрос или ответ превышает допустимую длину.',
        ];
    }
}
