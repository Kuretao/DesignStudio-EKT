<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Service\Pages;

use App\Models\Service;
use App\MoonShine\Resources\Service\ServiceResource;
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
 * @extends IndexPage<ServiceResource>
 */
class ServiceIndexPage extends IndexPage
{
    protected bool $isLazy = true;

    /**
     * @return list<AssetElementContract>
     */
    protected function assets(): array
    {
        return [
            ...parent::assets(),
            InlineCss::make((string) file_get_contents(resource_path('css/service-admin.css'))),
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make('#', 'id')->sortable(),

            Preview::make('Услуга', 'title', function (mixed $item): string {
                if (! is_object($item)) {
                    return '<div class="svc-card"><div class="svc-card__title">'.e((string) $item).'</div></div>';
                }

                $rawTitle = $this->cmsText($item->fieldRu('title') ?? '');
                $title = e($rawTitle);
                $slug = e($item->slug ?? '');
                $eyebrowValue = $this->cmsText($item->fieldRu('eyebrow') ?? '');
                $eyebrow = $eyebrowValue ? e($eyebrowValue) : null;
                $initial = mb_strtoupper(mb_substr($rawTitle !== '' ? $rawTitle : 'S', 0, 1));

                $image = $item->effective_image;

                $thumb = ! empty($image)
                    ? sprintf(
                        '<div class="svc-card__thumb"><img src="%s" alt="" loading="lazy"></div>',
                        e($image)
                    )
                    : sprintf('<div class="svc-card__thumb">%s</div>', $initial);

                $eyebrowHtml = $eyebrow
                    ? sprintf('<div class="svc-card__eyebrow">%s</div>', $eyebrow)
                    : '';

                return sprintf(
                    '<div class="svc-card">%s<div>%s<div class="svc-card__title">%s</div><div class="svc-card__slug">/%s</div></div></div>',
                    $thumb,
                    $eyebrowHtml,
                    $title,
                    $slug,
                );
            }),

            Preview::make('Цена / Срок', 'price', function (mixed $item): string {
                if (! is_object($item)) {
                    return '<span style="color:#cbd5e1;font-size:12px;">—</span>';
                }

                $parts = '';

                $price = $this->cmsText($item->fieldRu('price') ?? '');
                $timeline = $this->cmsText($item->fieldRu('timeline') ?? '');

                if (! empty($price)) {
                    $parts .= sprintf(
                        '<span class="svc-price">💰 %s</span>',
                        e($price)
                    );
                }

                if (! empty($timeline)) {
                    $parts .= sprintf(
                        '<span class="svc-timeline">⏱ %s</span>',
                        e($timeline)
                    );
                }

                return $parts
                    ? '<div class="svc-pricing">'.$parts.'</div>'
                    : '<span style="color:#cbd5e1;font-size:12px;">—</span>';
            }),

            Preview::make('Направление', 'service_direction_id', function (mixed $item): string {
                if (! is_object($item)) {
                    return '<span style="color:#cbd5e1;font-size:12px;">—</span>';
                }

                $direction = $item->direction?->labelRu();

                return filled($direction)
                    ? '<span class="svc-badge svc-badge--published">'.e($direction).'</span>'
                    : '<span class="svc-badge svc-badge--draft">Без направления</span>';
            }),

            Preview::make('Статус', 'is_published', function (mixed $item): string {
                $published = is_object($item) ? (bool) ($item->is_published ?? false) : (bool) $item;

                return $published
                    ? '<span class="svc-badge svc-badge--published">Опубликована</span>'
                    : '<span class="svc-badge svc-badge--draft">Черновик</span>';
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
        $total = Service::count();
        $published = Service::where('is_published', true)->count();
        $unpublished = max($total - $published, 0);
        $withPrice = Service::where(function ($query): void {
            $query->whereNotNull('price_ru')
                ->where('price_ru', '!=', '')
                ->orWhere(function ($fallback): void {
                    $fallback->whereNotNull('price')
                        ->where('price', '!=', '');
                });
        })->count();
        $withImage = Service::where(function ($query): void {
            $query->whereNotNull('image_file')->where('image_file', '!=', '')
                ->orWhere(function ($fallback): void {
                    $fallback->whereNotNull('image')->where('image', '!=', '');
                });
        })->count();
        $withoutPrice = max($total - $withPrice, 0);

        $thisMonth = Service::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $latest = Service::latest('created_at')->first(['created_at']);
        $latestDate = $latest?->created_at?->format('d.m.Y') ?? 'Нет';

        $createUrl = $this->getResource()->getFormPageUrl();

        return <<<HTML
        <div class="svc-overview">
            <section class="svc-overview__hero">
                <div class="svc-overview__hero-grid">
                    <div>
                        <p class="svc-overview__eyebrow">Каталог услуг</p>
                        <h1 class="svc-overview__title">Услуги</h1>
                        <p class="svc-overview__lead">
                            Управляйте каталогом услуг студии: добавляйте новые предложения,
                            указывайте цены и сроки, публикуйте для клиентов.
                            Ниже — стандартный список с фильтрами.
                        </p>
                        <div class="svc-overview__chips">
                            <span class="svc-overview__chip">Всего: {$total}</span>
                            <span class="svc-overview__chip">Опубликовано: {$published}</span>
                            <span class="svc-overview__chip">С ценой: {$withPrice}</span>
                            <span class="svc-overview__chip">С обложкой: {$withImage}</span>
                        </div>
                        <a class="svc-overview__action-btn" href="{$createUrl}">+ Добавить услугу</a>
                    </div>

                    <div class="svc-overview__spotlight">
                        <article class="svc-overview__spotlight-card">
                            <p class="svc-overview__spotlight-label">Всего услуг</p>
                            <p class="svc-overview__spotlight-value">{$total}</p>
                            <p class="svc-overview__spotlight-meta">В каталоге</p>
                        </article>
                        <article class="svc-overview__spotlight-card">
                            <p class="svc-overview__spotlight-label">Опубликовано</p>
                            <p class="svc-overview__spotlight-value">{$published}</p>
                            <p class="svc-overview__spotlight-meta">Видны клиентам</p>
                        </article>
                        <article class="svc-overview__spotlight-card">
                            <p class="svc-overview__spotlight-label">С ценой</p>
                            <p class="svc-overview__spotlight-value">{$withPrice}</p>
                            <p class="svc-overview__spotlight-meta">Указана стоимость</p>
                        </article>
                        <article class="svc-overview__spotlight-card">
                            <p class="svc-overview__spotlight-label">С обложкой</p>
                            <p class="svc-overview__spotlight-value">{$withImage}</p>
                            <p class="svc-overview__spotlight-meta">Есть изображение</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="svc-stats">
                <article class="svc-stat">
                    <p class="svc-stat-label">За этот месяц</p>
                    <p class="svc-stat-value">+{$thisMonth}</p>
                    <p class="svc-stat-meta">Новых услуг добавлено</p>
                </article>
                <article class="svc-stat">
                    <p class="svc-stat-label">Черновики</p>
                    <p class="svc-stat-value">{$unpublished}</p>
                    <p class="svc-stat-meta">Скрыты от клиентов</p>
                </article>
                <article class="svc-stat">
                    <p class="svc-stat-label">Без цены</p>
                    <p class="svc-stat-value">{$withoutPrice}</p>
                    <p class="svc-stat-meta">Стоимость не указана</p>
                </article>
                <article class="svc-stat">
                    <p class="svc-stat-label">Последняя добавлена</p>
                    <p class="svc-stat-value">{$latestDate}</p>
                    <p class="svc-stat-meta">Дата добавления</p>
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
