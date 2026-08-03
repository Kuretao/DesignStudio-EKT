<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Page\Pages;

use App\MoonShine\Resources\Page\PageResource;
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
 * @extends FormPage<PageResource>
 */
class PageFormPage extends FormPage
{
    /**
     * @return list<AssetElementContract>
     */
    protected function assets(): array
    {
        return [
            ...parent::assets(),
            InlineCss::make((string) file_get_contents(resource_path('css/page-admin.css'))),
            InlineJs::make((string) file_get_contents(resource_path('js/page-slug-autofill.js'))),
        ];
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        $mainFields = CmsFieldSets::pageSection('main');

        return [
            FlexibleRender::make($this->overviewHtml()),
            Alert::make('information-circle', 'info')
                ->content('Для обычной страницы оставьте тип "Обычная текстовая страница", заполните редактор и включите пункт меню на вкладке "Публикация и меню", если он нужен.'),
            Tabs::make([
                Tab::make('Текст страницы', [
                    Box::make('Содержание страницы', [
                        $this->sectionNote(
                            'Редактор для обычного текста',
                            'Заголовок и текст достаточно заполнить в этой форме. Блоки страниц нужны только для сложных макетов.'
                        ),
                        Grid::make([
                            Column::make([
                                $mainFields[0],
                                $mainFields[1],
                                $mainFields[2],
                                $this->slugAutofillControl(),
                                $mainFields[3],
                            ])->columnSpan(5),
                            Column::make(array_slice($mainFields, 4))->columnSpan(7),
                        ]),
                    ])->icon('pencil-square')->customAttributes(['class' => 'page-section']),
                ])->icon('document-text')->active(),
                Tab::make('Публикация и меню', [
                    Box::make('Показ на сайте', [
                        $this->sectionNote(
                            'Поиск, публикация и навигация',
                            'SEO-поля можно заполнить позже. Переключатель меню создаст пункт, связанный с этой страницей, без ручной ссылки.'
                        ),
                        Grid::make([
                            Column::make([
                                ...CmsFieldSets::pageSection('publish'),
                                ...CmsFieldSets::pageSection('menu'),
                            ])->columnSpan(5),
                            Column::make(CmsFieldSets::pageSection('seo'))->columnSpan(7),
                        ]),
                    ])->icon('globe-alt')->customAttributes(['class' => 'page-section']),
                ])->icon('bars-3'),
            ])->vertical()->customAttributes(['class' => 'page-tabs']),
        ];
    }

    private function slugAutofillControl(): ComponentContract
    {
        return FlexibleRender::make(<<<'HTML'
            <div class="page-slug-autofill" data-page-slug-autofill-control>
                <label class="page-slug-autofill__label">
                    <input class="page-slug-autofill__checkbox" type="checkbox" checked data-page-slug-autofill-toggle>
                    <span>Автозаполнение</span>
                </label>
                <div class="page-slug-autofill__hint">Включено: адрес обновляется из заголовка. Снимите, чтобы заполнить адрес вручную.</div>
            </div>
        HTML);
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
                'regex:/^[a-z0-9]+(?:[a-z0-9\/-]*[a-z0-9])?$/',
                Rule::unique('pages', 'slug')->ignore($item->getKey()),
            ],
            'template' => ['required', Rule::in(['text', 'content', 'legal', 'about', 'blog'])],
            'body_ru' => ['nullable', 'string', 'required_if:template,text'],
            'body_en' => ['nullable', 'string'],
            'seo_title_ru' => ['nullable', 'string', 'max:120'],
            'seo_title_en' => ['nullable', 'string', 'max:120'],
            'seo_description_ru' => ['nullable', 'string', 'max:320'],
            'seo_description_en' => ['nullable', 'string', 'max:320'],
            'is_published' => ['nullable', 'boolean'],
            'create_menu_item' => ['nullable', 'boolean'],
            'menu_label_ru' => ['nullable', 'string', 'max:255'],
            'menu_label_en' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function validationMessages(): array
    {
        return [
            'title_ru.required' => 'Напишите русский заголовок страницы.',
            'title_ru.max' => 'Заголовок страницы RU должен быть короче 255 символов.',
            'title_en.max' => 'Заголовок страницы EN должен быть короче 255 символов.',
            'body_ru.required_if' => 'Заполните русский текст обычной текстовой страницы.',
            'seo_title_ru.max' => 'SEO-заголовок RU должен быть короче 120 символов.',
            'seo_title_en.max' => 'SEO-заголовок EN должен быть короче 120 символов.',
            'seo_description_ru.max' => 'SEO-описание RU должно быть короче 320 символов.',
            'seo_description_en.max' => 'SEO-описание EN должно быть короче 320 символов.',
            'menu_label_ru.max' => 'Название в меню RU должно быть короче 255 символов.',
            'menu_label_en.max' => 'Название в меню EN должно быть короче 255 символов.',
            'slug.required' => 'Проверьте адрес страницы. Он нужен для ссылки на сайте.',
            'slug.unique' => 'Такой адрес уже занят другой страницей. Измените его.',
            'slug.regex' => 'Адрес страницы должен состоять из латинских букв, цифр, дефисов и при необходимости /. Например: dostavka-i-oplata.',
            'template.required' => 'Выберите тип страницы.',
            'template.in' => 'Выберите тип страницы из списка.',
        ];
    }

    private function sectionNote(string $title, string $text): ComponentContract
    {
        return FlexibleRender::make(sprintf(
            '<div class="page-note"><strong>%s</strong><span>%s</span></div>',
            e($title),
            e($text),
        ));
    }

    private function overviewHtml(): string
    {
        $item = $this->getResource()->getItem();
        $title = e(html_entity_decode($item?->fieldRu('title') ?: $item?->title ?: 'Новая страница'));
        $path = filled($item?->slug) ? '/'.e(ltrim($item->slug, '/')) : 'адрес появится после заполнения';
        $updatedAt = $item?->updated_at?->format('d.m.Y, H:i') ?? 'еще не сохранялась';
        $menuState = $item?->menuItems()->exists() ? 'У страницы уже есть пункт меню.' : 'Пункт меню можно создать при сохранении.';

        return <<<HTML
        <section class="page-form-overview">
            <div class="page-form-overview__intro">
                <div class="page-overview__eyebrow">Редактирование страницы</div>
                <h1>{$title}</h1>
                <p><code>{$path}</code> · {$menuState}</p>
                <div class="page-form-overview__saved">Последнее сохранение: {$updatedAt}</div>
            </div>
            <div class="page-form-overview__steps">
                {$this->guideCard('document-text', 'Текст', 'Напишите страницу в редакторе без создания блоков.')}
                {$this->guideCard('link', 'Адрес', 'Форма предложит ссылку из заголовка страницы.')}
                {$this->guideCard('bars-3', 'Меню', 'Создайте пункт меню тем же сохранением.')}
            </div>
        </section>
        HTML;
    }

    private function guideCard(string $icon, string $title, string $text): string
    {
        $iconHtml = (string) Icon::make($icon, 6)->render();

        return sprintf(
            '<div class="page-guide"><span class="page-guide__icon">%s</span><strong>%s</strong><span>%s</span></div>',
            $iconHtml,
            e($title),
            e($text),
        );
    }
}
