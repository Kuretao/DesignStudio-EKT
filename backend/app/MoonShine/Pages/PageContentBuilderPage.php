<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\Models\Page as ContentPage;
use App\Models\PageBlock;
use App\MoonShine\Resources\Page\PageResource;
use App\MoonShine\Resources\Page\Pages\PageFormPage;
use App\MoonShine\Resources\PageBlock\PageBlockResource;
use App\MoonShine\Resources\PageBlock\Pages\PageBlockFormPage;
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
        $createUrl = $this->moonshineResourcePageUrl(PageBlockResource::class, PageBlockFormPage::class) . '?page_id=' . $page->getKey();
        $createUrlEscaped = e($createUrl);
        $blocks = $page->blocks->isNotEmpty()
            ? $page->blocks->map(fn (PageBlock $block): string => $this->blockCard($block))->implode('')
            : $this->noBlocksHtml($createUrl);

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

            <div class="page-builder-blocks">
                {$blocks}
            </div>
        </div>
        HTML;
    }

    private function blockCard(PageBlock $block): string
    {
        $title = e($block->fieldRu('title') ?: $block->fieldRu('eyebrow') ?: 'Блок без заголовка');
        $type = e($this->blockTypeLabel($block->type));
        $position = (int) $block->position;
        $status = $block->is_active ? 'Показывается' : 'Скрыт';
        $statusClass = $block->is_active ? 'page-builder-block__status--active' : 'page-builder-block__status--draft';
        $text = e($this->previewText($block));
        $images = $this->blockImagesCount($block);
        $motion = e($block->motion_preset ?: 'motion');
        $variant = e($block->visual_variant ?: 'default');
        $state = e($block->card_state ?: 'normal');
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
                    <span>Позиция: {$position}</span>
                    <span>Слайдов: {$images}</span>
                    <span>Motion: {$motion}</span>
                    <span>Вариант: {$variant}</span>
                    <span>Состояние: {$state}</span>
                </div>
            </div>
            <div class="page-builder-block__actions">
                <a href="{$editUrl}">Редактировать все поля</a>
            </div>
        </article>
        HTML;
    }

    private function blockPreviewImage(PageBlock $block): string
    {
        $image = collect(preg_split('/\R/u', (string) $block->image) ?: [])
            ->map(static fn (string $line): string => trim($line))
            ->filter()
            ->first();

        if (! $image) {
            return '<span class="page-builder-block__placeholder">Нет медиа</span>';
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
