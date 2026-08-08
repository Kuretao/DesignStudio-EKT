<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\AssetManager\InlineCss;
use MoonShine\AssetManager\InlineJs;
use MoonShine\ColorManager\Palettes\PurplePalette;
use MoonShine\UI\Components\Layout\Footer;
use MoonShine\Contracts\AssetManager\AssetElementContract;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\ColorManager\PaletteContract;
use App\MoonShine\Pages\ImageGalleryPage;
use App\MoonShine\Pages\PageContentBuilderPage;
use App\MoonShine\Pages\ServiceDirectionsPage;
use App\MoonShine\Pages\StyleLabEditorPage;
use App\MoonShine\Resources\SiteSetting\SiteSettingResource;
use App\MoonShine\Resources\SiteSetting\Pages\SocialLinksFormPage;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem;
use MoonShine\Support\UriKey;
use App\MoonShine\Resources\MenuItem\MenuItemResource;
use App\MoonShine\Resources\Page\PageResource;
use App\MoonShine\Resources\Project\ProjectResource;
use App\MoonShine\Resources\Service\ServiceResource;
use App\MoonShine\Resources\NewsArticle\NewsArticleResource;
use App\MoonShine\Resources\Promo\PromoResource;
use App\MoonShine\Resources\Award\AwardResource;
use App\MoonShine\Resources\Partner\PartnerResource;
use App\MoonShine\Resources\Review\ReviewResource;
use App\MoonShine\Resources\Faq\FaqResource;
use App\MoonShine\Resources\Vacancy\VacancyResource;
use App\MoonShine\Resources\Lead\LeadResource;
use App\Models\SiteSetting;

final class MoonShineLayout extends AppLayout
{
    /**
     * @var null|class-string<PaletteContract>
     */
    protected ?string $palette = PurplePalette::class;

    /**
     * @return list<AssetElementContract>
     */
    protected function assets(): array
    {
        $galleryEndpoint = '/' . trim((string) config('moonshine.prefix', 'admin'), '/') . '/image-gallery';

        return [
            ...parent::assets(),
            InlineCss::make((string) file_get_contents(resource_path('css/image-gallery-admin.css'))),
            InlineJs::make('window.AdminImageGallery = ' . json_encode([
                'endpoint' => $galleryEndpoint,
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ';'),
            InlineJs::make((string) file_get_contents(resource_path('js/admin-image-gallery.js'))),
        ];
    }

    protected function menu(): array
    {
        return [
            ...parent::menu(),
            MenuGroup::make('Сайт', [
                MenuItem::make(SiteSettingResource::class, 'Настройки')->icon('cog-6-tooth'),
                MenuItem::make(ImageGalleryPage::class, 'Галерея')->icon('photo'),
                MenuItem::make(PageContentBuilderPage::class, 'Конструктор страниц')->icon('rectangle-stack'),
                MenuItem::make(fn (): string => url('/' . trim((string) config('moonshine.prefix', 'admin'), '/') . '/style-lab-editor'), 'Редактор Style Lab')->icon('swatch'),
                MenuItem::make(MenuItemResource::class, 'Меню сайта')->icon('bars-3'),
                MenuItem::make(ServiceDirectionsPage::class, 'Направления услуг')->icon('squares-2x2'),
                MenuItem::make(PageResource::class, 'Страницы')->icon('document-text'),
            ], 'globe-alt'),
            MenuGroup::make('Контент', [
                MenuItem::make(ProjectResource::class, 'Проекты')->icon('photo'),
                MenuItem::make(ServiceResource::class, 'Услуги')->icon('wrench-screwdriver'),
                MenuItem::make(NewsArticleResource::class, 'Новости')->icon('newspaper'),
                MenuItem::make(PromoResource::class, 'Акции')->icon('tag'),
                MenuItem::make(AwardResource::class, 'Награды и дипломы')->icon('trophy'),
                MenuItem::make(PartnerResource::class, 'Партнеры и застройщики')->icon('building-office-2'),
            ], 'document-text'),
            MenuGroup::make('Обратная связь', [
                MenuItem::make(LeadResource::class, 'Заявки')->icon('inbox'),
                MenuItem::make(fn (): string => $this->siteSettingPageUrl(SocialLinksFormPage::class), 'Меседжеры и сотсети')->icon('share'),
                MenuItem::make(ReviewResource::class, 'Отзывы')->icon('star'),
                MenuItem::make(FaqResource::class, 'FAQ')->icon('question-mark-circle'),
                MenuItem::make(VacancyResource::class, 'Вакансии')->icon('briefcase'),
            ], 'chat-bubble-left-right'),
        ];
    }

    protected function getFooterCopyright(): string
    {
        return '&copy; 2026 Сделано с ❤️ для <a href="#" class="font-semibold text-primary">3dsmartdesign.ru</a>';
    }

    protected function getFooterMenu(): array
    {
        return [];
    }

    /**
     * @param class-string $pageClass
     */
    private function siteSettingPageUrl(string $pageClass): string
    {
        $setting = SiteSetting::query()->firstOrCreate([], [
            'site_name' => '3D Smart Design Studio',
        ]);

        return route('moonshine.resource.page', [
            'resourceUri' => (new UriKey(SiteSettingResource::class))->generate(),
            'pageUri' => (new UriKey($pageClass))->generate(),
            'resourceItem' => $setting->getKey(),
        ]);
    }

    /**
     * @param ColorManager $colorManager
     */
    protected function colors(ColorManagerContract $colorManager): void
    {
        parent::colors($colorManager);

        // $colorManager->primary('#00000');
    }
}
