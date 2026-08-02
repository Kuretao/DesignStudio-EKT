<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\NewsArticle\Pages;

use App\Models\NewsArticle;
use App\MoonShine\Resources\NewsArticle\NewsArticleResource;
use MoonShine\AssetManager\InlineCss;
use MoonShine\Contracts\AssetManager\AssetElementContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\UI\Components\FlexibleRender;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Preview;

/**
 * @extends IndexPage<NewsArticleResource>
 */
class NewsArticleIndexPage extends IndexPage
{
    protected bool $isLazy = true;

    /**
     * @return list<AssetElementContract>
     */
    protected function assets(): array
    {
        return [
            ...parent::assets(),
            InlineCss::make((string) file_get_contents(resource_path('css/news-admin.css'))),
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make('#', 'id')->sortable(),

            Preview::make('Статья', 'title', function (mixed $item): string {
                if (! is_object($item)) {
                    return '<div class="news-card"><div class="news-card__title">'.e((string) $item).'</div></div>';
                }

                $rawTitle = $item->fieldRu('title') ?? '';
                $title = e($rawTitle);
                $slug = e($item->slug ?? '');
                $categoryValue = $item->fieldRu('category');
                $category = $categoryValue ? e($categoryValue) : null;
                $initial = mb_strtoupper(mb_substr($rawTitle !== '' ? $rawTitle : 'N', 0, 1));

                $image = $item->effective_image;

                $thumb = ! empty($image)
                    ? sprintf(
                        '<div class="news-card__thumb"><img src="%s" alt="" loading="lazy"></div>',
                        e($image)
                    )
                    : sprintf('<div class="news-card__thumb">%s</div>', $initial);

                $categoryHtml = $category
                    ? sprintf('<div class="news-card__category">%s</div>', $category)
                    : '';

                return sprintf(
                    '<div class="news-card">%s<div>%s<div class="news-card__title">%s</div><div class="news-card__slug">/%s</div></div></div>',
                    $thumb,
                    $categoryHtml,
                    $title,
                    $slug,
                );
            }),

            Preview::make('Аннотация', 'preview', function (mixed $item): string {
                $text = is_object($item) ? ($item->fieldRu('preview') ?? '') : (string) $item;

                return ! empty($text)
                    ? '<div class="news-preview">'.e($text).'</div>'
                    : '<span style="color:#cbd5e1;font-size:12px;">—</span>';
            }),

            Preview::make('Дата / Чтение', 'date', function (mixed $item): string {
                if (! is_object($item)) {
                    return '<span style="color:#cbd5e1;font-size:12px;">—</span>';
                }

                $parts = '';

                $date = $item->fieldRu('date');
                $readingTime = $item->fieldRu('reading_time');

                if (! empty($date)) {
                    $parts .= sprintf(
                        '<div class="news-meta__date">📅 %s</div>',
                        e($date)
                    );
                }

                if (! empty($readingTime)) {
                    $parts .= sprintf(
                        '<span class="news-meta__reading-pill">📖 %s</span>',
                        e($readingTime)
                    );
                }

                return $parts
                    ? '<div class="news-meta">'.$parts.'</div>'
                    : '<span style="color:#cbd5e1;font-size:12px;">—</span>';
            }),

            Preview::make('Статус', 'is_published', function (mixed $item): string {
                $published = is_object($item) ? (bool) ($item->is_published ?? false) : (bool) $item;

                return $published
                    ? '<span class="news-badge news-badge--published">Опубликована</span>'
                    : '<span class="news-badge news-badge--draft">Черновик</span>';
            }),

            Number::make('Поз.', 'position')->sortable(),
        ];
    }

    /**
     * @return list<ComponentContract>
     */
    protected function topLayer(): array
    {
        return [
            FlexibleRender::make($this->buildOverviewHtml()),
            ...parent::topLayer(),
        ];
    }

