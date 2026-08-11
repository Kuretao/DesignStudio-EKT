<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\Models\Page as ContentPage;
use App\Models\PageBlock;
use App\Models\UiText;
use App\MoonShine\Resources\Award\AwardResource;
use App\MoonShine\Resources\Award\Pages\AwardIndexPage;
use App\MoonShine\Resources\Faq\FaqResource;
use App\MoonShine\Resources\Faq\Pages\FaqIndexPage;
use App\MoonShine\Resources\Lead\LeadResource;
use App\MoonShine\Resources\Lead\Pages\LeadIndexPage;
use App\MoonShine\Resources\NewsArticle\NewsArticleResource;
use App\MoonShine\Resources\NewsArticle\Pages\NewsArticleIndexPage;
use App\MoonShine\Resources\Page\PageResource;
use App\MoonShine\Resources\Page\Pages\PageFormPage;
use App\MoonShine\Resources\Page\Pages\PageIndexPage;
use App\MoonShine\Resources\PageBlock\PageBlockResource;
use App\MoonShine\Resources\PageBlock\Pages\PageBlockFormPage;
use App\MoonShine\Resources\PageBlock\Pages\PageBlockIndexPage;
use App\MoonShine\Resources\Partner\PartnerResource;
use App\MoonShine\Resources\Partner\Pages\PartnerIndexPage;
use App\MoonShine\Resources\Promo\PromoResource;
use App\MoonShine\Resources\Promo\Pages\PromoIndexPage;
use App\MoonShine\Resources\Project\Pages\ProjectIndexPage;
use App\MoonShine\Resources\Project\ProjectResource;
use App\MoonShine\Resources\Review\ReviewResource;
use App\MoonShine\Resources\Review\Pages\ReviewIndexPage;
use App\MoonShine\Resources\Service\Pages\ServiceIndexPage;
use App\MoonShine\Resources\Service\ServiceResource;
use App\MoonShine\Resources\SiteSetting\Pages\SiteSettingIndexPage;
use App\MoonShine\Resources\SiteSetting\SiteSettingResource;
use App\MoonShine\Resources\UiText\Pages\UiTextFormPage;
use App\MoonShine\Resources\UiText\UiTextResource;
use App\MoonShine\Resources\Vacancy\VacancyResource;
use App\MoonShine\Resources\Vacancy\Pages\VacancyIndexPage;
use MoonShine\AssetManager\InlineCss;
use MoonShine\Contracts\AssetManager\AssetElementContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Support\UriKey;
use MoonShine\UI\Components\FlexibleRender;

#[\MoonShine\MenuManager\Attributes\SkipMenu]
class PageContentBuilderPage extends Page
{
    public function getBreadcrumbs(): array
    {
        return ['#' => $this->getTitle()];
    }

    public function getTitle(): string
    {
        return $this->title ?: 'Конструктор страниц';
    }

    /**
     * @return list<AssetElementContract>
     */
    protected function assets(): array
    {
        return [
            ...parent::assets(),
            InlineCss::make((string) file_get_contents(resource_path('css/page-builder-admin.css'))),
        ];
    }

    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
    {
        return [
            FlexibleRender::make($this->html()),
        ];
    }

    private function html(): string
    {
        $pages = ContentPage::query()
            ->withCount([
                'blocks',
                'blocks as active_blocks_count' => fn ($query) => $query->where('is_active', true),
            ])
            ->with(['blocks' => fn ($query) => $query->orderBy('position')])
            ->orderByRaw("case when slug = 'home' then 0 else 1 end")
            ->orderBy('title')
            ->get();

        $sitePages = collect($this->sitePageCatalog());
        $selectedSlug = (string) request()->query('page', 'home');
        $selectedSitePage = $sitePages->firstWhere('slug', $selectedSlug) ?? $sitePages->first();
        $selectedPage = $pages->firstWhere('slug', $selectedSitePage['slug'] ?? null);
        $section = request()->query('section') === 'permanent' ? 'permanent' : 'pages';

        $pageCards = $sitePages
            ->map(fn (array $page): string => $this->sitePageCard($page, ($selectedSitePage['slug'] ?? '') === $page['slug']))
            ->implode('');
        $builderBody = $section === 'permanent'
            ? $this->permanentBlocksHtml((string) request()->query('block', ''))
            : ($selectedSitePage ? $this->selectedSitePageHtml($selectedSitePage, $selectedPage) : $this->emptyState());
        $pageSectionClass = $section === 'pages' ? ' page-builder-switch__item--active' : '';
        $permanentSectionClass = $section === 'permanent' ? ' page-builder-switch__item--active' : '';
        $pagesUrl = e($this->currentBuilderUrl());
        $permanentUrl = e($this->currentBuilderUrl() . '?section=permanent');
        $sidebarTitle = $section === 'permanent' ? 'Постоянные блоки' : 'Страницы сайта';
        $sidebarText = $section === 'permanent'
            ? 'Откройте общую секцию, которая повторяется на сайте.'
            : 'Откройте страницу, затем нужный блок.';
        $sidebarList = $section === 'permanent'
            ? $this->permanentSidebarHtml()
            : '<div class="page-builder__page-list">' . $pageCards . '</div>';
        $totalBlocks = (int) $sitePages->sum(static fn (array $page): int => count($page['sections'] ?? []));
        $activeBlocks = $totalBlocks;

        return <<<HTML
        <section class="page-builder">
            <div class="page-builder__hero">
                <div>
                    <p class="page-builder__eyebrow">Страницы и блоки</p>
                    <h1>Конструктор страниц</h1>
                    <p>Выберите страницу слева и редактируйте только ее блоки: тексты, кнопки, картинки, слайдеры, motion и состояние карточек. Больше не нужно искать нужный блок в общей таблице.</p>
                </div>
                <div class="page-builder__stats">
                    <div><strong>{$sitePages->count()}</strong><span>страниц</span></div>
                    <div><strong>{$totalBlocks}</strong><span>блоков</span></div>
                    <div><strong>{$activeBlocks}</strong><span>показываются</span></div>
                </div>
            </div>

            <div class="page-builder__layout">
                <aside class="page-builder__sidebar">
                    <div class="page-builder-switch">
                        <a class="page-builder-switch__item{$pageSectionClass}" href="{$pagesUrl}">
                            <strong>Страницы</strong>
                            <span>Все реальные разделы сайта</span>
                        </a>
                        <a class="page-builder-switch__item{$permanentSectionClass}" href="{$permanentUrl}">
                            <strong>Постоянные блоки</strong>
                            <span>Общие секции, которые повторяются на сайте</span>
                        </a>
                    </div>
                    <div class="page-builder__sidebar-head">
                        <strong>{$sidebarTitle}</strong>
                        <span>{$sidebarText}</span>
                    </div>
                    {$sidebarList}
                </aside>

                <main class="page-builder__workspace">
                    {$builderBody}
                </main>
            </div>
        </section>
        HTML;
    }

