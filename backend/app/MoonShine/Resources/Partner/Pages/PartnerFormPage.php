<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Partner\Pages;

use App\MoonShine\Resources\Partner\PartnerResource;
use App\MoonShine\Support\CmsFieldSets;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Components\Layout\Box;

/**
 * @extends FormPage<PartnerResource>
 */
class PartnerFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [Box::make(CmsFieldSets::for('partner'))];
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'name_ru' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'note_ru' => ['nullable', 'string', 'max:1000'],
            'note_en' => ['nullable', 'string', 'max:1000'],
            'logo' => ['nullable', 'string', 'max:2048'],
            'logo_file' => ['nullable'],
            'position' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function validationMessages(): array
    {
        return [
            'name_ru.required' => 'Укажите русское название партнёра.',
            'position.required' => 'Укажите позицию партнёра в списке.',
            'position.integer' => 'Позиция должна быть целым числом.',
            '*.max' => 'Одно из полей партнёра превышает допустимую длину.',
        ];
    }
}
