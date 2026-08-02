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

        $selectedSlug = (string) request()->query('page', $pages->first()?->slug ?? '');
        $selectedPage = $pages->firstWhere('slug', $selectedSlug) ?? $pages->first();
        $section = request()->query('section') === 'permanent' ? 'permanent' : 'pages';

        $pageCards = $pages->map(fn (ContentPage $page): string => $this->pageCard($page, $selectedPage?->id === $page->id))->implode('');
        $builderBody = $section === 'permanent'
            ? $this->permanentBlocksHtml((string) request()->query('block', ''))
            : ($selectedPage ? $this->selectedPageHtml($selectedPage) : $this->emptyState());
        $pageSectionClass = $section === 'pages' ? ' page-builder-switch__item--active' : '';
        $permanentSectionClass = $section === 'permanent' ? ' page-builder-switch__item--active' : '';
        $pagesUrl = e(request()->url());
        $permanentUrl = e(request()->url() . '?section=permanent');
        $sidebarTitle = $section === 'permanent' ? 'Постоянные блоки' : 'Страницы сайта';
        $sidebarText = $section === 'permanent'
            ? 'Откройте общую секцию, которая повторяется на сайте.'
            : 'Откройте страницу, затем нужный блок.';
        $sidebarList = $section === 'permanent'
            ? $this->permanentSidebarHtml()
            : '<div class="page-builder__page-list">' . $pageCards . '</div>';
        $totalBlocks = (int) $pages->sum('blocks_count');
        $activeBlocks = (int) $pages->sum('active_blocks_count');

        return <<<HTML
        <section class="page-builder">
            <div class="page-builder__hero">
                <div>
                    <p class="page-builder__eyebrow">Страницы и блоки</p>
                    <h1>Конструктор страниц</h1>
                    <p>Выберите страницу слева и редактируйте только ее блоки: тексты, кнопки, картинки, слайдеры, motion и состояние карточек. Больше не нужно искать нужный блок в общей таблице.</p>
                </div>
                <div class="page-builder__stats">
                    <div><strong>{$pages->count()}</strong><span>страниц</span></div>
                    <div><strong>{$totalBlocks}</strong><span>блоков</span></div>
                    <div><strong>{$activeBlocks}</strong><span>показываются</span></div>
                </div>
            </div>

            <div class="page-builder__layout">
                <aside class="page-builder__sidebar">
                    <div class="page-builder-switch">
                        <a class="page-builder-switch__item{$pageSectionClass}" href="{$pagesUrl}">
                            <strong>Страницы</strong>
                            <span>Главная, услуги, новости, портфолио</span>
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

    private function pageCard(ContentPage $page, bool $active): string
    {
        $url = e(request()->url() . '?page=' . urlencode((string) $page->slug));
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
        $blocks = $page->blocks->isNotEmpty()
            ? $page->blocks->map(fn (PageBlock $block): string => $this->blockCard($block))->implode('')
            : $this->noBlocksHtml();

        return <<<HTML
        <div class="page-builder-selected">
            <div class="page-builder-selected__head">
                <div>
                    <p class="page-builder__eyebrow">Редактируется страница</p>
                    <h2>{$title}</h2>
                    <p><code>{$path}</code></p>
                </div>
                <div class="page-builder-selected__actions">
                    <a href="{$pageEditUrl}">Настройки страницы</a>
                </div>
            </div>

            <div class="page-builder-blocks">
                {$blocks}
            </div>

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
                $url = e(request()->url() . '?section=permanent' . ($group !== '' ? '&block=' . urlencode($group) : ''));
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
        $backUrl = e(request()->url() . '?section=permanent');
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
                $url = e(request()->url() . '?section=permanent&block=' . urlencode($itemGroup));
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
                    ['label' => 'Редактировать блок', 'url' => $this->permanentGroupUrl('style-lab')],
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
                'title' => 'Услуги, направления и этапы',
                'kind' => 'Постоянный блок',
                'badge' => 'Услуги и цены',
                'primary_group' => 'services-home',
                'groups' => [
                    ['label' => 'Услуги и цены', 'group' => 'services-home'],
                    ['label' => 'Детальная услуга', 'group' => 'service-detail'],
                ],
                'description' => 'Блок как на скриншоте: надзаголовок, заголовок, описание, карточки услуг, цены, сроки, направления и этапы.',
                'links' => [
                    ['label' => 'Услуги', 'url' => $this->moonshineResourcePageUrl(ServiceResource::class, ServiceIndexPage::class)],
                    ['label' => 'Редактировать заголовок блока', 'url' => $this->permanentGroupUrl('services-home')],
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

    private function permanentGroupUrl(string $group): string
    {
        return request()->url() . '?section=permanent&block=' . urlencode($group);
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
            'quote' => 'Цитата',
            'cta' => 'CTA',
            default => filled($type) ? (string) $type : 'Блок',
        };
    }
}
