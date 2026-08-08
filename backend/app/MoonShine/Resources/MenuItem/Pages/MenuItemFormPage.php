<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\MenuItem\Pages;

use App\Models\MenuItem;
use App\MoonShine\Resources\MenuItem\MenuItemResource;
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
 * @extends FormPage<MenuItemResource>
 */
class MenuItemFormPage extends FormPage
{
    /**
     * @return list<AssetElementContract>
     */
    protected function assets(): array
    {
        return [
            ...parent::assets(),
            InlineCss::make((string) file_get_contents(resource_path('css/menu-admin.css'))),
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
                ->content('Основной сценарий: выберите страницу сайта. Поле "Другая ссылка" нужно только для внешних сайтов, якорей и готовых разделов, которых нет в разделе "Страницы".'),
            Tabs::make([
                Tab::make('Где показывать', [
                    Box::make('Место и уровень пункта', [
                        $this->sectionNote(
                            'Структуру услуг собирайте как дерево',
                            'Верхний раздел оставьте без родителя. Для подпункта выберите родительский раздел услуг. Описание нужно только верхним разделам и показывается на странице "Услуги".'
                        ),
                        Grid::make([
                            Column::make(array_slice(CmsFieldSets::menuItemSection('placement'), 0, 2))->columnSpan(6),
                            Column::make(array_slice(CmsFieldSets::menuItemSection('placement'), 2))->columnSpan(6),
                        ]),
                    ])->icon('squares-2x2')->customAttributes(['class' => 'menu-section']),
                ])->icon('squares-2x2')->active(),
                Tab::make('Куда ведет', [
                    Box::make('Привязка пункта меню', [
                        $this->sectionNote(
                            'Проще всего выбрать страницу',
                            'После выбора страницы адрес в меню будет собран автоматически из ее slug. Если страница скрыта от публикации, пункт не попадет в меню сайта.'
                        ),
                        Grid::make([
                            Column::make(array_slice(CmsFieldSets::menuItemSection('target'), 0, 1))->columnSpan(7),
                            Column::make(array_slice(CmsFieldSets::menuItemSection('target'), 1))->columnSpan(5),
                        ]),
                    ])->icon('link')->customAttributes(['class' => 'menu-section']),
                ])->icon('document-text'),
                Tab::make('Название и показ', [
                    Box::make('Как пункт выглядит в меню', [
                        $this->sectionNote(
                            'Название, порядок и видимость',
                            'Название должно быть коротким. Если пункт пока не нужен посетителям, выключите показ вместо удаления.'
                        ),
                        Grid::make([
                            Column::make(CmsFieldSets::menuItemSection('text'))->columnSpan(7),
                            Column::make(CmsFieldSets::menuItemSection('display'))->columnSpan(5),
                        ]),
                    ])->icon('bars-3')->customAttributes(['class' => 'menu-section']),
                ])->icon('bars-3'),
                Tab::make('Картинка направления', [
                    Box::make('Медиа для карточки направления', [
                        $this->sectionNote(
                            'Используется только у верхнего раздела услуг',
                            'Если этот пункт - направление услуг без родителя, картинка попадет на карточку страницы "Услуги". Для обычных пунктов меню и подпунктов можно оставить пусто.'
                        ),
                        Grid::make([
                            Column::make(array_slice(CmsFieldSets::menuItemSection('media'), 0, 2))->columnSpan(6),
                            Column::make(array_slice(CmsFieldSets::menuItemSection('media'), 2))->columnSpan(6),
                        ]),
                    ])->icon('photo')->customAttributes(['class' => 'menu-section']),
                ])->icon('photo'),
            ])->vertical()->customAttributes(['class' => 'menu-tabs']),
        ];
    }

