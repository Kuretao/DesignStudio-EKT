<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\PageBlock;

use Illuminate\Database\Eloquent\Model;
use App\Models\PageBlock;
use App\MoonShine\Resources\PageBlock\Pages\PageBlockIndexPage;
use App\MoonShine\Resources\PageBlock\Pages\PageBlockFormPage;
use App\MoonShine\Resources\PageBlock\Pages\PageBlockDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;

/**
 * @extends ModelResource<PageBlock, PageBlockIndexPage, PageBlockFormPage, PageBlockDetailPage>
 */
class PageBlockResource extends ModelResource
{
    protected string $model = PageBlock::class;

    protected string $title = 'Блоки страниц';

    protected function activeActions(): ListOf
    {
        return parent::activeActions()->except(Action::CREATE);
    }
    
    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            PageBlockIndexPage::class,
            PageBlockFormPage::class,
            PageBlockDetailPage::class,
        ];
    }
}
