<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\NewsArticle\Pages;

use App\MoonShine\Resources\NewsArticle\NewsArticleResource;
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
 * @extends FormPage<NewsArticleResource>
 */
class NewsArticleFormPage extends FormPage
{
    /**
     * @return list<AssetElementContract>
     */
    protected function assets(): array
    {
        return [
            ...parent::assets(),
            InlineCss::make((string) file_get_contents(resource_path('css/news-admin.css'))),
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
                ->content('Новость теперь редактируется как полноценная карточка: заголовок, дата, анонс, тело статьи, обложка, слайды и публикация разложены по вкладкам.'),
            Tabs::make([
                Tab::make('Основное', [
                    Box::make('Название, адрес и категория', [
                        $this->sectionNote(
                            'Заполните основу материала',
                            'Slug можно собрать автоматически из заголовка или задать вручную для SEO. Категория нужна для бейджа в карточках.'
                        ),
                        Grid::make([
                            Column::make([
                                ...array_slice(CmsFieldSets::newsArticleSection('main'), 0, 3),
                                $this->slugAutofillControl(),
                            ])->columnSpan(7),
                            Column::make(array_slice(CmsFieldSets::newsArticleSection('main'), 3))->columnSpan(5),
                        ]),
                    ])->icon('pencil-square')->customAttributes(['class' => 'news-form-section']),
                ])->icon('document-text')->active(),
                Tab::make('Дата', [
                    Box::make('Публикационная дата и чтение', [
                        $this->sectionNote(
                            'Разделите дату для сортировки и дату для показа',
                            'Календарная дата нужна системе, а текстовая дата позволяет показать "20 августа 2026" ровно так, как нужно в дизайне.'
                        ),
                        Grid::make([
                            Column::make(array_slice(CmsFieldSets::newsArticleSection('date'), 0, 3))->columnSpan(6),
                            Column::make(array_slice(CmsFieldSets::newsArticleSection('date'), 3))->columnSpan(6),
                        ]),
                    ])->icon('calendar-days')->customAttributes(['class' => 'news-form-section']),
                ])->icon('calendar-days'),
                Tab::make('Текст', [
                    Box::make('Анонс и тело статьи', [
                        $this->sectionNote(
                            'Пишите без лишней разметки',
                            'Анонс идет в карточки и начало статьи. В теле статьи пустая строка отделяет абзацы.'
                        ),
                        Grid::make([
                            Column::make(array_slice(CmsFieldSets::newsArticleSection('content'), 0, 2))->columnSpan(5),
                            Column::make(array_slice(CmsFieldSets::newsArticleSection('content'), 2))->columnSpan(7),
                        ]),
                    ])->icon('bars-3-bottom-left')->customAttributes(['class' => 'news-form-section']),
                ])->icon('bars-3-bottom-left'),
                Tab::make('Медиа и слайдер', [
                    Box::make('Обложка и карусель новости', [
                        $this->sectionNote(
                            'Слайды задаются строками',
                            'Главная обложка используется в карточках. Дополнительные слайды можно добавить URL-ами или путями из хранилища по одному в строке.'
                        ),
                        Grid::make([
                            Column::make(array_slice(CmsFieldSets::newsArticleSection('media'), 0, 2))->columnSpan(6),
                            Column::make(array_slice(CmsFieldSets::newsArticleSection('media'), 2))->columnSpan(6),
                        ]),
                    ])->icon('photo')->customAttributes(['class' => 'news-form-section']),
                ])->icon('photo'),
                Tab::make('Публикация', [
                    Box::make('Показ на сайте', [
                        $this->sectionNote(
                            'Черновик лучше скрывать, а не удалять',
                            'Позиция управляет порядком в списке материалов. Выключите публикацию, если материал еще не согласован.'
                        ),
                        Grid::make([
                            Column::make(CmsFieldSets::newsArticleSection('publish'))->columnSpan(5),
                            Column::make([
                                FlexibleRender::make($this->publishGuideHtml()),
                            ])->columnSpan(7),
                        ]),
                    ])->icon('check-circle')->customAttributes(['class' => 'news-form-section']),
                ])->icon('check-circle'),
            ])->vertical()->customAttributes(['class' => 'news-form-tabs']),
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
                Rule::unique('news_articles', 'slug')->ignore($item->getKey()),
            ],
            'category_ru' => ['nullable', 'string', 'max:255'],
            'category_en' => ['nullable', 'string', 'max:255'],
            'date_iso' => ['nullable', 'date'],
            'date_ru' => ['nullable', 'string', 'max:255'],
            'date_en' => ['nullable', 'string', 'max:255'],
            'reading_time_ru' => ['nullable', 'string', 'max:255'],
            'reading_time_en' => ['nullable', 'string', 'max:255'],
            'preview_ru' => ['nullable', 'string', 'max:2000'],
            'preview_en' => ['nullable', 'string', 'max:2000'],
            'body_ru' => ['nullable', 'string'],
            'body_en' => ['nullable', 'string'],
            'image' => ['nullable', 'string', 'max:2048'],
            'hero_images' => ['nullable', 'string', 'max:10000'],
            'position' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }

