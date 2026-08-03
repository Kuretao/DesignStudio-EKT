<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\PageBlock\Pages;

use App\MoonShine\Resources\PageBlock\PageBlockResource;
use App\MoonShine\Support\CmsFieldSets;
use Illuminate\Validation\Rule;
use MoonShine\AssetManager\InlineCss;
use MoonShine\Contracts\AssetManager\AssetElementContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Components\Alert;
use MoonShine\UI\Components\FlexibleRender;
use MoonShine\UI\Components\Icon;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;

/**
 * @extends FormPage<PageBlockResource>
 */
class PageBlockFormPage extends FormPage
{
    /**
     * @return list<AssetElementContract>
     */
    protected function assets(): array
    {
        return [
            ...parent::assets(),
            InlineCss::make((string) file_get_contents(resource_path('css/page-block-admin.css'))),
        ];
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        $type = $this->currentBlockType();
        $pageSlug = $this->currentPageSlug();

        return [
            FlexibleRender::make($this->overviewHtml()),
            Alert::make('information-circle', 'info')
                ->content($this->typeHint($type, $pageSlug)),
            Tabs::make($this->tabsForType($type, $pageSlug))->vertical()->customAttributes(['class' => 'page-block-tabs']),
        ];
    }

    private function tabsForType(string $type, ?string $pageSlug): array
    {
        $tabs = [
            $this->targetTab(),
            $this->contentTab($type),
        ];

        if ($this->hasMediaTab($type, $pageSlug)) {
            $tabs[] = $this->mediaTab($type);
        }

        if (in_array($type, ['hero', 'media', 'cta'], true)) {
            $tabs[] = $this->actionTab($type);
        }

        if ($this->behaviorFields($type) !== []) {
            $tabs[] = $this->behaviorTab($type);
        }

        $tabs[] = $this->publishTab();

        return $tabs;
    }

    private function targetTab(): Tab
    {
        return Tab::make('Где блок', [
            Box::make('Страница и тип секции', [
                $this->sectionNote(
                    'Блок всегда принадлежит одной странице',
                    'Выберите страницу и тип секции. Если поменяли тип уже существующего блока, сохраните - форма сразу покажет только поля нового типа.'
                ),
                Grid::make([
                    Column::make(CmsFieldSets::pageBlockSection('target'))->columnSpan(6),
                    Column::make([
                        FlexibleRender::make($this->targetGuideHtml()),
                    ])->columnSpan(6),
                ]),
            ])->icon('squares-2x2')->customAttributes(['class' => 'page-block-section']),
        ])->icon('squares-2x2')->active();
    }

    private function contentTab(string $type): Tab
    {
        return Tab::make($type === 'quote' ? 'Цитата' : 'Текст', [
            Box::make($this->contentTitle($type), [
                $this->sectionNote($this->contentNoteTitle($type), $this->contentNoteText($type)),
                Grid::make($this->contentGrid($type)),
            ])->icon('pencil-square')->customAttributes(['class' => 'page-block-section']),
        ])->icon('document-text');
    }

    private function mediaTab(string $type): Tab
    {
        $title = $type === 'gallery' ? 'Картинки галереи' : 'Картинка и слайды';
        $note = $type === 'gallery'
            ? 'Каждая строка - отдельная картинка галереи. Если в блоке одна картинка, будет одна карточка.'
            : 'Для слайдера добавляйте каждую картинку с новой строки. Если нужна одна картинка, оставьте одну строку.';

        return Tab::make($type === 'gallery' ? 'Галерея' : 'Медиа', [
            Box::make($title, [
                $this->sectionNote('Медиа есть только у этого типа блока', $note),
                Grid::make([
                    Column::make(array_slice(CmsFieldSets::pageBlockSection('media'), 0, 1))->columnSpan(7),
                    Column::make(array_slice(CmsFieldSets::pageBlockSection('media'), 1))->columnSpan(5),
                ]),
            ])->icon('photo')->customAttributes(['class' => 'page-block-section']),
        ])->icon('photo');
    }

    private function actionTab(string $type): Tab
    {
        return Tab::make('Кнопка', [
            Box::make('Ссылка блока', [
                $this->sectionNote(
                    'Кнопка есть у этого блока',
                    $type === 'cta'
                        ? 'CTA без кнопки обычно теряет смысл, поэтому заполните текст и адрес.'
                        : 'Если кнопка не нужна визуально, оставьте текст кнопки пустым.'
                ),
                Grid::make([
                    Column::make(CmsFieldSets::pageBlockSection('action'))->columnSpan(7),
                    Column::make([
                        FlexibleRender::make($this->buttonGuideHtml()),
                    ])->columnSpan(5),
                ]),
            ])->icon('link')->customAttributes(['class' => 'page-block-section']),
        ])->icon('link');
    }

