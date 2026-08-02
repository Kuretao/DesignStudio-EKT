<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\UiText;

use App\Models\UiText;
use App\MoonShine\Resources\UiText\Pages\UiTextDetailPage;
use App\MoonShine\Resources\UiText\Pages\UiTextFormPage;
use App\MoonShine\Resources\UiText\Pages\UiTextIndexPage;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Laravel\Resources\ModelResource;

/**
 * @extends ModelResource<UiText, UiTextIndexPage, UiTextFormPage, UiTextDetailPage>
 */
class UiTextResource extends ModelResource
{
    protected string $model = UiText::class;

    protected string $title = 'Постоянные тексты блоков';

    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            UiTextIndexPage::class,
            UiTextFormPage::class,
            UiTextDetailPage::class,
        ];
    }
}
