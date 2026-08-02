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
        return [
            FlexibleRender::make($this->overviewHtml()),
            Alert::make('information-circle', 'info')
                ->content('Для первого экрана главной выберите страницу "Главная", вид блока "Первый экран" и заполните подпись, большой заголовок, описание и кнопку. Остальные поля можно не трогать.'),
            Tabs::make([
                Tab::make('Где блок', [
                    Box::make('Страница и тип секции', [
                        $this->sectionNote(
                            'Блок всегда принадлежит одной странице',
                            'Выберите страницу, тип блока и не смешивайте разные задачи в одной секции: hero отдельно, галерея отдельно, CTA отдельно.'
                        ),
                        Grid::make([
                            Column::make(CmsFieldSets::pageBlockSection('target'))->columnSpan(6),
                            Column::make([
                                FlexibleRender::make($this->targetGuideHtml()),
                            ])->columnSpan(6),
                        ]),
                    ])->icon('squares-2x2')->customAttributes(['class' => 'page-block-section']),
                ])->icon('squares-2x2')->active(),
                Tab::make('Текст', [
                    Box::make('Что увидит посетитель', [
                        $this->sectionNote(
                            'Поля повторяют первый экран сайта',
                            'Маленькая строка идет над заголовком, затем большой заголовок, описание и кнопка. Пишите коротко: этот блок должен быстро читаться.'
                        ),
                        Grid::make([
                            Column::make(array_slice(CmsFieldSets::pageBlockSection('content'), 0, 4))->columnSpan(6),
                            Column::make(array_slice(CmsFieldSets::pageBlockSection('content'), 4))->columnSpan(6),
                        ]),
                    ])->icon('pencil-square')->customAttributes(['class' => 'page-block-section']),
                ])->icon('document-text'),
                Tab::make('Медиа', [
                    Box::make('Картинки, галереи и слайды', [
                        $this->sectionNote(
                            'Слайды - это строки',
                            'Добавляйте URL или путь из хранилища по одному в строке. В галерее все строки станут отдельными изображениями, в hero - слайдами фона.'
                        ),
                        Grid::make([
                            Column::make(array_slice(CmsFieldSets::pageBlockSection('media'), 0, 1))->columnSpan(7),
                            Column::make(array_slice(CmsFieldSets::pageBlockSection('media'), 1))->columnSpan(5),
                        ]),
                    ])->icon('photo')->customAttributes(['class' => 'page-block-section']),
                ])->icon('photo'),
                Tab::make('Кнопка', [
                    Box::make('Ссылка блока', [
                        $this->sectionNote(
                            'Сначала проверьте кнопку',
                            'Для кнопки главной обычно достаточно текста и адреса /kontakty. Если кнопка не нужна, оставьте текст кнопки пустым.'
                        ),
                        Grid::make([
                            Column::make(CmsFieldSets::pageBlockSection('action'))->columnSpan(7),
                            Column::make([
                                FlexibleRender::make($this->buttonGuideHtml()),
                            ])->columnSpan(5),
                        ]),
                    ])->icon('link')->customAttributes(['class' => 'page-block-section']),
                ])->icon('link'),
                Tab::make('Поведение', [
                    Box::make('Визуальный режим и motion', [
                        $this->sectionNote(
                            'Настройки без правки кода',
                            'Здесь меняются акцентность блока, положение медиа, подпись motion-карточки и технические JSON-настройки для редких случаев.'
                        ),
                        Grid::make([
                            Column::make(array_slice(CmsFieldSets::pageBlockSection('behavior'), 0, 4))->columnSpan(6),
                            Column::make(array_slice(CmsFieldSets::pageBlockSection('behavior'), 4))->columnSpan(6),
                        ]),
                    ])->icon('sparkles')->customAttributes(['class' => 'page-block-section']),
                ])->icon('sparkles'),
                Tab::make('Публикация', [
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
                ])->icon('check-circle'),
            ])->vertical()->customAttributes(['class' => 'page-block-tabs']),
        ];
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
        $pageTitle = e($page?->fieldRu('title') ?: $page?->title ?: 'Страница еще не выбрана');
        $path = ! $page
            ? 'страница выбирается в форме'
            : ($page->slug === 'home' ? 'Главная страница сайта' : '/'.e(ltrim((string) $page->slug, '/')));
        $type = e(CmsFieldSets::pageBlockTypeLabel($item?->type));
        $title = e($item?->fieldRu('title') ?: $item?->title ?: 'Новый блок страницы');
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
