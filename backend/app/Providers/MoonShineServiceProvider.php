<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use App\MoonShine\Pages\ImageGalleryPage;
use App\MoonShine\Pages\PageContentBuilderPage;
use App\MoonShine\Resources\MoonShineUser\MoonShineUserResource;
use App\MoonShine\Resources\MoonShineUserRole\MoonShineUserRoleResource;
use App\MoonShine\Resources\SiteSetting\SiteSettingResource;
use App\MoonShine\Resources\MenuItem\MenuItemResource;
use App\MoonShine\Resources\Page\PageResource;
use App\MoonShine\Resources\PageBlock\PageBlockResource;
use App\MoonShine\Resources\Project\ProjectResource;
use App\MoonShine\Resources\Service\ServiceResource;
use App\MoonShine\Resources\NewsArticle\NewsArticleResource;
use App\MoonShine\Resources\Promo\PromoResource;
use App\MoonShine\Resources\Award\AwardResource;
use App\MoonShine\Resources\Partner\PartnerResource;
use App\MoonShine\Resources\Review\ReviewResource;
use App\MoonShine\Resources\Faq\FaqResource;
use App\MoonShine\Resources\Vacancy\VacancyResource;
use App\MoonShine\Resources\UiText\UiTextResource;
use App\MoonShine\Resources\Lead\LeadResource;

class MoonShineServiceProvider extends ServiceProvider
{
    /**
     * @param  CoreContract<MoonShineConfigurator>  $core
     */
    public function boot(CoreContract $core): void
    {
        $core
            ->resources([
                MoonShineUserResource::class,
                MoonShineUserRoleResource::class,
                SiteSettingResource::class,
                MenuItemResource::class,
                PageResource::class,
                PageBlockResource::class,
                ProjectResource::class,
                ServiceResource::class,
                NewsArticleResource::class,
                PromoResource::class,
                AwardResource::class,
                PartnerResource::class,
                ReviewResource::class,
                FaqResource::class,
                VacancyResource::class,
                UiTextResource::class,
                LeadResource::class,
            ])
            ->pages([
                ...$core->getConfig()->getPages(),
                ImageGalleryPage::class,
                PageContentBuilderPage::class,
            ])
        ;
    }
}