    private function sitePageCatalog(): array
    {
        $projectsUrl = $this->moonshineResourcePageUrl(ProjectResource::class, ProjectIndexPage::class);
        $servicesUrl = $this->moonshineResourcePageUrl(ServiceResource::class, ServiceIndexPage::class);
        $directionsUrl = $this->moonshinePageUrl(ServiceDirectionsPage::class);
        $newsUrl = $this->moonshineResourcePageUrl(NewsArticleResource::class, NewsArticleIndexPage::class);
        $promosUrl = $this->moonshineResourcePageUrl(PromoResource::class, PromoIndexPage::class);
        $awardsUrl = $this->moonshineResourcePageUrl(AwardResource::class, AwardIndexPage::class);
        $partnersUrl = $this->moonshineResourcePageUrl(PartnerResource::class, PartnerIndexPage::class);
        $faqUrl = $this->moonshineResourcePageUrl(FaqResource::class, FaqIndexPage::class);
        $vacanciesUrl = $this->moonshineResourcePageUrl(VacancyResource::class, VacancyIndexPage::class);
        $leadsUrl = $this->moonshineResourcePageUrl(LeadResource::class, LeadIndexPage::class);
        $settingsUrl = $this->moonshineResourcePageUrl(SiteSettingResource::class, SiteSettingIndexPage::class);

        return [
            [
                'slug' => 'home',
                'title' => 'Главная',
                'path' => '/',
                'description' => 'Первый экран, избранный проект и все основные витринные секции главной.',
                'sections' => [
                    ['id' => 'featured', 'title' => 'Избранный проект', 'kind' => 'Проекты', 'group' => 'portfolio-home', 'prefixes' => ['portfolio.selectedProject', 'portfolio.task', 'portfolio.result', 'portfolio.format'], 'description' => 'Выбор проекта, подписи, задача, результат и формат.', 'links' => [['label' => 'Проекты и выбранный проект', 'url' => $projectsUrl]]],
                    ['id' => 'services', 'title' => 'Услуги и цены', 'kind' => 'Услуги', 'group' => 'services-home', 'prefixes' => ['servicesSummary.'], 'description' => 'Заголовки и карточки услуг на главной.', 'links' => [['label' => 'Карточки услуг', 'url' => $servicesUrl]]],
                    ['id' => 'style-lab', 'title' => 'Подбор стиля', 'kind' => 'Style Lab', 'description' => 'Интерактивный блок, варианты и изображения для каждой комбинации.', 'links' => [['label' => 'Открыть редактор Style Lab', 'url' => $this->moonshinePageUrl(StyleLabEditorPage::class)]]],
                    ['id' => 'about', 'title' => 'О студии', 'kind' => 'Тексты и цифры', 'group' => 'about-home', 'description' => 'Заголовок, описание, факты, принципы, кнопки и подпись изображения.'],
                    ['id' => 'awards', 'title' => 'Награды и дипломы', 'kind' => 'Награды', 'group' => 'awards-home', 'description' => 'Заголовок секции и карточки наград.', 'links' => [['label' => 'Карточки наград', 'url' => $awardsUrl]]],
                    ['id' => 'partners', 'title' => 'Нам доверяют', 'kind' => 'Партнеры', 'group' => 'partners-home', 'description' => 'Заголовок блока и логотипы партнеров.', 'links' => [['label' => 'Карточки партнеров', 'url' => $partnersUrl]]],
                    ['id' => 'faq', 'title' => 'FAQ', 'kind' => 'Вопросы', 'group' => 'faq-home', 'description' => 'Заголовок секции и вопросы с ответами.', 'links' => [['label' => 'Вопросы FAQ', 'url' => $faqUrl]]],
                    ['id' => 'contact', 'title' => 'Контакты и форма', 'kind' => 'Контакты', 'group' => 'contact-home', 'description' => 'Контактные данные, карта, поля и кнопки формы.', 'links' => [['label' => 'Телефон, почта и адрес', 'url' => $settingsUrl], ['label' => 'Заявки', 'url' => $leadsUrl]]],
                ],
            ],
            [
                'slug' => 'o-nas',
                'title' => 'О нас',
                'path' => '/o-nas',
                'description' => 'Реальная страница AboutPageFull. Старая техническая запись hero здесь больше не показывается.',
                'sections' => [
                    ['id' => 'hero', 'title' => 'Первый экран, цифры и направления', 'kind' => 'Hero', 'group' => 'about-full', 'prefixes' => ['aboutFull.hero.', 'aboutFull.stats.', 'aboutFull.directions.'], 'description' => 'Заголовок, текст, кнопки, подписи, статистика и список направлений.'],
                    ['id' => 'principles', 'title' => 'Принципы', 'kind' => 'Карточки', 'group' => 'about-full', 'prefixes' => ['aboutFull.principles.'], 'description' => 'Заголовок секции и четыре карточки принципов.'],
                    ['id' => 'team', 'title' => 'Команда и направления работы', 'kind' => 'Медиа и список', 'group' => 'about-full', 'prefixes' => ['aboutFull.work.', 'aboutFull.team.'], 'description' => 'Подписи изображений, описание команды и список компетенций.', 'links' => [['label' => 'Изображения берутся из проектов', 'url' => $projectsUrl]]],
                    ['id' => 'process', 'title' => 'Как мы работаем', 'kind' => 'Этапы', 'group' => 'about-full', 'prefixes' => ['aboutFull.process.'], 'description' => 'Заголовок, описание и шесть этапов процесса.'],
                    ['id' => 'cta', 'title' => 'Финальный призыв', 'kind' => 'CTA', 'group' => 'about-full', 'prefixes' => ['aboutFull.cta.'], 'description' => 'Надзаголовок, основной текст и кнопка связи.'],
                    ['id' => 'awards', 'title' => 'Награды', 'kind' => 'Общий блок', 'group' => 'awards-home', 'description' => 'Заголовок и карточки наград, используемые на сайте.', 'links' => [['label' => 'Карточки наград', 'url' => $awardsUrl]]],
                    ['id' => 'partners', 'title' => 'Партнеры', 'kind' => 'Общий блок', 'group' => 'partners-home', 'description' => 'Подписи и логотипы партнеров.', 'links' => [['label' => 'Карточки партнеров', 'url' => $partnersUrl]]],
                ],
            ],
            [
                'slug' => 'portfolio',
                'title' => 'Портфолио',
                'path' => '/portfolio',
                'description' => 'Hero, фильтры, сетка проектов, выбранный проект и общая обвязка кейсов.',
                'sections' => [
                    ['id' => 'portfolio', 'title' => 'Hero, фильтры и сетка', 'kind' => 'Портфолио', 'group' => 'portfolio-home', 'description' => 'Все подписи страницы, фильтры, кнопки и выбранный проект.', 'links' => [['label' => 'Проекты, изображения и фильтры', 'url' => $projectsUrl]]],
                    ['id' => 'case', 'title' => 'Детальная страница проекта', 'kind' => 'Шаблон кейса', 'group' => 'portfolio-case', 'description' => 'Общие подписи галереи, до/после, процесса и похожих проектов.', 'links' => [['label' => 'Настройки каждого проекта', 'url' => $projectsUrl]]],
                ],
            ],
            [
                'slug' => 'services',
                'title' => 'Услуги',
                'path' => '/services',
                'description' => 'Первый экран, три выбранных направления, цены, каталог направлений и этапы работы.',
                'sections' => [
                    ['id' => 'hero', 'title' => 'Первый экран страницы услуг', 'kind' => 'Hero', 'group' => 'services-page', 'description' => 'Надзаголовок, заголовок, описание, кнопки и три показателя.', 'links' => [['label' => 'Выбрать три карточки направлений', 'url' => $directionsUrl]]],
                    ['id' => 'summary', 'title' => 'Услуги и цены', 'kind' => 'Карточки услуг', 'group' => 'services-home', 'prefixes' => ['servicesSummary.'], 'description' => 'Заголовок блока и услуги, выбранные для главной.', 'links' => [['label' => 'Карточки и цены услуг', 'url' => $servicesUrl]]],
                    ['id' => 'directions', 'title' => 'Направления услуг', 'kind' => 'Каталог направлений', 'group' => 'services-home', 'prefixes' => ['servicePages.'], 'description' => 'Подписи секции, карточки направлений и входящие услуги.', 'links' => [['label' => 'Направления, изображения и состав', 'url' => $directionsUrl]]],
                    ['id' => 'workflow', 'title' => 'Этапы работы', 'kind' => 'Этапы', 'group' => 'services-home', 'prefixes' => ['workflow.'], 'description' => 'Заголовок и все этапы процесса.'],
                    ['id' => 'service-detail', 'title' => 'Детальная страница услуги', 'kind' => 'Шаблон услуги', 'group' => 'service-detail', 'description' => 'Общие подписи hero, до/после, документации, преимуществ и процесса.', 'links' => [['label' => 'Настройки каждой услуги', 'url' => $servicesUrl]]],
                ],
            ],
            [
                'slug' => 'akcii-i-skidki',
                'title' => 'Акции',
                'path' => '/akcii-i-skidki',
                'description' => 'Первый экран, карточки акций и нижний CTA.',
                'sections' => [
                    ['id' => 'promos', 'title' => 'Страница акций', 'kind' => 'Hero, список и CTA', 'group' => 'promos-page', 'description' => 'Все тексты страницы, сроки, кнопки и пустое состояние.', 'links' => [['label' => 'Карточки акций и изображения', 'url' => $promosUrl]]],
                ],
            ],
            [
                'slug' => 'novosti',
                'title' => 'Блог и новости',
                'path' => '/novosti',
                'description' => 'Hero журнала, список материалов и общая обвязка детальной статьи.',
                'sections' => [
                    ['id' => 'news', 'title' => 'Список новостей', 'kind' => 'Hero и каталог', 'group' => 'news-page', 'prefixes' => ['news.hero.', 'news.list.', 'news.read'], 'description' => 'Заголовки, кнопки, время чтения и подписи списка.', 'links' => [['label' => 'Новости, тексты и изображения', 'url' => $newsUrl]]],
                    ['id' => 'article', 'title' => 'Детальная статья', 'kind' => 'Шаблон новости', 'group' => 'news-page', 'prefixes' => ['newsArticle.'], 'description' => 'Автор, CTA, навигация назад и блок «Читать также».', 'links' => [['label' => 'Все статьи', 'url' => $newsUrl]]],
                ],
            ],
            [
                'slug' => 'kontakty',
                'title' => 'Контакты',
                'path' => '/kontakty',
                'description' => 'Контактные данные, форма, карта и служебные подписи.',
                'sections' => [
                    ['id' => 'contacts', 'title' => 'Контакты и форма', 'kind' => 'Контакты', 'group' => 'contact-home', 'description' => 'Заголовки, поля формы, карта, статусы и кнопки.', 'links' => [['label' => 'Телефон, почта, адрес и соцсети', 'url' => $settingsUrl], ['label' => 'Полученные заявки', 'url' => $leadsUrl]]],
                ],
            ],
            [
                'slug' => 'karera',
                'title' => 'Карьера',
                'path' => '/karera',
                'description' => 'Hero, направления, вакансии, CTA и форма отклика.',
                'sections' => [
                    ['id' => 'career', 'title' => 'Страница карьеры', 'kind' => 'Hero, вакансии и форма', 'group' => 'career-page', 'description' => 'Все тексты, показатели, кнопки, подсказки и сообщения формы.', 'links' => [['label' => 'Карточки вакансий', 'url' => $vacanciesUrl], ['label' => 'Отклики кандидатов', 'url' => $leadsUrl]]],
                ],
            ],
            [
                'slug' => 'partneram',
                'title' => 'Партнерам',
                'path' => '/partneram',
                'description' => 'Уникальные блоки страницы, партнерские логотипы и заявки на сотрудничество.',
                'sections' => [
                    ['id' => 'partners', 'title' => 'Партнеры и доверие', 'kind' => 'Карточки партнеров', 'group' => 'partners-home', 'description' => 'Заголовки общего блока и карточки партнеров.', 'links' => [['label' => 'Партнеры и логотипы', 'url' => $partnersUrl]]],
                    ['id' => 'leads', 'title' => 'Заявки партнеров', 'kind' => 'Форма', 'description' => 'Все отправленные заявки со страницы партнеров.', 'links' => [['label' => 'Открыть заявки', 'url' => $leadsUrl]]],
                ],
            ],
        ];
    }

