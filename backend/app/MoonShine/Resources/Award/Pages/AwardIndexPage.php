<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Award\Pages;

use App\Models\Award;
use App\MoonShine\Resources\Award\AwardResource;
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
 * @extends IndexPage<AwardResource>
 */
class AwardIndexPage extends IndexPage
{
    protected bool $isLazy = true;

    /**
     * @return list<AssetElementContract>
     */
    protected function assets(): array
    {
        return [
            ...parent::assets(),
            InlineCss::make((string) file_get_contents(resource_path('css/award-admin.css'))),
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make('#', 'id')->sortable(),

            Preview::make('Награда', 'title', function (mixed $item): string {
                if (! is_object($item)) {
                    return '<div class="award-card"><div class="award-card__title">'.e((string) $item).'</div></div>';
                }

                $rawTitle = $this->cmsText($item->fieldRu('title') ?? '');
                $title = e($rawTitle);
                $issuerValue = $this->cmsText($item->fieldRu('issuer') ?? '');
                $issuer = $issuerValue ? e($issuerValue) : null;
                $initial = mb_strtoupper(mb_substr($rawTitle !== '' ? $rawTitle : 'A', 0, 1));

                $thumbSrc = $item->effective_image ?? null;
                $thumb = ! empty($thumbSrc)
                    ? sprintf(
                        '<div class="award-card__thumb"><img src="%s" alt="" loading="lazy"></div>',
                        e($thumbSrc)
                    )
                    : sprintf('<div class="award-card__thumb">%s</div>', $initial);

                $issuerHtml = $issuer
                    ? '<div class="award-card__issuer">'.$issuer.'</div>'
                    : '';

                return sprintf(
                    '<div class="award-card">%s<div><div class="award-card__title">%s</div>%s</div></div>',
                    $thumb,
                    $title,
                    $issuerHtml,
                );
            }),

            Preview::make('Описание', 'description', function (mixed $item): string {
                $text = is_object($item) ? $this->cmsText($item->fieldRu('description') ?? '') : (string) $item;

                return ! empty($text)
                    ? '<div class="award-description">'.e($text).'</div>'
                    : '<span style="color:#cbd5e1;font-size:12px;">—</span>';
            }),

            Preview::make('Год', 'year', function (mixed $item): string {
                $year = is_object($item) ? ($item->year ?? '') : (string) $item;

                return ! empty($year)
                    ? '<span>'.e((string) $year).'</span>'
                    : '<span style="color:#cbd5e1;font-size:12px;">—</span>';
            }),

            Preview::make('Статус', 'is_active', function (mixed $item): string {
                $active = is_object($item) ? (bool) ($item->is_active ?? false) : (bool) $item;

                return $active
                    ? '<span class="award-badge award-badge--active">Активна</span>'
                    : '<span class="award-badge award-badge--inactive">Отключена</span>';
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
        $total = Award::count();
        $active = Award::where('is_active', true)->count();
        $inactive = max($total - $active, 0);
        $withImage = Award::where(function ($q) {
            $q->whereNotNull('image_file')->where('image_file', '!=', '')
                ->orWhere(function ($q2) {
                    $q2->whereNotNull('image')->where('image', '!=', '');
                });
        })->count();

        $latest = Award::latest('created_at')->first(['created_at']);
        $latestDate = $latest?->created_at?->format('d.m.Y') ?? 'Нет';

        $createUrl = $this->getResource()->getFormPageUrl();

        return <<<HTML
        <div class="award-overview">
            <section class="award-overview__hero">
                <div class="award-overview__hero-grid">
                    <div>
                        <p class="award-overview__eyebrow">Награды и дипломы</p>
                        <h1 class="award-overview__title">Подтверждения опыта<br>и доверия</h1>
                        <p class="award-overview__lead">
                            Управляйте наградами, дипломами и сертификатами студии.
                            Они показываются на главной странице и в разделе
                            "Награды и дипломы". Ниже — список с фильтрами.
                        </p>
                        <div class="award-overview__chips">
                            <span class="award-overview__chip">Всего: {$total}</span>
                            <span class="award-overview__chip">Активных: {$active}</span>
                            <span class="award-overview__chip">С изображением: {$withImage}</span>
                            <span class="award-overview__chip">Последняя: {$latestDate}</span>
                        </div>
                        <a class="award-overview__action-btn" href="{$createUrl}">+ Добавить награду</a>
                    </div>

                    <div class="award-overview__spotlight">
                        <article class="award-overview__spotlight-card">
                            <p class="award-overview__spotlight-label">Всего наград</p>
                            <p class="award-overview__spotlight-value">{$total}</p>
                            <p class="award-overview__spotlight-meta">В базе</p>
                        </article>
                        <article class="award-overview__spotlight-card">
                            <p class="award-overview__spotlight-label">Активных</p>
                            <p class="award-overview__spotlight-value">{$active}</p>
                            <p class="award-overview__spotlight-meta">Видны на сайте</p>
                        </article>
                        <article class="award-overview__spotlight-card">
                            <p class="award-overview__spotlight-label">Отключено</p>
                            <p class="award-overview__spotlight-value">{$inactive}</p>
                            <p class="award-overview__spotlight-meta">Скрыты от посетителей</p>
                        </article>
                        <article class="award-overview__spotlight-card">
                            <p class="award-overview__spotlight-label">С изображением</p>
                            <p class="award-overview__spotlight-value">{$withImage}</p>
                            <p class="award-overview__spotlight-meta">Есть обложка</p>
                        </article>
                    </div>
                </div>
            </section>
        </div>
        HTML;
    }

    private function cmsText(?string $value): string
    {
        return html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