    private function behaviorTab(string $type): Tab
    {
        return Tab::make('Поведение', [
            Box::make('Визуальный режим', [
                $this->sectionNote(
                    'Только настройки, которые реально используются этим типом',
                    $this->behaviorHint($type)
                ),
                Grid::make([
                    Column::make($this->behaviorFields($type))->columnSpan(6),
                    Column::make([
                        FlexibleRender::make($this->behaviorGuideHtml($type)),
                    ])->columnSpan(6),
                ]),
            ])->icon('sparkles')->customAttributes(['class' => 'page-block-section']),
        ])->icon('sparkles');
    }

    private function publishTab(): Tab
    {
        return Tab::make('Публикация', [
            Box::make('Порядок и видимость', [
                $this->sectionNote(
                    'Показывать или спрятать',
                    'Позицию удобно задавать десятками: 10, 20, 30. Так потом можно вставить новый блок между существующими.'
                ),
                Grid::make([
                    Column::make(CmsFieldSets::pageBlockSection('display'))->columnSpan(5),
                    Column::make([
                        FlexibleRender::make($this->publishGuideHtml()),
                    ])->columnSpan(7),
                ]),
            ])->icon('check-circle')->customAttributes(['class' => 'page-block-section']),
        ])->icon('check-circle');
    }

    private function contentGrid(string $type): array
    {
        $fields = CmsFieldSets::pageBlockSection('content');

        return match ($type) {
            'hero' => [
                Column::make(array_slice($fields, 0, 4))->columnSpan(6),
                Column::make(array_slice($fields, 4, 2))->columnSpan(6),
            ],
            'text', 'media', 'gallery' => [
                Column::make(array_slice($fields, 0, 4))->columnSpan(6),
                Column::make(array_slice($fields, 4))->columnSpan(6),
            ],
            'quote' => [
                Column::make([$fields[0], $fields[1], $fields[6], $fields[7]])->columnSpan(7),
                Column::make([$fields[2], $fields[3]])->columnSpan(5),
            ],
            'cta' => [
                Column::make(array_slice($fields, 0, 4))->columnSpan(6),
                Column::make(array_slice($fields, 4, 2))->columnSpan(6),
            ],
            default => [
                Column::make(array_slice($fields, 0, 4))->columnSpan(6),
                Column::make(array_slice($fields, 4))->columnSpan(6),
            ],
        };
    }

    private function behaviorFields(string $type): array
    {
        $fields = CmsFieldSets::pageBlockSection('behavior');

        return match ($type) {
            'media' => [$fields[0], $fields[1], $fields[2], $fields[3]],
            'hero' => [$fields[2]],
            'quote', 'cta' => [$fields[3]],
            default => [],
        };
    }

    private function currentBlockType(): string
    {
        $item = $this->getResource()->getItem();
        $type = (string) ($item?->type ?: request()->query('type', 'hero'));

        return in_array($type, ['hero', 'text', 'media', 'gallery', 'quote', 'cta'], true) ? $type : 'hero';
    }

    private function currentPageSlug(): ?string
    {
        $item = $this->getResource()->getItem();

        if ($item?->page?->slug) {
            return (string) $item->page->slug;
        }

        $pageId = request()->integer('page_id');

        if ($pageId <= 0) {
            return null;
        }

        $slug = \App\Models\Page::query()->whereKey($pageId)->value('slug');

        return $slug === null ? null : (string) $slug;
    }

    private function hasMediaTab(string $type, ?string $pageSlug): bool
    {
        if ($type === 'hero' && $pageSlug === 'home') {
            return false;
        }

        return in_array($type, ['hero', 'media', 'gallery'], true);
    }

    private function typeHint(string $type, ?string $pageSlug): string
    {
        if ($type === 'hero' && $pageSlug === 'home') {
            return 'Hero главной редактирует текст и кнопку. Медиа-вкладки тут нет, потому что на главной сейчас используется видео-фон из верстки, а не CMS-слайдер.';
        }

        return match ($type) {
            'hero' => 'Hero редактирует первый экран: подпись, заголовок, описание, кнопку и слайды, если они реально используются на этой странице.',
            'text' => 'Текстовый блок без медиа и без кнопки: только текстовая часть, порядок и показ.',
            'media' => 'Блок "текст + медиа": здесь есть текст, изображения, положение медиа, motion и кнопка.',
            'gallery' => 'Галерея/слайдер: редактируются заголовок секции и список картинок. Кнопки тут нет.',
            'quote' => 'Цитата: редактируются подпись, текст цитаты и автор/заголовок. Медиа и кнопок у этого блока нет.',
            'cta' => 'CTA: призыв к действию с текстом и кнопкой. Медиа у этого блока нет.',
            default => 'Форма показывает только поля, которые относятся к выбранному типу блока.',
        };
    }