    private function sitePageCard(array $page, bool $active): string
    {
        $url = e($this->pageBuilderPageUrl((string) $page['slug']));
        $title = e((string) $page['title']);
        $path = e((string) $page['path']);
        $sections = count($page['sections'] ?? []);
        $activeClass = $active ? ' page-builder-page--active' : '';

        return <<<HTML
        <a class="page-builder-page{$activeClass}" href="{$url}">
            <span class="page-builder-page__title">{$title}</span>
            <code>{$path}</code>
            <span class="page-builder-page__meta">{$sections} секций · используются на сайте</span>
        </a>
        HTML;
    }

    private function selectedSitePageHtml(array $definition, ?ContentPage $contentPage): string
    {
        $selectedGroup = (string) request()->query('edit', '');

        if ($selectedGroup !== '') {
            return $this->sitePageGroupEditorHtml($definition, $selectedGroup);
        }

        $title = e((string) $definition['title']);
        $path = e((string) $definition['path']);
        $description = e((string) ($definition['description'] ?? 'Все реальные секции страницы собраны ниже.'));
        $settingsButton = $contentPage
            ? '<a href="' . e($this->moonshineResourcePageUrl(PageResource::class, PageFormPage::class, $contentPage->getKey())) . '">SEO и адрес страницы</a>'
            : '';
        $sections = collect($definition['sections'] ?? [])
            ->map(fn (array $section): string => $this->sitePageSectionCard($definition, $section))
            ->implode('');
        $pageBlocks = $this->sitePageBlocksHtml($definition, $contentPage);

        return <<<HTML
        <div class="page-builder-selected">
            <div class="page-builder-selected__head">
                <div>
                    <p class="page-builder__eyebrow">Редактируется реальная страница</p>
                    <h2>{$title}</h2>
                    <p><code>{$path}</code></p>
                    <p>{$description}</p>
                </div>
                <div class="page-builder-selected__actions">{$settingsButton}</div>
            </div>

            <div class="page-builder-home-map__grid">{$sections}</div>
            {$pageBlocks}
        </div>
        HTML;
    }