    public function validationMessages(): array
    {
        return [
            'title_ru.required' => 'Напишите русский заголовок новости.',
            'title_ru.max' => 'Заголовок новости RU должен быть короче 255 символов.',
            'title_en.max' => 'Заголовок новости EN должен быть короче 255 символов.',
            'slug.required' => 'Проверьте адрес новости. Он нужен для ссылки на сайте.',
            'slug.unique' => 'Такой адрес уже занят другой новостью. Измените slug.',
            'slug.regex' => 'Slug новости должен состоять из латинских букв, цифр и дефисов. Например: novyj-proekt-studii.',
            'category_ru.max' => 'Категория RU должна быть короче 255 символов.',
            'date_iso.date' => 'Дата для сортировки должна быть корректной календарной датой.',
            'preview_ru.max' => 'Анонс RU слишком длинный. Оставьте до 2000 символов.',
            'preview_en.max' => 'Анонс EN слишком длинный. Оставьте до 2000 символов.',
            'image.max' => 'URL обложки слишком длинный.',
            'hero_images.max' => 'Список слайдов слишком большой. Оставьте только нужные изображения.',
            'position.required' => 'Укажите порядок новости.',
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
                <div class="page-slug-autofill__hint">Включено: адрес обновляется из заголовка. Снимите галочку, если нужен ручной SEO-адрес.</div>
            </div>
        HTML);
    }

    private function sectionNote(string $title, string $text): ComponentContract
    {
        return FlexibleRender::make(sprintf(
            '<div class="news-form-note"><strong>%s</strong><span>%s</span></div>',
            e($title),
            e($text),
        ));
    }

    private function overviewHtml(): string
    {
        $item = $this->getResource()->getItem();
        $title = e($item?->fieldRu('title') ?: 'Новая новость');
        $slug = filled($item?->slug) ? '/novosti/'.e($item->slug) : 'slug появится после заполнения';
        $category = e($item?->fieldRu('category') ?: 'категория не указана');
        $date = e($item?->fieldRu('date') ?: $item?->date_iso?->format('d.m.Y') ?: 'дата не указана');
        $updatedAt = $item?->updated_at?->format('d.m.Y, H:i') ?? 'еще не сохранялась';

        return <<<HTML
        <section class="news-form-overview">
            <div class="news-form-overview__intro">
                <div class="news-form-overview__eyebrow">Редактирование новости</div>
                <h1>{$title}</h1>
                <p><code>{$slug}</code> · {$category} · {$date}</p>
                <div class="news-form-overview__saved">Последнее сохранение: {$updatedAt}</div>
            </div>
            <div class="news-form-overview__steps">
                {$this->guideCard('document-text', 'Заголовок', 'Материал получает понятный адрес и категорию.')}
                {$this->guideCard('bars-3-bottom-left', 'Текст', 'Анонс и тело статьи редактируются отдельно.')}
                {$this->guideCard('photo', 'Слайдер', 'Обложка и дополнительные изображения управляются из формы.')}
            </div>
        </section>
        HTML;
    }

    private function publishGuideHtml(): string
    {
        return <<<HTML
        <div class="news-publish-guide">
            <strong>Перед публикацией проверьте:</strong>
            <span>заголовок, категорию, дату, анонс, обложку и первый абзац статьи.</span>
            <span>Если заголовок длинный, карточки сайта перенесут его аккуратно и не развалят сетку.</span>
        </div>
        HTML;
    }

    private function guideCard(string $icon, string $title, string $text): string
    {
        $iconHtml = (string) Icon::make($icon, 6)->render();

        return sprintf(
            '<div class="news-form-guide"><span class="news-form-guide__icon">%s</span><strong>%s</strong><span>%s</span></div>',
            $iconHtml,
            e($title),
            e($text),
        );
    }
}
