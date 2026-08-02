<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Partner\Pages;

use App\Models\Partner;
use App\MoonShine\Resources\Partner\PartnerResource;
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
 * @extends IndexPage<PartnerResource>
 */
class PartnerIndexPage extends IndexPage
{
    protected bool $isLazy = true;

    /**
     * @return list<AssetElementContract>
     */
    protected function assets(): array
    {
        return [
            ...parent::assets(),
            InlineCss::make((string) file_get_contents(resource_path('css/partner-admin.css'))),
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make('#', 'id')->sortable(),

            Preview::make('Партнер', 'name', function (mixed $item): string {
                if (! is_object($item)) {
                    return '<div class="partner-card"><div class="partner-card__title">' . e((string) $item) . '</div></div>';
                }

                $rawName = method_exists($item, 'fieldRu') ? ($item->fieldRu('name') ?? '') : ($item->name ?? '');
                $name    = e($rawName);
                $initial = mb_strtoupper(mb_substr($rawName ?: 'P', 0, 1));

                $thumbSrc = $item->effective_logo ?? null;
                $thumb = ! empty($thumbSrc)
                    ? sprintf(
                        '<div class="partner-card__thumb"><img src="%s" alt="" loading="lazy"></div>',
                        e($thumbSrc)
                    )
                    : sprintf('<div class="partner-card__thumb">%s</div>', $initial);

                return sprintf(
                    '<div class="partner-card">%s<div><div class="partner-card__title">%s</div></div></div>',
                    $thumb,
                    $name,
                );
            }),

            Preview::make('Описание', 'note', function (mixed $item): string {
                $text = is_object($item)
                    ? (method_exists($item, 'fieldRu') ? ($item->fieldRu('note') ?? '') : ($item->note ?? ''))
                    : (string) $item;
                return ! empty($text)
                    ? '<div class="partner-note">' . e($text) . '</div>'
                    : '<span style="color:#cbd5e1;font-size:12px;">—</span>';
            }),

            Preview::make('Статус', 'is_active', function (mixed $item): string {
                $active = is_object($item) ? (bool) ($item->is_active ?? false) : (bool) $item;

                return $active
                    ? '<span class="partner-badge partner-badge--active">Активен</span>'
                    : '<span class="partner-badge partner-badge--inactive">Отключен</span>';
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
        $total    = Partner::count();
        $active   = Partner::where('is_active', true)->count();
        $inactive = max($total - $active, 0);
        $withLogo = Partner::where(function ($q) {
            $q->whereNotNull('logo_file')->where('logo_file', '!=', '')
              ->orWhere(function ($q2) {
                  $q2->whereNotNull('logo')->where('logo', '!=', '');
              });
        })->count();

        $latest     = Partner::latest('created_at')->first(['created_at']);
        $latestDate = $latest?->created_at?->format('d.m.Y') ?? 'Нет';

        $createUrl = $this->getResource()->getFormPageUrl();

        return <<<HTML
        <div class="partner-overview">
            <section class="partner-overview__hero">
                <div class="partner-overview__hero-grid">
                    <div>
                        <p class="partner-overview__eyebrow">Нам доверяют</p>
                        <h1 class="partner-overview__title">Девелоперы<br>и застройщики</h1>
                        <p class="partner-overview__lead">
                            Управляйте логотипами и описаниями партнеров и застройщиков.
                            Они показываются в блоке "Нам доверяют" на главной странице
                            и на странице услуг. Ниже — список с фильтрами.
                        </p>
                        <div class="partner-overview__chips">
                            <span class="partner-overview__chip">Всего: {$total}</span>
                            <span class="partner-overview__chip">Активных: {$active}</span>
                            <span class="partner-overview__chip">С логотипом: {$withLogo}</span>
                            <span class="partner-overview__chip">Последний: {$latestDate}</span>
                        </div>
                        <a class="partner-overview__action-btn" href="{$createUrl}">+ Добавить партнера</a>
                    </div>

                    <div class="partner-overview__spotlight">
                        <article class="partner-overview__spotlight-card">
                            <p class="partner-overview__spotlight-label">Всего</p>
                            <p class="partner-overview__spotlight-value">{$total}</p>
                            <p class="partner-overview__spotlight-meta">В базе</p>
                        </article>
                        <article class="partner-overview__spotlight-card">
                            <p class="partner-overview__spotlight-label">Активных</p>
                            <p class="partner-overview__spotlight-value">{$active}</p>
                            <p class="partner-overview__spotlight-meta">Видны на сайте</p>
                        </article>
                        <article class="partner-overview__spotlight-card">
                            <p class="partner-overview__spotlight-label">Отключено</p>
                            <p class="partner-overview__spotlight-value">{$inactive}</p>
                            <p class="partner-overview__spotlight-meta">Скрыты от посетителей</p>
                        </article>
                        <article class="partner-overview__spotlight-card">
                            <p class="partner-overview__spotlight-label">С логотипом</p>
                            <p class="partner-overview__spotlight-value">{$withLogo}</p>
                            <p class="partner-overview__spotlight-meta">Есть изображение</p>
                        </article>
                    </div>
                </div>
            </section>
        </div>
        HTML;
    }
}