    private function sitePageSectionCard(array $page, array $section): string
    {
        $title = e((string) $section['title']);
        $kind = e((string) ($section['kind'] ?? 'Блок страницы'));
        $description = e((string) ($section['description'] ?? ''));
        $group = (string) ($section['group'] ?? '');
        $fieldsCount = $group !== '' ? $this->uiTextQueryForSection($section)->count() : null;
        $meta = $fieldsCount !== null ? $fieldsCount . ' редактируемых полей' : 'Отдельный редактор';
        $links = collect($section['links'] ?? []);

        if ($group !== '') {
            $links->prepend([
                'label' => 'Редактировать поля блока',
                'url' => $this->pageGroupUrl((string) $page['slug'], $group, (string) ($section['id'] ?? '')),
            ]);
        }

        $linksHtml = $links
            ->map(static fn (array $link): string => '<a href="' . e((string) $link['url']) . '">' . e((string) $link['label']) . '</a>')
            ->implode('');

        return <<<HTML
        <article class="page-builder-section-card">
            <span>{$kind}</span>
            <h4>{$title}</h4>
            <code>{$meta}</code>
            <p>{$description}</p>
            <div>{$linksHtml}</div>
        </article>
        HTML;
    }

    private function sitePageGroupEditorHtml(array $page, string $group): string
    {
        $part = (string) request()->query('part', '');
        $section = collect($page['sections'] ?? [])->first(function (array $section) use ($group, $part): bool {
            if (($section['group'] ?? '') !== $group) {
                return false;
            }

            return $part === '' || ($section['id'] ?? '') === $part;
        });

        if (! $section) {
            return $this->emptyState();
        }

        $rows = $this->uiTextQueryForSection($section)->orderBy('position')->orderBy('label')->get();
        $items = $rows->isNotEmpty()
            ? $rows->map(fn (UiText $text): string => $this->uiTextFieldCard($text))->implode('')
            : $this->missingUiTextGroupHtml($group);
        $title = e((string) $section['title']);
        $pageTitle = e((string) $page['title']);
        $backUrl = e($this->pageBuilderPageUrl((string) $page['slug']));

        return <<<HTML
        <section class="page-builder-permanent-editor">
            <div class="page-builder-home-map__head">
                <p class="page-builder__eyebrow">{$pageTitle} / блок страницы</p>
                <h3>{$title}</h3>
                <span>Ниже только поля выбранной секции, в том же порядке, в котором с ними удобно работать.</span>
                <div class="page-builder-permanent-editor__actions"><a href="{$backUrl}">Назад ко всем блокам страницы</a></div>
            </div>
            <div class="page-builder-permanent-editor__head"><h4>{$title}</h4><span>{$rows->count()} полей</span></div>
            <div class="page-builder-ui-fields">{$items}</div>
        </section>
        HTML;
    }

    private function uiTextQueryForSection(array $section)
    {
        $query = UiText::query()->where('group', (string) $section['group']);
        $prefixes = collect($section['prefixes'] ?? [])
            ->filter(static fn (mixed $prefix): bool => is_string($prefix) && $prefix !== '')
            ->values();

        if ($prefixes->isNotEmpty()) {
            $query->where(function ($query) use ($prefixes): void {
                $prefixes->each(function (string $prefix, int $index) use ($query): void {
                    $method = $index === 0 ? 'where' : 'orWhere';
                    $query->{$method}('key', 'like', $prefix . '%');
                });
            });
        }

        return $query;
    }

    private function sitePageBlocksHtml(array $definition, ?ContentPage $contentPage): string
    {
        if (! $contentPage || ! in_array($definition['slug'], ['home', 'partneram'], true)) {
            return '';
        }

        $blocks = $contentPage->blocks
            ->filter(static fn (PageBlock $block): bool => $block->is_active || filled($block->fieldRu('title')) || filled($block->fieldRu('text')))
            ->map(fn (PageBlock $block): string => $this->blockCard($block))
            ->implode('');

        if ($blocks === '') {
            return '';
        }

        return <<<HTML
        <section class="page-builder-connected-blocks">
            <div class="page-builder-home-map__head">
                <p class="page-builder__eyebrow">Уникальные блоки этой страницы</p>
                <h3>Блоки, которые действительно читаются из PageBlock</h3>
                <span>Они показаны отдельно от общих секций, чтобы технические записи других страниц не путались с реальной версткой.</span>
            </div>
            <div class="page-builder-blocks">{$blocks}</div>
        </section>
        HTML;
    }

    private function pageCard(ContentPage $page, bool $active): string
    {
        $url = e($this->currentBuilderUrl() . '?page=' . urlencode((string) $page->slug));
        $title = e($page->fieldRu('title') ?: $page->title ?: 'Без названия');
        $path = $page->slug === 'home' ? 'Главная' : '/' . e(ltrim((string) $page->slug, '/'));
        $blocks = (int) ($page->blocks_count ?? 0);
        $activeBlocks = (int) ($page->active_blocks_count ?? 0);
        $activeClass = $active ? ' page-builder-page--active' : '';
        $status = $page->is_published ? 'Опубликована' : 'Черновик';

        return <<<HTML
        <a class="page-builder-page{$activeClass}" href="{$url}">
            <span class="page-builder-page__title">{$title}</span>
            <code>{$path}</code>
            <span class="page-builder-page__meta">{$activeBlocks} / {$blocks} блоков · {$status}</span>
        </a>
        HTML;
    }