    private function buildOverviewHtml(): string
    {
        $total = NewsArticle::count();
        $published = NewsArticle::where('is_published', true)->count();
        $unpublished = max($total - $published, 0);
        $categories = NewsArticle::query()
            ->get()
            ->map(static fn (NewsArticle $article): ?string => $article->fieldRu('category'))
            ->filter()
            ->unique()
            ->count();
        $withImage = NewsArticle::where(function ($query): void {
            $query->whereNotNull('image_file')->where('image_file', '!=', '')
                ->orWhere(function ($fallback): void {
                    $fallback->whereNotNull('image')->where('image', '!=', '');
                });
        })->count();

        $thisMonth = NewsArticle::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $withReading = NewsArticle::where(function ($query): void {
            $query->whereNotNull('reading_time_ru')
                ->where('reading_time_ru', '!=', '')
                ->orWhere(function ($fallback): void {
                    $fallback->whereNotNull('reading_time')
                        ->where('reading_time', '!=', '');
                });
        })->count();

        $latest = NewsArticle::latest('created_at')->first(['created_at']);
        $latestDate = $latest?->created_at?->format('d.m.Y') ?? 'Нет';

        $createUrl = $this->getResource()->getFormPageUrl();

        return <<<HTML
        <div class="news-overview">
            <section class="news-overview__hero">
                <div class="news-overview__hero-grid">
                    <div>
                        <p class="news-overview__eyebrow">Редакция блога</p>
                        <h1 class="news-overview__title">Новости и статьи</h1>
                        <p class="news-overview__lead">
                            Ведите блог студии: публикуйте новости, кейсы и материалы
                            по категориям, указывайте время чтения и управляйте
                            видимостью каждой статьи. Ниже — список с фильтрами.
                        </p>
                        <div class="news-overview__chips">
                            <span class="news-overview__chip">Всего: {$total}</span>
                            <span class="news-overview__chip">Опубликовано: {$published}</span>
                            <span class="news-overview__chip">Категорий: {$categories}</span>
                            <span class="news-overview__chip">С обложкой: {$withImage}</span>
                        </div>
                        <a class="news-overview__action-btn" href="{$createUrl}">+ Добавить статью</a>
                    </div>

                    <div class="news-overview__spotlight">
                        <article class="news-overview__spotlight-card">
                            <p class="news-overview__spotlight-label">Всего статей</p>
                            <p class="news-overview__spotlight-value">{$total}</p>
                            <p class="news-overview__spotlight-meta">В блоге</p>
                        </article>
                        <article class="news-overview__spotlight-card">
                            <p class="news-overview__spotlight-label">Опубликовано</p>
                            <p class="news-overview__spotlight-value">{$published}</p>
                            <p class="news-overview__spotlight-meta">Видны читателям</p>
                        </article>
                        <article class="news-overview__spotlight-card">
                            <p class="news-overview__spotlight-label">Категорий</p>
                            <p class="news-overview__spotlight-value">{$categories}</p>
                            <p class="news-overview__spotlight-meta">Уникальных рубрик</p>
                        </article>
                        <article class="news-overview__spotlight-card">
                            <p class="news-overview__spotlight-label">С обложкой</p>
                            <p class="news-overview__spotlight-value">{$withImage}</p>
                            <p class="news-overview__spotlight-meta">Есть изображение</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="news-stats">
                <article class="news-stat">
                    <p class="news-stat-label">За этот месяц</p>
                    <p class="news-stat-value">+{$thisMonth}</p>
                    <p class="news-stat-meta">Новых статей добавлено</p>
                </article>
                <article class="news-stat">
                    <p class="news-stat-label">Черновики</p>
                    <p class="news-stat-value">{$unpublished}</p>
                    <p class="news-stat-meta">Скрыты от читателей</p>
                </article>
                <article class="news-stat">
                    <p class="news-stat-label">Со временем чтения</p>
                    <p class="news-stat-value">{$withReading}</p>
                    <p class="news-stat-meta">Указана длительность</p>
                </article>
                <article class="news-stat">
                    <p class="news-stat-label">Последняя добавлена</p>
                    <p class="news-stat-value">{$latestDate}</p>
                    <p class="news-stat-meta">Дата добавления</p>
                </article>
            </section>
        </div>
        HTML;
    }
}
