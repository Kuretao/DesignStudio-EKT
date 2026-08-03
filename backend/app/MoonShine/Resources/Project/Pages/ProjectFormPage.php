<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Project\Pages;

use App\MoonShine\Resources\Project\ProjectResource;
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
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;

/**
 * @extends FormPage<ProjectResource>
 */
class ProjectFormPage extends FormPage
{
    /**
     * @return list<AssetElementContract>
     */
    protected function assets(): array
    {
        return [
            ...parent::assets(),
            InlineCss::make((string) file_get_contents(resource_path('css/project-admin.css'))),
            InlineJs::make((string) file_get_contents(resource_path('js/page-slug-autofill.js'))),
        ];
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        $fields = CmsFieldSets::for('project');

        return [
            Alert::make('information-circle', 'info')
                ->content('Проект редактируется по разделам: название и адрес, описание, изображения до/после и публикация. Для изображений можно выбрать файл из галереи или загрузить новый.'),
            Tabs::make([
                Tab::make('Основное', [
                    Box::make('Название, адрес и категория', [
                        $this->sectionNote('Адрес проекта', 'Slug можно заполнить автоматически из русского заголовка или задать вручную для SEO.'),
                        Grid::make([
                            Column::make([
                                ...array_slice($fields, 1, 3),
                                $this->slugAutofillControl(),
                            ])->columnSpan(6),
                            Column::make([
                                ...array_slice($fields, 4, 2),
                                ...array_slice($fields, 8, 3),
                            ])->columnSpan(6),
                        ]),
                    ])->icon('pencil-square')->customAttributes(['class' => 'proj-form-section']),
                ])->icon('document-text')->active(),
                Tab::make('Описание', [
                    Box::make('История проекта', [
                        Grid::make([
                            Column::make(array_slice($fields, 11, 1))->columnSpan(6),
                            Column::make(array_slice($fields, 12, 1))->columnSpan(6),
                        ]),
                    ])->icon('bars-3-bottom-left')->customAttributes(['class' => 'proj-form-section']),
                ])->icon('bars-3-bottom-left'),
                Tab::make('Медиа', [
                    Box::make('Обложка и сравнение до/после', [
                        $this->sectionNote('Изображения из галереи', 'Главное изображение идет в карточки и hero. До/После используются в портфолио и услугах, где есть сравнение.'),
                        Grid::make([
                            Column::make(array_slice($fields, 13, 2))->columnSpan(4),
                            Column::make(array_slice($fields, 15, 2))->columnSpan(4),
                            Column::make(array_slice($fields, 17, 2))->columnSpan(4),
                        ]),
                    ])->icon('photo')->customAttributes(['class' => 'proj-form-section']),
                ])->icon('photo'),
                Tab::make('Галерея кейса', [
                    Box::make('Ракурсы и детали проекта', [
                        $this->sectionNote('Картинки и подписи карточек', 'Первая картинка показывается большой плашкой. Остальные идут маленькими карточками снизу. Подписи пишутся в том же порядке.'),
                        Grid::make([
                            Column::make(array_slice($fields, 28, 3))->columnSpan(6),
                            Column::make(array_slice($fields, 31, 3))->columnSpan(6),
                        ]),
                        Grid::make([
                            Column::make(array_slice($fields, 34, 1))->columnSpan(6),
                            Column::make(array_slice($fields, 35, 2))->columnSpan(6),
                        ]),
                    ])->icon('rectangle-stack')->customAttributes(['class' => 'proj-form-section']),
                ])->icon('rectangle-stack'),
                Tab::make('История и Состав', [
                    Box::make('Контент страницы кейса', [
                        $this->sectionNote('История проекта', 'Раздел "Что важно увидеть в этом кейсе". Если оставить пустым, будет показан текст по умолчанию для категории проекта.'),
                        Grid::make([
                            Column::make(array_slice($fields, 43, 1))->columnSpan(6),
                            Column::make(array_slice($fields, 44, 2))->columnSpan(6),
                        ]),
                    ])->icon('chat-bubble-left-ellipsis')->customAttributes(['class' => 'proj-form-section']),
                ])->icon('chat-bubble-left-ellipsis'),
                Tab::make('Публикация', [
                    Box::make('Показ на сайте', [
                        Grid::make([
                            Column::make([
                                $fields[6],
                                $fields[7],
                                $fields[19],
                            ])->columnSpan(5),
                            Column::make([
                                FlexibleRender::make('<div class="proj-publish-guide"><strong>Проверьте перед сохранением:</strong><span>заголовок, категорию, локацию, год, обложку и изображения до/после, если они нужны.</span></div>'),
                            ])->columnSpan(7),
                        ]),
                    ])->icon('check-circle')->customAttributes(['class' => 'proj-form-section']),
                ])->icon('check-circle'),
                Tab::make('Избранный кейс', [
                    Box::make('Большая структура на главной', [
                        $this->sectionNote('Поля работают только для избранного', 'Включите статус "Избранный" во вкладке публикации. После этого проект попадет в fullscreen-секцию на главной и возьмет эти отдельные подписи, заголовок, описание и фон.'),
                        Grid::make([
                            Column::make(array_slice($fields, 20, 4))->columnSpan(6),
                            Column::make(array_slice($fields, 24, 4))->columnSpan(6),
                        ]),
                        Grid::make([
                            Column::make(array_slice($fields, 37, 1))->columnSpan(12),
                        ]),
                    ])->icon('star')->customAttributes(['class' => 'proj-form-section']),
                ])->icon('star'),
            ])->vertical()->customAttributes(['class' => 'proj-form-tabs']),
        ];
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:[a-z0-9-]*[a-z0-9])?$/',
                Rule::unique('projects', 'slug')->ignore($item->getKey()),
            ],
            'title_ru' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'category_ru' => ['required', 'string', 'max:255'],
            'category_en' => ['nullable', 'string', 'max:255'],
            'location_ru' => ['nullable', 'string', 'max:255'],
            'location_en' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'string', 'max:32'],
            'description_ru' => ['nullable', 'string', 'max:10000'],
            'description_en' => ['nullable', 'string', 'max:10000'],
            'image' => ['nullable', 'string', 'max:2048'],
            'before_image' => ['nullable', 'string', 'max:2048'],
            'after_image' => ['nullable', 'string', 'max:2048'],
            'gallery_eyebrow_ru' => ['nullable', 'string', 'max:255'],
            'gallery_eyebrow_en' => ['nullable', 'string', 'max:255'],
            'gallery_title_ru' => ['nullable', 'string', 'max:255'],
            'gallery_title_en' => ['nullable', 'string', 'max:255'],
            'gallery_text_ru' => ['nullable', 'string', 'max:10000'],
            'gallery_text_en' => ['nullable', 'string', 'max:10000'],
            'gallery_images' => ['nullable', 'string', 'max:20000'],
            'gallery_labels_ru' => ['nullable', 'string', 'max:10000'],
            'gallery_labels_en' => ['nullable', 'string', 'max:10000'],
            'featured_label_ru' => ['nullable', 'string', 'max:255'],
            'featured_label_en' => ['nullable', 'string', 'max:255'],
            'featured_title_ru' => ['nullable', 'string', 'max:255'],
            'featured_title_en' => ['nullable', 'string', 'max:255'],
            'featured_description_ru' => ['nullable', 'string', 'max:10000'],
            'featured_description_en' => ['nullable', 'string', 'max:10000'],
            'featured_image' => ['nullable', 'string', 'max:2048'],
            'featured_gallery_images' => ['nullable', 'string', 'max:20000'],
            'story_chapters' => ['nullable', 'json'],
            'deliverables_ru' => ['nullable', 'string', 'max:10000'],
            'deliverables_en' => ['nullable', 'string', 'max:10000'],
            'position' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_featured' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }

    public function validationMessages(): array
    {
        return [
            'slug.required' => 'Проверьте адрес проекта. Он нужен для ссылки на сайте.',
            'slug.unique' => 'Такой адрес уже занят другим проектом.',
            'slug.regex' => 'Slug проекта должен состоять из латинских букв, цифр и дефисов. Например: zhk-river-park.',
            'title_ru.required' => 'Напишите русский заголовок проекта.',
            'category_ru.required' => 'Укажите русскую категорию проекта.',
            'image.max' => 'URL главного изображения слишком длинный.',
            'before_image.max' => 'URL изображения "До" слишком длинный.',
            'after_image.max' => 'URL изображения "После" слишком длинный.',
            'position.required' => 'Укажите порядок проекта.',
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
                <div class="page-slug-autofill__hint">Включено: адрес обновляется из заголовка. Снимите галочку, чтобы задать адрес вручную.</div>
            </div>
        HTML);
    }

    private function sectionNote(string $title, string $text): ComponentContract
    {
        return FlexibleRender::make(sprintf(
            '<div class="proj-form-note"><strong>%s</strong><span>%s</span></div>',
            e(html_entity_decode($title)),
            e(html_entity_decode($text)),
        ));
    }
}