    private function selectedPageHtml(ContentPage $page): string
    {
        $title = e($page->fieldRu('title') ?: $page->title ?: 'Без названия');
        $path = $page->slug === 'home' ? 'Главная страница' : '/' . e(ltrim((string) $page->slug, '/'));
        $pageEditUrl = e($this->moonshineResourcePageUrl(PageResource::class, PageFormPage::class, $page->getKey()));
        $isLegal = $page->template === 'legal'
            || in_array($page->slug, ['politika-konfidencialnosti', 'user/agreement'], true);
        $blocks = $isLegal
            ? $this->legalPageEditorHtml($page, $pageEditUrl)
            : ($page->blocks->isNotEmpty()
                ? $page->blocks->map(fn (PageBlock $block): string => $this->blockCard($block))->implode('')
                : $this->noBlocksHtml());
        $actionLabel = $isLegal ? 'Редактировать текст документа' : 'Настройки страницы';

        return <<<HTML
        <div class="page-builder-selected">
            <div class="page-builder-selected__head">
                <div>
                    <p class="page-builder__eyebrow">Редактируется страница</p>
                    <h2>{$title}</h2>
                    <p><code>{$path}</code></p>
                </div>
                <div class="page-builder-selected__actions">
                    <a class="page-builder-selected__primary" href="{$pageEditUrl}">{$actionLabel}</a>
                </div>
            </div>

            <div class="page-builder-blocks">
                {$blocks}
            </div>

        </div>
        HTML;
    }

    private function legalPageEditorHtml(ContentPage $page, string $editUrl): string
    {
        $preview = e(str((string) ($page->fieldRu('body') ?? ''))->stripTags()->squish()->limit(240)->toString());
        $preview = $preview !== ''
            ? $preview
            : 'Текст документа пока не заполнен. Откройте редактор и добавьте содержание страницы.';

        return <<<HTML
        <div class="page-builder-empty">
            <strong>Юридический документ редактируется целиком</strong>
            <span>{$preview}</span>
            <a href="{$editUrl}">Открыть редактор текста</a>
        </div>
        HTML;
    }

    private function permanentBlocksHtml(string $selectedGroup = ''): string
    {
        if ($selectedGroup !== '') {
            return $this->permanentBlockEditorHtml($selectedGroup);
        }

        $cards = collect($this->permanentBlockCards())
            ->map(fn (array $section): string => $this->permanentBlockCard($section))
            ->implode('');

        return <<<HTML
        <section class="page-builder-home-map">
            <div class="page-builder-home-map__head">
                <p class="page-builder__eyebrow">Постоянные блоки</p>
                <h3>Общие секции сайта в одном месте</h3>
                <span>Здесь собраны блоки, которые повторяются на главной, услугах, портфолио и внутренних страницах: заголовки, подписи, кнопки, списки, логотипы, карточки и контакты.</span>
            </div>
            <div class="page-builder-home-map__grid">{$cards}</div>
        </section>
        HTML;
    }

    private function permanentSidebarHtml(): string
    {
        $selectedGroup = (string) request()->query('block', '');

        return '<div class="page-builder__page-list">' . collect($this->permanentBlockCards())
            ->map(static function (array $section): string {
                $title = e($section['title']);
                $rawBadge = (string) ($section['badge'] ?? 'Постоянный блок');
                $badge = e($rawBadge);
                $anchor = 'permanent-' . md5($rawBadge);

                $group = (string) ($section['primary_group'] ?? '');
                $url = e($this->currentBuilderUrl() . '?section=permanent' . ($group !== '' ? '&block=' . urlencode($group) : ''));
                $activeClass = $group !== '' && $group === (string) request()->query('block', '') ? ' page-builder-page--active' : '';

                return <<<HTML
                <a class="page-builder-page{$activeClass}" href="{$url}">
                    <span class="page-builder-page__title">{$title}</span>
                    <code>{$badge}</code>
                    <span class="page-builder-page__meta">Общая секция сайта</span>
                </a>
                HTML;
            })
            ->implode('') . '</div>';
    }

    private function permanentBlockCard(array $section): string
    {
        $title = e($section['title']);
        $description = e($section['description']);
        $kind = e($section['kind']);
        $rawBadge = (string) ($section['badge'] ?? 'Постоянный блок');
        $badge = e($rawBadge);
        $anchor = 'permanent-' . md5($rawBadge);
        $links = collect($section['links'])
            ->map(fn (array $link): string => sprintf(
                '<a href="%s">%s</a>',
                e($link['url']),
                e($link['label']),
            ))
            ->implode('');

        return <<<HTML
        <article class="page-builder-section-card" id="{$anchor}">
            <span>{$kind}</span>
            <h4>{$title}</h4>
            <code>{$badge}</code>
            <p>{$description}</p>
            <div>{$links}</div>
        </article>
        HTML;
    }

    private function permanentBlockEditorHtml(string $group): string
    {
        $section = collect($this->permanentBlockCards())->first(function (array $section) use ($group): bool {
            return collect($section['groups'] ?? [])->contains(static fn (array $item): bool => ($item['group'] ?? '') === $group);
        });

        $groupMeta = collect($section['groups'] ?? [])->first(static fn (array $item): bool => ($item['group'] ?? '') === $group) ?? null;
        $title = e($groupMeta['label'] ?? $section['title'] ?? 'Постоянный блок');
        $blockTitle = e($section['title'] ?? 'Постоянный блок');
        $backUrl = e($this->currentBuilderUrl() . '?section=permanent');
        $rows = UiText::query()
            ->where('group', $group)
            ->orderBy('position')
            ->orderBy('label')
            ->get();

        $items = $rows->isNotEmpty()
            ? $rows->map(fn (UiText $text): string => $this->uiTextFieldCard($text))->implode('')
            : $this->missingUiTextGroupHtml($group);

        $groupTabs = collect($section['groups'] ?? [])
            ->map(function (array $item) use ($group): string {
                $itemGroup = (string) ($item['group'] ?? '');
                $label = e($item['label'] ?? $itemGroup);
                $url = e($this->currentBuilderUrl() . '?section=permanent&block=' . urlencode($itemGroup));
                $active = $itemGroup === $group ? ' page-builder-group-chip--active' : '';

                return '<a class="page-builder-group-chip' . $active . '" href="' . $url . '">' . $label . '</a>';
            })
            ->implode('');

        return <<<HTML
        <section class="page-builder-permanent-editor">
            <div class="page-builder-home-map__head">
                <p class="page-builder__eyebrow">Постоянный блок</p>
                <h3>{$blockTitle}</h3>
                <span>Редактируйте конкретные поля блока ниже. Это не общая таблица: здесь только строки выбранной секции.</span>
                <div class="page-builder-permanent-editor__actions">
                    <a href="{$backUrl}">Все постоянные блоки</a>
                </div>
            </div>

            <div class="page-builder-group-chips">{$groupTabs}</div>

            <div class="page-builder-permanent-editor__head">
                <h4>{$title}</h4>
                <span>{$rows->count()} полей</span>
            </div>

            <div class="page-builder-ui-fields">
                {$items}
            </div>
        </section>
        HTML;
    }

