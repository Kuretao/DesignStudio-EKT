<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\Models\Page as ContentPage;
use App\Models\PageBlock;
use App\MoonShine\Resources\Award\AwardResource;
use App\MoonShine\Resources\Award\Pages\AwardIndexPage;
use App\MoonShine\Resources\Faq\FaqResource;
use App\MoonShine\Resources\Faq\Pages\FaqIndexPage;
use App\MoonShine\Resources\Page\PageResource;
use App\MoonShine\Resources\Page\Pages\PageFormPage;
use App\MoonShine\Resources\Page\Pages\PageIndexPage;
use App\MoonShine\Resources\PageBlock\PageBlockResource;
use App\MoonShine\Resources\PageBlock\Pages\PageBlockFormPage;
use App\MoonShine\Resources\PageBlock\Pages\PageBlockIndexPage;
use App\MoonShine\Resources\Partner\PartnerResource;
use App\MoonShine\Resources\Partner\Pages\PartnerIndexPage;
use App\MoonShine\Resources\Project\Pages\ProjectIndexPage;
use App\MoonShine\Resources\Project\ProjectResource;
use App\MoonShine\Resources\Service\Pages\ServiceIndexPage;
use App\MoonShine\Resources\Service\ServiceResource;
use App\MoonShine\Resources\SiteSetting\Pages\SiteSettingIndexPage;
use App\MoonShine\Resources\SiteSetting\SiteSettingResource;
use App\MoonShine\Resources\UiText\Pages\UiTextIndexPage;
use App\MoonShine\Resources\UiText\UiTextResource;
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

        $pageCards = $pages->map(fn (ContentPage $page): string => $this->pageCard($page, $selectedPage?->id === $page->id))->implode('');
        $builderBody = $selectedPage ? $this->selectedPageHtml($selectedPage) : $this->emptyState();
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
                    <div class="page-builder__sidebar-head">
                        <strong>Страницы сайта</strong>
                        <span>Откройте страницу, затем нужный блок.</span>
                    </div>
                    <div class="page-builder__page-list">{$pageCards}</div>
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
        $createUrl = $this->createBlockUrl($page, 'hero');
        $createUrlEscaped = e($createUrl);
        $createButtons = $this->createBlockButtons($page);
        $blocks = $page->blocks->isNotEmpty()
            ? $page->blocks->map(fn (PageBlock $block): string => $this->blockCard($block))->implode('')
            : $this->noBlocksHtml($createUrl);
        $homeSections = $page->slug === 'home' ? $this->homeSectionsHtml() : '';

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
                    <a class="page-builder-selected__primary" href="{$createUrlEscaped}">+ Добавить блок</a>
                </div>
            </div>

            <div class="page-builder-create">
                <strong>Добавить блок по типу</strong>
                <span>Форма откроется сразу с нужными полями, без лишних кнопок и медиа там, где их нет.</span>
                <div>{$createButtons}</div>
            </div>

            <div class="page-builder-blocks">
                {$blocks}
            </div>

            {$homeSections}
        </div>
        HTML;
    }

    private function createBlockButtons(ContentPage $page): string
    {
        return collect([
            'hero' => 'Hero',
            'text' => 'Текст',
            'media' => 'Медиа',
            'gallery' => 'Галерея',
            'quote' => 'Цитата',
            'cta' => 'CTA',
        ])->map(fn (string $label, string $type): string => sprintf(
            '<a href="%s">%s</a>',
            e($this->createBlockUrl($page, $type)),
            e($label),
        ))->implode('');
    }

    private function createBlockUrl(ContentPage $page, string $type): string
    {
        return $this->moonshineResourcePageUrl(PageBlockResource::class, PageBlockFormPage::class)
            . '?page_id=' . $page->getKey()
            . '&type=' . urlencode($type);
    }

    private function homeSectionsHtml(): string
    {
        $cards = collect($this->homeSectionCards())
            ->map(fn (array $section): string => $this->homeSectionCard($section))
            ->implode('');

        return <<<HTML
        <section class="page-builder-home-map">
            <div class="page-builder-home-map__head">
                <p class="page-builder__eyebrow">Реальные секции главной</p>
                <h3>Главная редактируется не только блоками</h3>
                <span>Ниже все участки главной, которые сайт реально выводит: тексты, коллекции карточек, формы, контакты и списки.</span>
            </div>
            <div class="page-builder-home-map__grid">{$cards}</div>
        </section>
        HTML;
    }

    private function homeSectionCard(array $section): string
    {
        $title = e($section['title']);
        $description = e($section['description']);
        $kind = e($section['kind']);
        $links = collect($section['links'])
            ->map(fn (array $link): string => sprintf(
                '<a href="%s">%s</a>',
                e($link['url']),
                e($link['label']),
            ))
            ->implode('');

        return <<<HTML
        <article class="page-builder-section-card">
            <span>{$kind}</span>
            <h4>{$title}</h4>
            <p>{$description}</p>
            <div>{$links}</div>
        </article>
        HTML;
    }

    private function homeSectionCards(): array
    {
        return [
            [
                'title' => 'Первый экран и философия',
                'kind' => 'PageBlock',
                'description' => 'Hero и большой текстовый экран. Здесь есть только те поля, которые использует конкретный тип блока.',
                'links' => [
                    ['label' => 'Все блоки страниц', 'url' => $this->moonshineResourcePageUrl(PageBlockResource::class, PageBlockIndexPage::class)],
                ],
            ],
            [
                'title' => 'Избранные проекты и портфолио',
                'kind' => 'Проекты + UI',
                'description' => 'Карточки проектов, фоновые изображения, категории, локации, годы и подписи фильтров портфолио.',
                'links' => [
                    ['label' => 'Проекты', 'url' => $this->moonshineResourcePageUrl(ProjectResource::class, ProjectIndexPage::class)],
                    ['label' => 'Тексты портфолио', 'url' => $this->uiTextIndexUrl('portfolio-home')],
                    ['label' => 'Подписи избранных', 'url' => $this->uiTextIndexUrl('home')],
                ],
            ],
            [
                'title' => 'Подбор стиля',
                'kind' => 'UI',
                'description' => 'Заголовок Style Lab, варианты стилей, материалов, света, кнопки и служебные подписи.',
                'links' => [
                    ['label' => 'Тексты Style Lab', 'url' => $this->uiTextIndexUrl('style-lab')],
                ],
            ],
            [
                'title' => 'О нас',
                'kind' => 'UI + страница',
                'description' => 'Текст секции, факты, принципы, подпись изображения и кнопки перехода/контакта.',
                'links' => [
                    ['label' => 'Тексты "О нас"', 'url' => $this->uiTextIndexUrl('about-home')],
                    ['label' => 'Страницы', 'url' => $this->pageIndexUrl()],
                ],
            ],
            [
                'title' => 'Награды и дипломы',
                'kind' => 'Награды + UI',
                'description' => 'Заголовок секции и сами карточки наград с описаниями и изображениями.',
                'links' => [
                    ['label' => 'Награды', 'url' => $this->moonshineResourcePageUrl(AwardResource::class, AwardIndexPage::class)],
                    ['label' => 'Тексты секции', 'url' => $this->uiTextIndexUrl('awards-home')],
                ],
            ],
            [
                'title' => 'Партнеры',
                'kind' => 'Партнеры + UI',
                'description' => 'Логотипы партнеров, названия, подписи и общий текст секции доверия.',
                'links' => [
                    ['label' => 'Партнеры', 'url' => $this->moonshineResourcePageUrl(PartnerResource::class, PartnerIndexPage::class)],
                    ['label' => 'Тексты секции', 'url' => $this->uiTextIndexUrl('partners-home')],
                ],
            ],
            [
                'title' => 'Услуги, направления и этапы',
                'kind' => 'Услуги + UI',
                'description' => 'Карточки услуг, цены, посадочные страницы, заголовки секций и этапы работы.',
                'links' => [
                    ['label' => 'Услуги', 'url' => $this->moonshineResourcePageUrl(ServiceResource::class, ServiceIndexPage::class)],
                    ['label' => 'Тексты услуг', 'url' => $this->uiTextIndexUrl('services-home')],
                ],
            ],
            [
                'title' => 'Квиз',
                'kind' => 'UI',
                'description' => 'Вопросы, варианты, кнопки, финальный шаг, подписи расчета и согласий.',
                'links' => [
                    ['label' => 'Тексты квиза', 'url' => $this->uiTextIndexUrl('quiz')],
                ],
            ],
            [
                'title' => 'FAQ',
                'kind' => 'FAQ + UI',
                'description' => 'Заголовок секции и список вопросов/ответов.',
                'links' => [
                    ['label' => 'Вопросы FAQ', 'url' => $this->moonshineResourcePageUrl(FaqResource::class, FaqIndexPage::class)],
                    ['label' => 'Тексты секции', 'url' => $this->uiTextIndexUrl('faq-home')],
                ],
            ],
            [
                'title' => 'Контакты и форма',
                'kind' => 'Настройки + UI',
                'description' => 'Телефон, почта, адрес, карта, поля формы, кнопки и подписи контактов.',
                'links' => [
                    ['label' => 'Настройки сайта', 'url' => $this->moonshineResourcePageUrl(SiteSettingResource::class, SiteSettingIndexPage::class)],
                    ['label' => 'Тексты контактов', 'url' => $this->uiTextIndexUrl('contact-home')],
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

    private function noBlocksHtml(string $createUrl): string
    {
        $createUrl = e($createUrl);

        return <<<HTML
        <div class="page-builder-empty">
            <strong>У страницы пока нет блоков</strong>
            <span>Создайте первый hero, текстовый блок, галерею или CTA.</span>
            <a href="{$createUrl}">+ Добавить блок</a>
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

    private function uiTextIndexUrl(string $group): string
    {
        return $this->moonshineResourcePageUrl(UiTextResource::class, UiTextIndexPage::class)
            . '?search=' . urlencode($group);
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
