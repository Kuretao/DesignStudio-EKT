<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Vacancy\Pages;

use App\MoonShine\Resources\Vacancy\VacancyResource;
use App\MoonShine\Support\CmsFieldSets;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;

/**
 * @extends FormPage<VacancyResource>
 */
class VacancyFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Tabs::make([
                Tab::make('Основное', [
                    Box::make('Название и направление', [
                        Grid::make([
                            Column::make(array_slice(CmsFieldSets::vacancySection('main'), 0, 2))->columnSpan(6),
                            Column::make(array_slice(CmsFieldSets::vacancySection('main'), 2))->columnSpan(6),
                        ]),
                    ]),
                ])->icon('document-text')->active(),
                Tab::make('Условия', [
                    Box::make('Формат, место, опыт и оплата', CmsFieldSets::vacancySection('terms')),
                ])->icon('briefcase'),
                Tab::make('Описание', [
                    Box::make('Описание, обязанности и требования', CmsFieldSets::vacancySection('content')),
                ])->icon('list-bullet'),
                Tab::make('Медиа', [
                    Box::make('Изображение карточки', CmsFieldSets::vacancySection('media')),
                ])->icon('photo'),
                Tab::make('Публикация', [
                    Box::make('Порядок и видимость', CmsFieldSets::vacancySection('publish')),
                ])->icon('check-circle'),
            ])->vertical(),
        ];
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'title_ru' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'employment_ru' => ['nullable', 'string', 'max:255'],
            'employment_en' => ['nullable', 'string', 'max:255'],
            'department_ru' => ['nullable', 'string', 'max:255'],
            'department_en' => ['nullable', 'string', 'max:255'],
            'format_ru' => ['nullable', 'string', 'max:255'],
            'format_en' => ['nullable', 'string', 'max:255'],
            'experience_ru' => ['nullable', 'string', 'max:255'],
            'experience_en' => ['nullable', 'string', 'max:255'],
            'location_ru' => ['nullable', 'string', 'max:255'],
            'location_en' => ['nullable', 'string', 'max:255'],
            'salary_ru' => ['nullable', 'string', 'max:255'],
            'salary_en' => ['nullable', 'string', 'max:255'],
            'description_ru' => ['nullable', 'string', 'max:10000'],
            'description_en' => ['nullable', 'string', 'max:10000'],
            'requirements_ru' => ['nullable', 'string', 'max:10000'],
            'requirements_en' => ['nullable', 'string', 'max:10000'],
            'responsibilities_ru' => ['nullable', 'string', 'max:10000'],
            'responsibilities_en' => ['nullable', 'string', 'max:10000'],
            'perks_ru' => ['nullable', 'string', 'max:10000'],
            'perks_en' => ['nullable', 'string', 'max:10000'],
            'image' => ['nullable', 'string', 'max:2048'],
            'position' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function validationMessages(): array
    {
        return [
            'title_ru.required' => 'Укажите русский заголовок вакансии.',
            'position.required' => 'Укажите позицию вакансии в списке.',
            'position.integer' => 'Позиция должна быть целым числом.',
            '*.max' => 'Одно из полей вакансии превышает допустимую длину.',
        ];
    }
}
