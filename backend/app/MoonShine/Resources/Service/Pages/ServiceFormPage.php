<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Service\Pages;

use App\MoonShine\Resources\Service\ServiceResource;
use App\MoonShine\Support\CmsFieldSets;
use Illuminate\Validation\Rule;
use MoonShine\AssetManager\InlineCss;
use MoonShine\AssetManager\InlineJs;
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
 * @extends FormPage<ServiceResource>
 */
class ServiceFormPage extends FormPage
{
    /**
     * @return list<AssetElementContract>
     */
    protected function assets(): array
    {
        return [
            ...parent::assets(),
            InlineCss::make((string) file_get_contents(resource_path('css/service-admin.css'))),
            InlineJs::make((string) file_get_contents(resource_path('js/page-slug-autofill.js'))),
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
                ->content('Создание услуги устроено как карточка товара: сначала название и адрес, затем цена, контент, слайды, списки и публикация. Все поля можно менять отдельно, без ручного поиска по длинной форме.'),
            Tabs::make([
                Tab::make('Основное', [
                    Box::make('Название, адрес и краткая подпись', [
                        $this->sectionNote(
                            'Начните с заголовка',
                            'Slug может заполниться автоматически из русского заголовка. Если адрес уже продуман под SEO, выключите автозаполнение и впишите его вручную.'
                        ),
                        Grid::make([
                            Column::make([
                                ...array_slice(CmsFieldSets::serviceSection('main'), 0, 3),
                                $this->slugAutofillControl(),
                            ])->columnSpan(6),
                            Column::make(array_slice(CmsFieldSets::serviceSection('main'), 3))->columnSpan(6),
                        ]),
                    ])->icon('pencil-square')->customAttributes(['class' => 'svc-form-section']),
                ])->icon('document-text')->active(),
                Tab::make('Цена и сроки', [
                    Box::make('Коммерческие подписи', [
                        $this->sectionNote(
                            'Цена хранится как текст',
                            'Можно писать "от 2 500 ₽/м²", "по запросу" или любую формулировку. При сохранении RU-цена синхронизируется со старым полем, чтобы сайт точно увидел изменение.'
                        ),
                        Grid::make([
                            Column::make(array_slice(CmsFieldSets::serviceSection('pricing'), 0, 2))->columnSpan(6),
                            Column::make(array_slice(CmsFieldSets::serviceSection('pricing'), 2))->columnSpan(6),
                        ]),
                    ])->icon('banknotes')->customAttributes(['class' => 'svc-form-section']),
                ])->icon('banknotes'),
                Tab::make('Описание', [
                    Box::make('Текст посадочной страницы', [
                        $this->sectionNote(
                            'Основной смысл услуги',
                            'Этот текст используется в карточках и на детальной странице. Пишите спокойно: абзацы и выделения сохраняются.'
                        ),
                        Grid::make([
                            Column::make(array_slice(CmsFieldSets::serviceSection('content'), 0, 1))->columnSpan(6),
                            Column::make(array_slice(CmsFieldSets::serviceSection('content'), 1))->columnSpan(6),
                        ]),
                    ])->icon('bars-3-bottom-left')->customAttributes(['class' => 'svc-form-section']),
                ])->icon('bars-3-bottom-left'),
                Tab::make('Медиа и слайдер', [
                    Box::make('Обложка и карусель', [
                        $this->sectionNote(
                            'Слайды задаются строками',
                            'Загрузите главную обложку или вставьте URL. Для карусели добавляйте дополнительные изображения по одному пути в строке.'
                        ),
                        Grid::make([
                            Column::make(array_slice(CmsFieldSets::serviceSection('media'), 0, 2))->columnSpan(6),
                            Column::make(array_slice(CmsFieldSets::serviceSection('media'), 2))->columnSpan(6),
                        ]),
                    ])->icon('photo')->customAttributes(['class' => 'svc-form-section']),
                ])->icon('photo'),
                Tab::make('Списки', [
                    Box::make('Что входит, преимущества и этапы', [
                        $this->sectionNote(
                            'Каждая строка станет отдельным пунктом',
                            'Так удобнее редактировать состав услуги, карточки документов и процесс без JSON и лишней разметки.'
                        ),
                        Grid::make([
                            Column::make(array_slice(CmsFieldSets::serviceSection('lists'), 0, 2))->columnSpan(4),
                            Column::make(array_slice(CmsFieldSets::serviceSection('lists'), 2, 2))->columnSpan(4),
                            Column::make(array_slice(CmsFieldSets::serviceSection('lists'), 4))->columnSpan(4),
                        ]),
                    ])->icon('list-bullet')->customAttributes(['class' => 'svc-form-section']),
                ])->icon('list-bullet'),
                Tab::make('Публикация', [
                    Box::make('Показ на сайте', [
                        $this->sectionNote(
                            'Порядок и видимость',
                            'Оставьте черновиком, если услуга еще не готова. Позицию удобно задавать десятками, чтобы потом вставлять новые услуги между существующими.'
                        ),
                        Grid::make([
                            Column::make(CmsFieldSets::serviceSection('publish'))->columnSpan(5),
                            Column::make([
                                FlexibleRender::make($this->publishGuideHtml()),
                            ])->columnSpan(7),
                        ]),
                    ])->icon('check-circle')->customAttributes(['class' => 'svc-form-section']),
                ])->icon('check-circle'),
            ])->vertical()->customAttributes(['class' => 'svc-form-tabs']),
        ];
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'title_ru' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:[a-z0-9-]*[a-z0-9])?$/',
                Rule::unique('services', 'slug')->ignore($item->getKey()),
            ],
            'eyebrow_ru' => ['nullable', 'string', 'max:255'],
            'eyebrow_en' => ['nullable', 'string', 'max:255'],
            'price_ru' => ['nullable', 'string', 'max:255'],
            'price_en' => ['nullable', 'string', 'max:255'],
            'timeline_ru' => ['nullable', 'string', 'max:255'],
            'timeline_en' => ['nullable', 'string', 'max:255'],
            'text_ru' => ['nullable', 'string'],
            'text_en' => ['nullable', 'string'],
            'image' => ['nullable', 'string', 'max:2048'],
            'hero_images' => ['nullable', 'string', 'max:10000'],
            'deliverables_ru' => ['nullable', 'string', 'max:10000'],
            'deliverables_en' => ['nullable', 'string', 'max:10000'],
            'benefits_ru' => ['nullable', 'string', 'max:10000'],
            'benefits_en' => ['nullable', 'string', 'max:10000'],
            'process_ru' => ['nullable', 'string', 'max:10000'],
            'process_en' => ['nullable', 'string', 'max:10000'],
            'position' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }

    public function validationMessages(): array
    {
        return [
            'title_ru.required' => 'Напишите русский заголовок услуги.',
            'title_ru.max' => 'Заголовок услуги RU должен быть короче 255 символов.',
            'title_en.max' => 'Заголовок услуги EN должен быть короче 255 символов.',
            'slug.required' => 'Проверьте адрес услуги. Он нужен для ссылки на сайте.',
            'slug.unique' => 'Такой адрес уже занят другой услугой. Измените slug.',
            'slug.regex' => 'Slug услуги должен состоять из латинских букв, цифр и дефисов. Например: dizajn-interyera.',
            'price_ru.max' => 'Цена RU должна быть короче 255 символов.',
            'price_en.max' => 'Цена EN должна быть короче 255 символов.',
            'timeline_ru.max' => 'Срок RU должен быть короче 255 символов.',
            'timeline_en.max' => 'Срок EN должен быть короче 255 символов.',
            'image.max' => 'URL обложки слишком длинный.',
            'hero_images.max' => 'Список слайдов слишком большой. Оставьте только нужные изображения.',
            'position.required' => 'Укажите порядок услуги.',
            'position.integer' => 'Порядок должен быть целым числом.',
            'position.min' => 'Порядок не может быть отрицательным.',
            'position.max' => 'Порядок должен быть меньше 10000.',
        ];
    }

    private function slugAutofillControl(): ComponentContract
    {
        return FlexibleRender::make(<<<'HTML'
            <div class="page-slug-autofill" data-page-slug-autofill-control>
                <label class="page-slug-autofill__label">
                    <input class="page-slug-autofill__checkbox" type="checkbox" checked data-page-slug-autofill-toggle>
                    <span>Автозаполнение slug</span>
                </label>
                <div class="page-slug-autofill__hint">Включено: адрес обновляется из заголовка. Снимите галочку, чтобы задать SEO-адрес вручную.</div>
            </div>
        HTML);
    }

    private function sectionNote(string $title, string $text): ComponentContract
    {
        return FlexibleRender::make(sprintf(
            '<div class="svc-form-note"><strong>%s</strong><span>%s</span></div>',
            e($title),
            e($text),
        ));
    }

    private function overviewHtml(): string
    {
        $item = $this->getResource()->getItem();
        $title = e($item?->fieldRu('title') ?: 'Новая услуга');
        $slug = filled($item?->slug) ? '/'.e($item->slug) : 'slug появится после заполнения';
        $price = e($item?->fieldRu('price') ?: 'цена не указана');
        $timeline = e($item?->fieldRu('timeline') ?: 'срок не указан');
        $updatedAt = $item?->updated_at?->format('d.m.Y, H:i') ?? 'еще не сохранялась';

        return <<<HTML
        <section class="svc-form-overview">
            <div class="svc-form-overview__intro">
                <div class="svc-form-overview__eyebrow">Редактирование услуги</div>
                <h1>{$title}</h1>
                <p><code>{$slug}</code> · {$price} · {$timeline}</p>
                <div class="svc-form-overview__saved">Последнее сохранение: {$updatedAt}</div>
            </div>
            <div class="svc-form-overview__steps">
                {$this->guideCard('document-text', 'Контент', 'Заголовок, описание и списки услуги разнесены по вкладкам.')}
                {$this->guideCard('banknotes', 'Цена', 'Стоимость редактируется текстом и синхронизируется со старым полем.')}
                {$this->guideCard('photo', 'Слайдер', 'Главная обложка и дополнительные слайды управляются из CMS.')}
            </div>
        </section>
        HTML;
    }

    private function publishGuideHtml(): string
    {
        return <<<HTML
        <div class="svc-publish-guide">
            <strong>Перед публикацией проверьте:</strong>
            <span>есть русский заголовок, понятная цена, срок, обложка и хотя бы 3 пункта в списке "Что входит".</span>
            <span>Длинные заголовки можно оставлять: фронт теперь переносит их без разъезда карточек.</span>
        </div>
        HTML;
    }

    private function guideCard(string $icon, string $title, string $text): string
    {
        $iconHtml = (string) Icon::make($icon, 6)->render();

        return sprintf(
            '<div class="svc-form-guide"><span class="svc-form-guide__icon">%s</span><strong>%s</strong><span>%s</span></div>',
            $iconHtml,
            e($title),
            e($text),
        );
    }
}