    private function uiTextFieldCard(UiText $text): string
    {
        $label = e($text->label ?: $text->key);
        $key = e($text->key);
        $value = e(str((string) ($text->value_ru ?? ''))->stripTags()->squish()->limit(160)->toString());
        $editUrl = e($this->moonshineResourcePageUrl(UiTextResource::class, UiTextFormPage::class, $text->getKey()));
        $status = $text->is_active ? 'Показывается' : 'Скрыто';
        $statusClass = $text->is_active ? 'page-builder-block__status--active' : 'page-builder-block__status--draft';

        return <<<HTML
        <article class="page-builder-ui-field">
            <div>
                <span class="page-builder-block__status {$statusClass}">{$status}</span>
                <h5>{$label}</h5>
                <code>{$key}</code>
                <p>{$value}</p>
            </div>
            <a href="{$editUrl}">Редактировать поле</a>
        </article>
        HTML;
    }

    private function missingUiTextGroupHtml(string $group): string
    {
        $group = e($group);

        return <<<HTML
        <div class="page-builder-empty">
            <strong>Поля блока пока не заведены</strong>
            <span>Группа <code>{$group}</code> отсутствует в базе. После миграции недостающие постоянные тексты создадутся автоматически, без затирания уже отредактированных строк.</span>
        </div>
        HTML;
    }