    private function contentTitle(string $type): string
    {
        return match ($type) {
            'hero' => 'Текст первого экрана',
            'quote' => 'Текст цитаты',
            'cta' => 'Текст призыва к действию',
            default => 'Текст секции',
        };
    }

    private function contentNoteTitle(string $type): string
    {
        return match ($type) {
            'quote' => 'Цитата не имеет картинки и кнопки',
            'gallery' => 'Текст только подписывает галерею',
            default => 'Поля соответствуют видимой части блока',
        };
    }

    private function contentNoteText(string $type): string
    {
        return match ($type) {
            'hero' => 'Заполните подпись, большой заголовок и короткое описание. Дополнительный длинный текст для hero не показывается, поэтому его тут нет.',
            'quote' => 'Основной текст цитаты заполняется в поле "Дополнительный текст". Заголовок можно использовать как автора или короткую подпись.',
            'gallery' => 'Заголовок и описание появляются над изображениями. Сами картинки редактируются во вкладке "Галерея".',
            'cta' => 'Короткий заголовок, описание и кнопка. Дополнительный длинный текст для CTA не нужен.',
            default => 'Редактируйте только текст, который реально может быть показан у этого типа блока.',
        };
    }

    private function behaviorHint(string $type): string
    {
        return match ($type) {
            'media' => 'Media-блок реально использует вариант, положение картинки, motion и состояние карточки.',
            'hero' => 'Hero использует только motion-подпись для набора слайдов.',
            'quote' => 'Цитата использует состояние карточки, например выделение.',
            'cta' => 'CTA использует состояние карточки, например приглушенный режим.',
            default => 'У этого типа нет дополнительных визуальных настроек.',
        };
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'page_id' => ['required', 'integer', 'exists:pages,id'],
            'type' => ['required', Rule::in(['hero', 'text', 'media', 'gallery', 'quote', 'cta'])],
            'visual_variant' => ['nullable', Rule::in(['default', 'wide', 'accent', 'compact', 'split'])],
            'media_position' => ['nullable', Rule::in(['', 'right', 'left', 'top', 'background'])],
            'motion_preset' => ['nullable', Rule::in(['none', 'motion', 'slides', 'story', 'read', 'preview'])],
            'card_state' => ['nullable', Rule::in(['normal', 'featured', 'muted', 'disabled'])],
            'eyebrow_ru' => ['nullable', 'string', 'max:255'],
            'eyebrow_en' => ['nullable', 'string', 'max:255'],
            'title_ru' => ['nullable', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'subtitle_ru' => ['nullable', 'string'],
            'subtitle_en' => ['nullable', 'string'],
            'text_ru' => ['nullable', 'string'],
            'text_en' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'image_alt_ru' => ['nullable', 'string', 'max:255'],
            'image_alt_en' => ['nullable', 'string', 'max:255'],
            'link_label_ru' => ['nullable', 'string', 'max:255'],
            'link_label_en' => ['nullable', 'string', 'max:255'],
            'link_href' => ['nullable', 'string', 'max:2048'],
            'settings' => ['nullable'],
            'position' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function validationMessages(): array
    {
        return [
            'page_id.required' => 'Выберите страницу, на которой должен появиться блок.',
            'page_id.exists' => 'Выбранная страница не найдена. Выберите страницу из списка.',
            'type.required' => 'Выберите вид блока.',
            'type.in' => 'Выберите вид блока из списка.',
            'visual_variant.in' => 'Выберите визуальный вариант блока из списка.',
            'media_position.in' => 'Выберите положение медиа из списка.',
            'motion_preset.in' => 'Выберите motion-режим из списка.',
            'card_state.in' => 'Выберите состояние карточки из списка.',
            'eyebrow_ru.max' => 'Маленькая строка RU над заголовком должна быть короче 255 символов.',
            'eyebrow_en.max' => 'Маленькая строка EN над заголовком должна быть короче 255 символов.',
            'title_ru.max' => 'Заголовок блока RU должен быть короче 255 символов.',
            'title_en.max' => 'Заголовок блока EN должен быть короче 255 символов.',
            'image_alt_ru.max' => 'Alt изображения RU должен быть короче 255 символов.',
            'image_alt_en.max' => 'Alt изображения EN должен быть короче 255 символов.',
            'link_label_ru.max' => 'Текст кнопки RU должен быть короче 255 символов.',
            'link_label_en.max' => 'Текст кнопки EN должен быть короче 255 символов.',
            'link_href.max' => 'Адрес кнопки получился слишком длинным.',
            'position.required' => 'Укажите порядок блока на странице.',
            'position.integer' => 'Порядок блока должен быть числом.',
            'position.min' => 'Порядок блока не может быть отрицательным.',
        ];
    }

    private function sectionNote(string $title, string $text): ComponentContract
    {
        return FlexibleRender::make(sprintf(
            '<div class="page-block-note"><strong>%s</strong><span>%s</span></div>',
            e($title),
            e($text),
        ));
    }

    private function targetGuideHtml(): string
    {
        return <<<HTML
        <div class="page-block-editor-tip">
            <strong>Как выбирать тип:</strong>
            <span>Hero - первый экран. Media - текст с изображением. Gallery - несколько изображений. Quote - крупная цитата. CTA - финальный призыв с кнопкой.</span>
        </div>
        HTML;
    }

    private function buttonGuideHtml(): string
    {
        return <<<HTML
        <div class="page-block-editor-tip">
            <strong>Адрес кнопки:</strong>
            <span>Внутренние страницы начинаются с /, внешние - с https://. Для модалки или якоря можно использовать #section.</span>
        </div>
        HTML;
    }

    private function behaviorGuideHtml(string $type): string
    {
        $text = match ($type) {
            'media' => 'Если картинка должна быть с другой стороны - меняйте "Положение медиа". Если слайдера нет, motion можно поставить "Без motion".',
            'hero' => 'Если у hero одна картинка или видео задано в коде страницы, motion можно не трогать.',
            'quote' => 'Для цитаты обычно достаточно обычного или выделенного состояния.',
            'cta' => 'Для финального призыва обычно достаточно обычного состояния; приглушенный режим подойдет для вторичного CTA.',
            default => 'Здесь нет лишних настроек для выбранного типа.',
        };

        return <<<HTML
        <div class="page-block-editor-tip">
            <strong>По делу:</strong>
            <span>{$text}</span>
        </div>
        HTML;
    }

    private function publishGuideHtml(): string
    {
        return <<<HTML
        <div class="page-block-editor-tip">
            <strong>Не удаляйте без нужды:</strong>
            <span>Если блок временно не нужен, выключите показ. Так текст, картинки и настройки останутся в CMS.</span>
        </div>
        HTML;
    }

    private function overviewHtml(): string
    {
        $item = $this->getResource()->getItem();
        $page = $item?->page;
        $pageTitle = e(html_entity_decode($page?->fieldRu('title') ?: $page?->title ?: 'Страница еще не выбрана'));
        $path = ! $page
            ? 'страница выбирается в форме'
            : ($page->slug === 'home' ? 'Главная страница сайта' : '/'.e(ltrim((string) $page->slug, '/')));
        $type = e(html_entity_decode(CmsFieldSets::pageBlockTypeLabel($item?->type)));
        $title = e(html_entity_decode($item?->fieldRu('title') ?: $item?->title ?: 'Новый блок страницы'));
        $updatedAt = $item?->updated_at?->format('d.m.Y, H:i') ?? 'еще не сохранялся';

        return <<<HTML
        <section class="page-block-form-overview">
            <div class="page-block-form-overview__intro">
                <div class="page-block-overview__eyebrow">Редактирование блока</div>
                <h1>{$title}</h1>
                <p><strong>{$pageTitle}</strong> · <code>{$path}</code> · {$type}</p>
                <div class="page-block-form-overview__saved">Последнее сохранение: {$updatedAt}</div>
            </div>
            <div class="page-block-form-overview__steps">
                {$this->guideCard('document-text', 'Страница', 'Выберите, где должен показываться блок. Для hero сайта это "Главная".')}
                {$this->guideCard('pencil-square', 'Текст', 'Меняйте подпись, заголовок, описание и надпись на кнопке.')}
                {$this->guideCard('check-circle', 'Показ', 'Оставьте блок включенным, чтобы посетители увидели изменения.')}
            </div>
        </section>
        HTML;
    }

    private function guideCard(string $icon, string $title, string $text): string
    {
        $iconHtml = (string) Icon::make($icon, 6)->render();

        return sprintf(
            '<div class="page-block-guide"><span class="page-block-guide__icon">%s</span><strong>%s</strong><span>%s</span></div>',
            $iconHtml,
            e($title),
            e($text),
        );
    }
}