    protected function rules(DataWrapperContract $item): array
    {
        $original = $item->getOriginal();
        $currentId = $original instanceof MenuItem ? $original->id : null;
        $notCurrent = $currentId === null ? [] : [Rule::notIn([$currentId])];

        return [
            'menu_area' => ['required', 'string', Rule::in([MenuItem::AREA_MAIN, MenuItem::AREA_SERVICES])],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:menu_items,id',
                ...$notCurrent,
                static function (string $attribute, mixed $value, \Closure $fail): void {
                    if (blank($value)) {
                        return;
                    }

                    if (request()->input('menu_area') !== MenuItem::AREA_SERVICES) {
                        $fail('Подпункт можно добавить только в структуру услуг.');

                        return;
                    }

                    $parent = MenuItem::query()->find($value);

                    if ($parent === null || $parent->menu_area !== MenuItem::AREA_SERVICES || $parent->parent_id !== null) {
                        $fail('Выберите верхний раздел услуг как родителя.');
                    }
                },
            ],
            'label_ru' => ['required', 'string', 'max:255'],
            'label_en' => ['nullable', 'string', 'max:255'],
            'page_id' => ['nullable', 'integer', 'exists:pages,id'],
            'href' => [
                'nullable',
                'string',
                'max:2048',
                'regex:/^(\/(?!\/)|#|https?:\/\/|mailto:|tel:)/i',
                static function (string $attribute, mixed $value, \Closure $fail): void {
                    $isTopServiceDirection = request()->input('menu_area') === MenuItem::AREA_SERVICES
                        && blank(request()->input('parent_id'));

                    if ($isTopServiceDirection) {
                        return;
                    }

                    if (blank($value) && blank(request()->input('page_id'))) {
                        $fail('Выберите страницу или укажите ссылку. Для верхнего направления услуг ссылку можно оставить пустой.');
                    }
                },
            ],
            'description_ru' => ['nullable', 'string', 'max:2000'],
            'description_en' => ['nullable', 'string', 'max:2000'],
            'image_file' => ['nullable'],
            'image' => ['nullable', 'string', 'max:2048'],
            'image_alt_ru' => ['nullable', 'string', 'max:255'],
            'image_alt_en' => ['nullable', 'string', 'max:255'],
            'position' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function validationMessages(): array
    {
        return [
            'menu_area.required' => 'Выберите, где показывать пункт.',
            'menu_area.in' => 'Выберите один из доступных разделов меню.',
            'parent_id.exists' => 'Выбранный родительский раздел больше недоступен.',
            'parent_id.not_in' => 'Пункт не может быть родителем самому себе.',
            'label_ru.required' => 'Напишите русское название пункта меню или направления.',
            'label_ru.max' => 'Русское название пункта меню должно быть короче 255 символов.',
            'label_en.max' => 'Английское название пункта меню должно быть короче 255 символов.',
            'page_id.exists' => 'Выбранная страница больше недоступна. Выберите ее заново.',
            'href.regex' => 'Другая ссылка должна начинаться с /, #, http://, https://, mailto: или tel:.',
            'href.max' => 'Ссылка слишком длинная. Проверьте, что вставлен именно адрес.',
            'description_ru.max' => 'Русское описание раздела услуг должно быть короче 2000 символов.',
            'description_en.max' => 'Английское описание раздела услуг должно быть короче 2000 символов.',
            'image.max' => 'URL картинки направления слишком длинный.',
            'image_alt_ru.max' => 'Alt картинки RU должен быть короче 255 символов.',
            'image_alt_en.max' => 'Alt картинки EN должен быть короче 255 символов.',
            'position.required' => 'Укажите порядок пункта в меню.',
            'position.integer' => 'Порядок должен быть целым числом.',
            'position.min' => 'Порядок не может быть отрицательным.',
            'position.max' => 'Порядок должен быть меньше 10000.',
        ];
    }

    private function sectionNote(string $title, string $text): ComponentContract
    {
        return FlexibleRender::make(sprintf(
            '<div class="menu-note"><strong>%s</strong><span>%s</span></div>',
            e($title),
            e($text),
        ));
    }

    private function overviewHtml(): string
    {
        $item = $this->getResource()->getItem();
        $label = e(html_entity_decode($item?->labelRu() ?: 'Новый пункт меню'));
        $updatedAt = $item?->updated_at?->format('d.m.Y, H:i') ?? 'еще не сохранялся';
        $target = 'Сначала выберите страницу или ссылку.';
        $area = html_entity_decode(CmsFieldSets::menuAreaLabel($item?->menu_area));
        $level = $item?->parent_id === null
            ? 'Верхний раздел'
            : 'Подпункт раздела: ' . e(html_entity_decode($item->parent?->labelRu() ?? 'родитель не найден'));

        if ($item?->page_id !== null) {
            $target = $item->page?->fieldRu('title')
                ? 'Привязан к странице: ' . e(html_entity_decode($item->page->fieldRu('title')))
                : 'Страница больше не найдена.';
        } elseif (filled($item?->href)) {
            $target = 'Своя ссылка: ' . e($item->href);
        }

        return <<<HTML
        <section class="menu-form-overview">
            <div class="menu-form-overview__intro">
                <div class="menu-overview__eyebrow">Редактирование меню</div>
                <h1>{$label}</h1>
                <p>{$target}</p>
                <div class="menu-form-overview__meta">
                    <span>{$area}</span>
                    <span>{$level}</span>
                    <span>Последнее сохранение: {$updatedAt}</span>
                </div>
            </div>
            <div class="menu-form-overview__steps">
                {$this->guideCard('squares-2x2', 'Место', 'Выберите обычное меню или структуру услуг.')}
                {$this->guideCard('link', 'Ссылка', 'Привяжите страницу или укажите готовый адрес.')}
                {$this->guideCard('arrows-up-down', 'Порядок', 'Меньшее число ставит пункт выше в своем списке.')}
            </div>
        </section>
        HTML;
    }

    private function guideCard(string $icon, string $title, string $text): string
    {
        $iconHtml = (string) Icon::make($icon, 6)->render();

        return sprintf(
            '<div class="menu-guide"><span class="menu-guide__icon">%s</span><strong>%s</strong><span>%s</span></div>',
            $iconHtml,
            e($title),
            e($text),
        );
    }
}