    private function permanentBlockCards(): array
    {
        return [
            [
                'title' => 'Первый экран и философия',
                'kind' => 'PageBlock',
                'badge' => 'Главная / блоки страницы',
                'description' => 'Hero и большой текстовый экран. Здесь есть только те поля, которые использует конкретный тип блока.',
                'links' => [
                    ['label' => 'Все блоки страниц', 'url' => $this->moonshineResourcePageUrl(PageBlockResource::class, PageBlockIndexPage::class)],
                ],
            ],
            [
                'title' => 'Избранные проекты и портфолио',
                'kind' => 'Постоянный блок',
                'badge' => 'Портфолио',
                'primary_group' => 'portfolio-home',
                'groups' => [
                    ['label' => 'Подписи портфолио', 'group' => 'portfolio-home'],
                    ['label' => 'Избранные на главной', 'group' => 'home'],
                ],
                'description' => 'Карточки проектов, фоновые изображения, категории, локации, годы и подписи фильтров портфолио.',
                'links' => [
                    ['label' => 'Проекты', 'url' => $this->moonshineResourcePageUrl(ProjectResource::class, ProjectIndexPage::class)],
                    ['label' => 'Редактировать подписи портфолио', 'url' => $this->permanentGroupUrl('portfolio-home')],
                    ['label' => 'Подписи избранных на главной', 'url' => $this->permanentGroupUrl('home')],
                ],
            ],
            [
                'title' => 'Подбор стиля',
                'kind' => 'Постоянный блок',
                'badge' => 'Style Lab',
                'primary_group' => 'style-lab',
                'groups' => [
                    ['label' => 'Подбор стиля', 'group' => 'style-lab'],
                ],
                'description' => 'Заголовок Style Lab, варианты стилей, материалов, света, кнопки и служебные подписи.',
                'links' => [
                    ['label' => 'Открыть редактор с предпросмотром', 'url' => url('/' . trim((string) config('moonshine.prefix', 'admin'), '/') . '/style-lab-editor')],
                    ['label' => 'Таблица текстов', 'url' => $this->permanentGroupUrl('style-lab')],
                ],
            ],
            [
                'title' => 'О нас',
                'kind' => 'Постоянный блок',
                'badge' => 'О студии',
                'primary_group' => 'about-home',
                'groups' => [
                    ['label' => 'О нас на главной', 'group' => 'about-home'],
                    ['label' => 'Полная страница', 'group' => 'about-full'],
                ],
                'description' => 'Текст секции, факты, принципы, подпись изображения и кнопки перехода/контакта.',
                'links' => [
                    ['label' => 'Редактировать блок "О нас"', 'url' => $this->permanentGroupUrl('about-home')],
                    ['label' => 'Страницы', 'url' => $this->pageIndexUrl()],
                ],
            ],
            [
                'title' => 'Награды и дипломы',
                'kind' => 'Постоянный блок',
                'badge' => 'Награды',
                'primary_group' => 'awards-home',
                'groups' => [
                    ['label' => 'Заголовок секции', 'group' => 'awards-home'],
                ],
                'description' => 'Заголовок секции и сами карточки наград с описаниями и изображениями.',
                'links' => [
                    ['label' => 'Награды', 'url' => $this->moonshineResourcePageUrl(AwardResource::class, AwardIndexPage::class)],
                    ['label' => 'Редактировать заголовок секции', 'url' => $this->permanentGroupUrl('awards-home')],
                ],
            ],
            [
                'title' => 'Нам доверяют',
                'kind' => 'Постоянный блок',
                'badge' => 'Партнеры / доверие',
                'primary_group' => 'partners-home',
                'groups' => [
                    ['label' => 'Заголовок блока', 'group' => 'partners-home'],
                ],
                'description' => 'Один общий блок доверия для всех страниц: заголовок, текст, логотипы партнеров, названия и подписи.',
                'links' => [
                    ['label' => 'Партнеры', 'url' => $this->moonshineResourcePageUrl(PartnerResource::class, PartnerIndexPage::class)],
                    ['label' => 'Редактировать заголовок блока', 'url' => $this->permanentGroupUrl('partners-home')],
                ],
            ],
            [
                'title' => 'Страница партнерам',
                'kind' => 'Страница с блоками',
                'badge' => 'Партнерам',
                'description' => 'Первый экран, форматы сотрудничества, форма заявки, тексты кнопок и список партнеров/логотипов. Основные секции редактируются как блоки страницы.',
                'links' => [
                    ['label' => 'Открыть блоки страницы', 'url' => $this->pageBuilderPageUrl('partneram')],
                    ['label' => 'Партнеры и логотипы', 'url' => $this->moonshineResourcePageUrl(PartnerResource::class, PartnerIndexPage::class)],
                    ['label' => 'Заявки со страницы', 'url' => $this->moonshineResourcePageUrl(LeadResource::class, LeadIndexPage::class)],
                ],
            ],
            [
                'title' => 'Услуги и цены на главной',
                'kind' => 'Постоянный блок',
                'badge' => 'Услуги и цены',
                'primary_group' => 'services-home',
                'groups' => [
                    ['label' => 'Услуги и цены', 'group' => 'services-home'],
                    ['label' => 'Детальная услуга', 'group' => 'service-detail'],
                ],
                'description' => 'Блок как на главной: надзаголовок, заголовок, описание справа и 6 карточек услуг с названиями, ценами и текстами.',
                'links' => [
                    ['label' => 'Направления услуг: название, описание, картинка', 'url' => $this->moonshinePageUrl(ServiceDirectionsPage::class)],
                    ['label' => 'Карточки услуг: название, цена, описание', 'url' => $this->moonshineResourcePageUrl(ServiceResource::class, ServiceIndexPage::class)],
                    ['label' => 'Тексты блока: надзаголовок, заголовок, описание', 'url' => $this->permanentGroupUrl('services-home')],
                    ['label' => 'Общие блоки детальной услуги', 'url' => $this->permanentGroupUrl('service-detail')],
                ],
            ],
            [
                'title' => 'Квиз',
                'kind' => 'Постоянный блок',
                'badge' => 'Расчет проекта',
                'primary_group' => 'quiz',
                'groups' => [
                    ['label' => 'Квиз', 'group' => 'quiz'],
                ],
                'description' => 'Вопросы, варианты, кнопки, финальный шаг, подписи расчета и согласий.',
                'links' => [
                    ['label' => 'Редактировать квиз', 'url' => $this->permanentGroupUrl('quiz')],
                ],
            ],
            [
                'title' => 'FAQ',
                'kind' => 'Постоянный блок',
                'badge' => 'Частые вопросы',
                'primary_group' => 'faq-home',
                'groups' => [
                    ['label' => 'Заголовок FAQ', 'group' => 'faq-home'],
                ],
                'description' => 'Заголовок секции и список вопросов/ответов.',
                'links' => [
                    ['label' => 'Вопросы FAQ', 'url' => $this->moonshineResourcePageUrl(FaqResource::class, FaqIndexPage::class)],
                    ['label' => 'Редактировать заголовок секции', 'url' => $this->permanentGroupUrl('faq-home')],
                ],
            ],
            [
                'title' => 'Новости и статьи',
                'kind' => 'Постоянный блок',
                'badge' => 'Новости',
                'primary_group' => 'news-page',
                'groups' => [
                    ['label' => 'Страница новостей', 'group' => 'news-page'],
                ],
                'description' => 'Hero, список материалов, кнопки чтения, боковые карточки статьи, подписи времени чтения и блок "читать также".',
                'links' => [
                    ['label' => 'Новости', 'url' => $this->moonshineResourcePageUrl(NewsArticleResource::class, NewsArticleIndexPage::class)],
                    ['label' => 'Редактировать обвязку новостей', 'url' => $this->permanentGroupUrl('news-page')],
                ],
            ],
            [
                'title' => 'Акции и скидки',
                'kind' => 'Постоянный блок',
                'badge' => 'Акции',
                'primary_group' => 'promos-page',
                'groups' => [
                    ['label' => 'Страница акций', 'group' => 'promos-page'],
                ],
                'description' => 'Hero акций, подписи сроков, кнопки карточек и нижний CTA. Сами акции редактируются отдельными записями.',
                'links' => [
                    ['label' => 'Акции', 'url' => $this->moonshineResourcePageUrl(PromoResource::class, PromoIndexPage::class)],
                    ['label' => 'Редактировать обвязку акций', 'url' => $this->permanentGroupUrl('promos-page')],
                ],
            ],
            [
                'title' => 'Отзывы',
                'kind' => 'Постоянный блок',
                'badge' => 'Отзывы',
                'primary_group' => 'reviews-page',
                'groups' => [
                    ['label' => 'Страница отзывов', 'group' => 'reviews-page'],
                ],
                'description' => 'Hero отзывов, заголовки секций, подписи ответов администратора, форма отзыва и кнопки.',
                'links' => [
                    ['label' => 'Отзывы', 'url' => $this->moonshineResourcePageUrl(ReviewResource::class, ReviewIndexPage::class)],
                    ['label' => 'Редактировать обвязку отзывов', 'url' => $this->permanentGroupUrl('reviews-page')],
                ],
            ],
            [
                'title' => 'Карьера и вакансии',
                'kind' => 'Постоянный блок',
                'badge' => 'Карьера',
                'primary_group' => 'career-page',
                'groups' => [
                    ['label' => 'Страница карьеры', 'group' => 'career-page'],
                ],
                'description' => 'Hero, статистика, подписи карточек вакансий, CTA и поля модального отклика. Сами вакансии редактируются отдельными записями.',
                'links' => [
                    ['label' => 'Вакансии', 'url' => $this->moonshineResourcePageUrl(VacancyResource::class, VacancyIndexPage::class)],
                    ['label' => 'Редактировать обвязку карьеры', 'url' => $this->permanentGroupUrl('career-page')],
                ],
            ],
            [
                'title' => 'Детальная страница проекта',
                'kind' => 'Постоянный блок',
                'badge' => 'Портфолио / кейс',
                'primary_group' => 'portfolio-case',
                'groups' => [
                    ['label' => 'Обвязка кейса', 'group' => 'portfolio-case'],
                    ['label' => 'Портфолио', 'group' => 'portfolio-home'],
                ],
                'description' => 'Паспорт проекта, навигация, галерея, до/после, процесс, похожие проекты и финальный CTA на странице проекта.',
                'links' => [
                    ['label' => 'Проекты', 'url' => $this->moonshineResourcePageUrl(ProjectResource::class, ProjectIndexPage::class)],
                    ['label' => 'Редактировать кейс', 'url' => $this->permanentGroupUrl('portfolio-case')],
                ],
            ],
            [
                'title' => 'Общий слайдер',
                'kind' => 'Постоянный блок',
                'badge' => 'Slider',
                'primary_group' => 'slider',
                'groups' => [
                    ['label' => 'Подписи слайдера', 'group' => 'slider'],
                ],
                'description' => 'ARIA-подписи точек и стрелок hero-слайдеров на страницах.',
                'links' => [
                    ['label' => 'Редактировать подписи слайдера', 'url' => $this->permanentGroupUrl('slider')],
                ],
            ],
            [
                'title' => 'Контакты и форма',
                'kind' => 'Постоянный блок',
                'badge' => 'Контакты',
                'primary_group' => 'contact-home',
                'groups' => [
                    ['label' => 'Контакты и форма', 'group' => 'contact-home'],
                ],
                'description' => 'Телефон, почта, адрес, карта, поля формы, кнопки и подписи контактов.',
                'links' => [
                    ['label' => 'Настройки сайта', 'url' => $this->moonshineResourcePageUrl(SiteSettingResource::class, SiteSettingIndexPage::class)],
                    ['label' => 'Редактировать блок контактов', 'url' => $this->permanentGroupUrl('contact-home')],
                ],
            ],
        ];
    }

