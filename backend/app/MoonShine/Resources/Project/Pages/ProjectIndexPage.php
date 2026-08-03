<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Project\Pages;

use App\Models\Project;
use App\MoonShine\Resources\Project\ProjectResource;
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
 * @extends IndexPage<ProjectResource>
 */
class ProjectIndexPage extends IndexPage
{
    protected bool $isLazy = true;

    /**
     * @return list<AssetElementContract>
     */
    protected function assets(): array
    {
        return [
            ...parent::assets(),
            InlineCss::make((string) file_get_contents(resource_path('css/project-admin.css'))),
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make('#', 'id')->sortable(),

            Preview::make('Проект', 'title', function (mixed $item): string {
                if (! is_object($item)) {
                    return '<div class="proj-card"><div class="proj-card__title">'.e((string) $item).'</div></div>';
                }

                $rawTitle = $this->cmsText($item->fieldRu('title') ?? '');
                $title = e($rawTitle);
                $slug = e($item->slug ?? '');
                $initial = mb_strtoupper(mb_substr($rawTitle !== '' ? $rawTitle : 'P', 0, 1));
                $image = $item->effective_image ?? null;

                $thumb = ! empty($image)
                    ? sprintf(
                        '<div class="proj-card__thumb"><img src="%s" alt="" loading="lazy"></div>',
                        e($image)
                    )
                    : sprintf('<div class="proj-card__thumb">%s</div>', $initial);

                return sprintf(
                    '<div class="proj-card">%s<div><div class="proj-card__title">%s</div><div class="proj-card__slug">/%s</div></div></div>',
                    $thumb,
                    $title,
                    $slug,
                );
            }),

            Preview::make('Категория', 'category', function (mixed $item): string {
                $cat = is_object($item) ? $this->cmsText($item->fieldRu('category') ?? '') : (string) $item;

                return ! empty($cat)
                    ? '<span class="proj-category">'.e($cat).'</span>'
                    : '<span style="color:#cbd5e1;font-size:12px;">—</span>';
            }),

            Preview::make('Место / Год', 'location', function (mixed $item): string {
                if (! is_object($item)) {
                    return '<span style="color:#cbd5e1;font-size:12px;">—</span>';
                }

                $parts = '';
                $location = $this->cmsText($item->fieldRu('location') ?? '');

                if (! empty($location)) {
                    $parts .= '<div class="proj-meta__item">📍 '.e($location).'</div>';
                }
                if (! empty($item->year)) {
                    $parts .= '<div class="proj-meta__item">📅 '.e($item->year).'</div>';
                }

                return $parts
                    ? '<div class="proj-meta">'.$parts.'</div>'
                    : '<span style="color:#cbd5e1;font-size:12px;">—</span>';
            }),

            Preview::make('Статус', 'is_published', function (mixed $item): string {
                $published = is_object($item) ? (bool) ($item->is_published ?? false) : (bool) $item;
                $featured = is_object($item) ? (bool) ($item->is_featured ?? false) : false;

                $pubBadge = $published
                    ? '<span class="proj-badge proj-badge--published">Опубликован</span>'
                    : '<span class="proj-badge proj-badge--draft">Черновик</span>';

                $featBadge = $featured
                    ? '<span class="proj-badge proj-badge--featured">★ Избранный</span>'
                    : '';

                return '<div class="proj-badges">'.$pubBadge.$featBadge.'</div>';
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
        $total = Project::count();
        $published = Project::where('is_published', true)->count();
        $unpublished = max($total - $published, 0);
        $featured = Project::where('is_featured', true)->count();
        $categories = Project::query()
            ->get()
            ->map(static fn (Project $project): ?string => $project->fieldRu('category'))
            ->filter()
            ->unique()
            ->count();

        $thisMonth = Project::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $withImage = Project::whereNotNull('image')->where('image', '!=', '')->count();

        $latest = Project::latest('created_at')->first(['created_at']);
        $latestDate = $latest?->created_at?->format('d.m.Y') ?? 'Нет';

        $createUrl = $this->getResource()->getFormPageUrl();

        return <<<HTML
        <div class="proj-overview">
            <section class="proj-overview__hero">
                <div class="proj-overview__hero-grid">
                    <div>
                        <p class="proj-overview__eyebrow">Портфолио студии</p>
                        <h1 class="proj-overview__title">Проекты</h1>
                        <p class="proj-overview__lead">
                            Управляйте портфолио: публикуйте выполненные работы,
                            выделяйте лучшие как избранные и следите за охватом
                            по категориям. Ниже — стандартный список с фильтрами.
                        </p>
                        <div class="proj-overview__chips">
                            <span class="proj-overview__chip">Всего: {$total}</span>
                            <span class="proj-overview__chip">Опубликовано: {$published}</span>
                            <span class="proj-overview__chip">Избранных: {$featured}</span>
                            <span class="proj-overview__chip">Категорий: {$categories}</span>
                        </div>
                        <a class="proj-overview__action-btn" href="{$createUrl}">+ Добавить проект</a>
                    </div>

                    <div class="proj-overview__spotlight">
                        <article class="proj-overview__spotlight-card">
                            <p class="proj-overview__spotlight-label">Всего проектов</p>
                            <p class="proj-overview__spotlight-value">{$total}</p>
                            <p class="proj-overview__spotlight-meta">В портфолио</p>
                        </article>
                        <article class="proj-overview__spotlight-card">
                            <p class="proj-overview__spotlight-label">Опубликовано</p>
                            <p class="proj-overview__spotlight-value">{$published}</p>
                            <p class="proj-overview__spotlight-meta">Видны на сайте</p>
                        </article>
                        <article class="proj-overview__spotlight-card">
                            <p class="proj-overview__spotlight-label">Избранных</p>
                            <p class="proj-overview__spotlight-value">{$featured}</p>
                            <p class="proj-overview__spotlight-meta">Выделены на главной</p>
                        </article>
                        <article class="proj-overview__spotlight-card">
                            <p class="proj-overview__spotlight-label">Категорий</p>
                            <p class="proj-overview__spotlight-value">{$categories}</p>
                            <p class="proj-overview__spotlight-meta">Уникальных типов работ</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="proj-stats">
                <article class="proj-stat">
                    <p class="proj-stat-label">За этот месяц</p>
                    <p class="proj-stat-value">+{$thisMonth}</p>
                    <p class="proj-stat-meta">Новых проектов добавлено</p>
                </article>
                <article class="proj-stat">
                    <p class="proj-stat-label">С обложкой</p>
                    <p class="proj-stat-value">{$withImage}</p>
                    <p class="proj-stat-meta">Есть изображение</p>
                </article>
                <article class="proj-stat">
                    <p class="proj-stat-label">Черновики</p>
                    <p class="proj-stat-value">{$unpublished}</p>
                    <p class="proj-stat-meta">Скрыты от посетителей</p>
                </article>
                <article class="proj-stat">
                    <p class="proj-stat-label">Последний добавлен</p>
                    <p class="proj-stat-value">{$latestDate}</p>
                    <p class="proj-stat-meta">Дата публикации</p>
                </article>
            </section>
        </div>
        HTML;
    }

    private function cmsText(?string $value): string
    {
        return html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