    private function blockCard(PageBlock $block): string
    {
        $title = e($block->fieldRu('title') ?: $block->fieldRu('eyebrow') ?: 'Блок без заголовка');
        $type = e($this->blockTypeLabel($block->type));
        $position = (int) $block->position;
        $status = $block->is_active ? 'Показывается' : 'Скрыт';
        $statusClass = $block->is_active ? 'page-builder-block__status--active' : 'page-builder-block__status--draft';
        $text = e($this->previewText($block));
        $chips = $this->blockChipsHtml($block);
        $editUrl = e($this->moonshineResourcePageUrl(PageBlockResource::class, PageBlockFormPage::class, $block->getKey()));
        $imageHtml = $this->blockPreviewImage($block);

        return <<<HTML
        <article class="page-builder-block">
            <div class="page-builder-block__media">{$imageHtml}</div>
            <div class="page-builder-block__body">
                <div class="page-builder-block__top">
                    <span class="page-builder-block__type">{$type}</span>
                    <span class="page-builder-block__status {$statusClass}">{$status}</span>
                </div>
                <h3>{$title}</h3>
                <p>{$text}</p>
                <div class="page-builder-block__chips">
                    {$chips}
                </div>
            </div>
            <div class="page-builder-block__actions">
                <a href="{$editUrl}">Редактировать все поля</a>
            </div>
        </article>
        HTML;
    }

    private function blockChipsHtml(PageBlock $block): string
    {
        $type = (string) $block->type;
        $pageSlug = (string) ($block->page?->slug ?? '');
        $chips = ['Позиция: ' . (int) $block->position];

        if (in_array($type, ['media', 'gallery'], true) || ($type === 'hero' && $pageSlug !== 'home')) {
            $chips[] = 'Картинок: ' . $this->blockImagesCount($block);
        }

        if ($type === 'media') {
            $chips[] = 'Motion: ' . ($block->motion_preset ?: 'motion');
            $chips[] = 'Медиа: ' . ($block->media_position ?: 'auto');
            $chips[] = 'Вариант: ' . ($block->visual_variant ?: 'default');
            $chips[] = 'Состояние: ' . ($block->card_state ?: 'normal');
        }

        if ($type === 'hero' && $pageSlug !== 'home') {
            $chips[] = 'Motion: ' . ($block->motion_preset ?: 'motion');
        }

        if (in_array($type, ['quote', 'cta'], true)) {
            $chips[] = 'Состояние: ' . ($block->card_state ?: 'normal');
        }

        return collect($chips)
            ->map(static fn (string $chip): string => '<span>' . e($chip) . '</span>')
            ->implode('');
    }

    private function blockPreviewImage(PageBlock $block): string
    {
        $image = collect(preg_split('/\R/u', (string) $block->image) ?: [])
            ->map(static fn (string $line): string => trim($line))
            ->filter()
            ->first();

        if (! $image) {
            $label = match ((string) $block->type) {
                'hero' => $block->page?->slug === 'home' ? 'Видео-фон в верстке' : 'Hero без слайдов',
                'text' => 'Текстовый блок',
                'quote' => 'Цитата',
                'cta' => 'CTA без медиа',
                default => 'Нет медиа',
            };

            return '<span class="page-builder-block__placeholder">' . e($label) . '</span>';
        }

        $src = preg_match('/^(https?:)?\/\//i', $image) === 1 || str_starts_with($image, '/')
            ? $image
            : '/storage/' . ltrim($image, '/');

        return sprintf('<img src="%s" alt="" loading="lazy">', e($src));
    }

    private function noBlocksHtml(): string
    {
        return <<<HTML
        <div class="page-builder-empty">
            <strong>У страницы пока нет подключенных блоков</strong>
            <span>Новые блоки отсюда не создаются: редактор предназначен для существующих секций сайта.</span>
        </div>
        HTML;
    }

    private function emptyState(): string
    {
        return <<<HTML
        <div class="page-builder-empty">
            <strong>Страниц пока нет</strong>
            <span>Сначала создайте страницу, потом добавьте к ней блоки.</span>
        </div>
        HTML;
    }

    /**
     * @param class-string $resourceClass
     * @param class-string $pageClass
     */
    private function moonshineResourcePageUrl(string $resourceClass, string $pageClass, int|string|null $item = null): string
    {
        $params = [
            'resourceUri' => (new UriKey($resourceClass))->generate(),
            'pageUri' => (new UriKey($pageClass))->generate(),
        ];

        if ($item !== null) {
            $params['resourceItem'] = $item;
        }

        return route('moonshine.resource.page', $params);
    }

    private function pageIndexUrl(): string
    {
        return $this->moonshineResourcePageUrl(PageResource::class, PageIndexPage::class);
    }

    private function pageBuilderPageUrl(string $slug): string
    {
        return $this->currentBuilderUrl() . '?page=' . urlencode($slug);
    }

    private function currentBuilderUrl(): string
    {
        return url('/' . ltrim(request()->path(), '/'));
    }

    private function pageGroupUrl(string $slug, string $group, string $part = ''): string
    {
        $url = $this->pageBuilderPageUrl($slug) . '&edit=' . urlencode($group);

        return $part !== '' ? $url . '&part=' . urlencode($part) : $url;
    }

    /**
     * @param class-string $pageClass
     */
    private function moonshinePageUrl(string $pageClass): string
    {
        return url('/' . trim((string) config('moonshine.prefix', 'admin'), '/') . '/page/' . (new UriKey($pageClass))->generate());
    }

    private function permanentGroupUrl(string $group): string
    {
        return $this->currentBuilderUrl() . '?section=permanent&block=' . urlencode($group);
    }

    private function previewText(PageBlock $block): string
    {
        return str($block->fieldRu('subtitle') ?: $block->fieldRu('text') ?: 'Описание не заполнено')
            ->stripTags()
            ->squish()
            ->limit(180)
            ->toString();
    }

    private function blockImagesCount(PageBlock $block): int
    {
        return collect(preg_split('/\R/u', (string) $block->image) ?: [])
            ->map(static fn (string $line): string => trim($line))
            ->filter()
            ->count();
    }

    private function blockTypeLabel(?string $type): string
    {
        return match ($type) {
            'hero' => 'Первый экран',
            'text' => 'Текст',
            'media' => 'Текст + медиа',
            'gallery' => 'Галерея',
            'list' => 'Список карточек',
            'form' => 'Форма заявки',
            'quote' => 'Цитата',
            'cta' => 'CTA',
            default => filled($type) ? (string) $type : 'Блок',
        };
    }
}
