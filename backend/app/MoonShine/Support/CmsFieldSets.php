<?php

declare(strict_types=1);

namespace App\MoonShine\Support;

use App\Models\MenuItem;
use App\Models\Page;
use App\Models\PageBlock;
use App\MoonShine\Resources\Page\PageResource;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\CKEditor\Fields\CKEditor;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Preview;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Url;

final class CmsFieldSets
{
    public static function for(string $key, string $mode = 'form'): array
    {
        $compact = $mode === 'index';

        return match ($key) {
            'site_setting' => self::siteSetting($compact),
            'menu_item' => self::menuItem($compact),
            'page' => self::page($compact),
            'page_block' => self::pageBlock($compact),
            'project' => self::project($compact),
            'service' => self::service($compact),
            'news_article' => self::newsArticle($compact),
            'promo' => self::promo($compact),
            'award' => self::award($compact),
            'partner' => self::partner($compact),
            'review' => self::review($compact),
            'faq' => self::faq($compact),
            'vacancy' => self::vacancy($compact),
            'ui_text' => self::uiText($compact),
            'lead' => self::lead($compact),
            default => [ID::make()->sortable()],
        };
    }

    private static function siteSetting(bool $compact): array
    {
        if ($compact) {
            return [
                ID::make()->sortable(),
                Text::make('Название сайта', 'site_name')->required(),
                Text::make('Телефон', 'phone'),
                Text::make('Ссылка телефона', 'phone_href'),
            ];
        }

        return [
            ...self::siteSettingSection('main'),
            ...self::siteSettingSection('compare'),
            ...self::siteSettingSection('branding'),
            ...self::siteSettingSection('animations'),
            ...self::siteSettingSection('contacts'),
            ...self::siteSettingSection('links'),
            ...self::siteSettingSection('seo'),
        ];
    }

    public static function siteSettingSection(string $section): array
    {
        return match ($section) {
            'main' => [
                Text::make('Название сайта', 'site_name')
                    ->required()
                    ->placeholder('3D Smart Design Studio')
                    ->hint('Показывается в подписи логотипа и используется как запасной заголовок сайта.'),
            ],
            'compare' => [
                Text::make('Подпись блока «До / После»', 'compare_eyebrow')
                    ->placeholder('Render / Blueprint')
                    ->hint('Маленькая строка над заголовком блока сравнения «до / после» в портфолио.'),
                Text::make('Заголовок блока «До / После»', 'compare_title')
                    ->placeholder('Сравнение до / после')
                    ->hint('Крупный заголовок блока сравнения с ползунком в портфолио.'),
                Textarea::make('Текст блока «До / После»', 'compare_text')
                    ->hint('Описание под заголовком блока сравнения. Изображения «до» и «после» задаются в карточке каждого проекта.'),
            ],
            'branding' => [
                Image::make('Логотип сайта', 'site_logo')
                    ->disk('public')
                    ->dir('site/branding')
                    ->allowedExtensions(['svg', 'png', 'jpg', 'jpeg', 'webp'])
                    ->disableDeleteFiles()
                    ->removable()
                    ->hint('Основной логотип для подвала сайта. Подойдут SVG, PNG, JPG или WEBP до 4 МБ.'),
                Image::make('Компактный логотип', 'site_logo_small')
                    ->disk('public')
                    ->dir('site/branding')
                    ->allowedExtensions(['svg', 'png', 'jpg', 'jpeg', 'webp'])
                    ->disableDeleteFiles()
                    ->removable()
                    ->hint('Логотип для верхней панели. Если оставить пустым, будет использован основной логотип.'),
                File::make('Фавикон', 'favicon')
                    ->disk('public')
                    ->dir('site/branding')
                    ->allowedExtensions(['ico', 'svg', 'png'])
                    ->removable()
                    ->hint('Маленькая иконка во вкладке браузера. Лучше ICO, SVG или квадратный PNG.'),
                Image::make('Иконка для iPhone и iPad', 'apple_touch_icon')
                    ->disk('public')
                    ->dir('site/branding')
                    ->allowedExtensions(['png', 'jpg', 'jpeg', 'webp'])
                    ->disableDeleteFiles()
                    ->removable()
                    ->hint('Квадратная картинка для сохранения сайта на экран устройства. Рекомендуется PNG 180x180.'),
            ],
            'animations' => [
                Switcher::make('Включить анимации сайта', 'animations_enabled')
                    ->default(true)
                    ->hint('Главный переключатель GSAP-анимаций, появления блоков и декоративной анимации.'),
                Switcher::make('Масляный скролл', 'smooth_scroll_enabled')
                    ->default(true)
                    ->hint('Плавная прокрутка колесом мыши на desktop. Выключите, если нужен более привычный скролл.'),
                Switcher::make('Появление блоков при скролле', 'page_reveal_enabled')
                    ->default(true)
                    ->hint('Анимация появления карточек и секций при прокрутке страницы.'),
            ],
            'contacts' => [
                Text::make('Телефон для показа', 'phone')
                    ->placeholder('+7 (987) 942-12-42')
                    ->hint('Так номер увидит посетитель сайта.'),
                Text::make('Ссылка для звонка', 'phone_href')
                    ->placeholder('tel:+79879421242')
                    ->hint('Для звонка с телефона начните значение с tel:. Пример: tel:+79879421242.'),
                Textarea::make('Email, по одному в строке', 'emails')
                    ->placeholder("mail@example.ru\ninfo@example.ru - вопросы по рекламе")
                    ->hint('В начале каждой строки должен быть email. После него можно добавить пояснение через дефис.'),
                Text::make('Режим работы', 'schedule')
                    ->placeholder('Пн-Пт 9:00-20:00, Сб-Вс 10:00-19:00')
                    ->hint('Короткая строка для блока контактов.'),
                Textarea::make('Адрес и пояснение', 'address')
                    ->placeholder('Самара. Работаем удаленно по всей России.')
                    ->hint('Можно указать город, офис или короткое пояснение о географии работы.'),
                Textarea::make('Ссылка для карты', 'map_src')
                    ->placeholder('https://www.openstreetmap.org/export/embed.html?...')
                    ->hint('Вставьте URL карты, который можно открыть в браузере. HTML-код iframe сюда не нужен.'),
            ],
            'links' => [
                Url::make('Telegram', 'telegram_url')
                    ->placeholder('https://t.me/company')
                    ->hint('Ссылка на чат, канал или профиль Telegram.'),
                Url::make('MAX', 'max_url')
                    ->placeholder('https://max.ru/company')
                    ->hint('Ссылка на страницу в MAX.'),
                Url::make('VK', 'vk_url')
                    ->placeholder('https://vk.com/company')
                    ->hint('Ссылка на сообщество или профиль VK.'),
                Url::make('LinkedIn', 'linkedin_url')
                    ->placeholder('https://www.linkedin.com/in/company')
                    ->hint('Оставьте пустым, если ссылка не нужна на сайте.'),
                Url::make('Behance', 'behance_url')
                    ->placeholder('https://www.behance.net/company')
                    ->hint('Оставьте пустым, если портфолио не ведется в Behance.'),
                Url::make('Pinterest', 'pinterest_url')
                    ->placeholder('https://pinterest.com/company')
                    ->hint('Оставьте пустым, если Pinterest не используется.'),
            ],
            'seo' => [
                Text::make('SEO-заголовок', 'seo_title')
                    ->placeholder('3D Smart Design Studio')
                    ->hint('Заголовок сайта во вкладке браузера и для поисковой выдачи.'),
                Textarea::make('SEO-описание', 'seo_description')
                    ->placeholder('Коротко опишите студию, услуги и город.')
                    ->hint('Короткое описание сайта. Держите главный смысл в первых 160 символах.'),
                Image::make('Изображение для соцсетей', 'social_preview_image')
                    ->disk('public')
                    ->dir('site/branding')
                    ->allowedExtensions(['png', 'jpg', 'jpeg', 'webp'])
                    ->disableDeleteFiles()
                    ->removable()
                    ->hint('Картинка для превью ссылки в соцсетях. Рекомендуется горизонтальный PNG или JPG.'),
            ],
            default => [],
        };
    }

    private static function menuItem(bool $compact): array
    {
        if ($compact) {
            return [
                ID::make()->sortable(),
                Preview::make('Где показывается', 'menu_area', static function (mixed $item): string {
                    $area = $item instanceof MenuItem ? $item->menu_area : (string) $item;
                    $label = self::menuAreaLabel($area);
                    $modifier = $area === MenuItem::AREA_SERVICES ? ' menu-link-badge--services' : '';

                    return sprintf('<span class="menu-link-badge%s">%s</span>', $modifier, e($label));
                }),
                Preview::make('Уровень', 'parent_id', static function (mixed $item): string {
                    if (! $item instanceof MenuItem) {
                        return '';
                    }

                    if ($item->parent_id === null) {
                        return '<span class="menu-tree-badge">Верхний раздел</span>';
                    }

                    return sprintf(
                        '<span class="menu-tree-badge menu-tree-badge--child">Подпункт</span><span class="menu-parent-label">%s</span>',
                        e($item->parent?->labelRu() ?? 'Родитель не найден'),
                    );
                }),
                Text::make('Название RU', 'label_ru')->sortable(),
                Text::make('Название EN', 'label_en')->sortable(),
                self::menuPageField(),
                Preview::make('Ссылка на сайте', 'href', static function (mixed $item): string {
                    if (! $item instanceof MenuItem) {
                        return e((string) $item);
                    }

                    $href = $item->siteHref();

                    if ($href === null) {
                        return '<span class="menu-link-badge menu-link-badge--muted">Не показывается</span>';
                    }

                    $kind = $item->page_id !== null ? 'Страница' : 'Своя ссылка';

                    return sprintf(
                        '<span class="menu-link-badge">%s</span><code class="menu-link-path">%s</code>',
                        e($kind),
                        e($href),
                    );
                }),
                Number::make('Порядок', 'position')->sortable(),
                Switcher::make('Показывать', 'is_active'),
            ];
        }

        return [
            ...self::menuItemSection('placement'),
            ...self::menuItemSection('text'),
            ...self::menuItemSection('target'),
            ...self::menuItemSection('display'),
        ];
    }

    public static function menuItemSection(string $section): array
    {
        return match ($section) {
            'placement' => [
                Select::make('Где показывать', 'menu_area')
                    ->options([
                        MenuItem::AREA_MAIN => 'Главное меню сайта',
                        MenuItem::AREA_SERVICES => 'Структура услуг',
                    ])
                    ->default(MenuItem::AREA_MAIN)
                    ->required()
                    ->hint('Главное меню - обычные ссылки сайта. Структура услуг - группы и подпункты в раскрытии "Услуги" и на странице услуг.'),
                Select::make('Родительский раздел услуг', 'parent_id')
                    ->options(static fn (): array => MenuItem::query()
                        ->where('menu_area', MenuItem::AREA_SERVICES)
                        ->whereNull('parent_id')
                        ->orderBy('position')
                        ->get()
                        ->mapWithKeys(static fn (MenuItem $item): array => [
                            $item->id => sprintf('%s (%s)', $item->labelRu(), $item->siteHref() ?? 'без ссылки'),
                        ])
                        ->all())
                    ->nullable()
                    ->searchable()
                    ->hint('Заполняйте только для подпунктов. Если оставить пустым, запись станет верхним разделом структуры услуг.'),
                Textarea::make('Описание раздела услуг RU', 'description_ru')
                    ->nullable()
                    ->hint('Показывается на странице "Услуги" у верхнего раздела. Для обычного меню и подпунктов можно оставить пустым.'),
                Textarea::make('Описание раздела услуг EN', 'description_en')
                    ->nullable()
                    ->hint('Английская версия описания для переключателя RU/EN.'),
            ],
            'text' => [
                Text::make('Название в меню RU', 'label_ru')
                    ->placeholder('О нас')
                    ->hint('Русский текст, который увидит посетитель. Если выбрана страница и поле оставить пустым, возьмем заголовок страницы.'),
                Text::make('Название в меню EN', 'label_en')
                    ->placeholder('About Us')
                    ->hint('Английский текст для переключателя языка на сайте.'),
            ],
            'target' => [
                self::menuPageField(),
                Text::make('Другая ссылка', 'href')
                    ->nullable()
                    ->placeholder('/portfolio или https://example.com')
                    ->hint('Оставьте пустым, если выбрали страницу выше. Заполняйте только для готовых разделов сайта, якорей или внешних ссылок.'),
            ],
            'display' => [
                Number::make('Порядок в меню', 'position')
                    ->default(0)
                    ->sortable()
                    ->hint('Меньшее число ставит пункт ближе к началу меню. Например: 1, 2, 3.'),
                Switcher::make('Показывать на сайте', 'is_active')
                    ->default(true)
                    ->hint('Выключите, чтобы временно скрыть пункт меню без удаления.'),
            ],
            default => [],
        };
    }

    public static function menuAreaLabel(?string $area): string
    {
        return match ($area) {
            MenuItem::AREA_SERVICES => 'Структура услуг',
            MenuItem::AREA_MAIN => 'Главное меню',
            default => filled($area) ? $area : 'Главное меню',
        };
    }

    private static function menuPageField(): BelongsTo
    {
        return BelongsTo::make(
            'Страница сайта',
            'page',
            formatted: static function (Page $page): string {
                if (! $page->exists || blank($page->slug)) {
                    return '';
                }

                return sprintf(
                    '%s /%s%s',
                    $page->title,
                    ltrim($page->slug, '/'),
                    $page->is_published ? '' : ' (не опубликована)',
                );
            },
            resource: PageResource::class,
        )
            ->nullable()
            ->searchable()
            ->valuesQuery(static fn (Builder $query): Builder => $query
                ->select(['id', 'title', 'slug', 'is_published'])
                ->orderBy('title'))
            ->hint('Выберите страницу из раздела "Страницы". Ее адрес подставится автоматически и обновится при смене slug.');
    }

    private static function page(bool $compact): array
    {
        if ($compact) {
            return [
                ID::make()->sortable(),
                Text::make('Заголовок RU', 'title_ru')->sortable(),
                Text::make('Заголовок EN', 'title_en'),
                Preview::make('Адрес', 'slug', static function (mixed $item): string {
                    $slug = $item instanceof Page ? $item->slug : (string) $item;

                    return filled($slug)
                        ? '<code class="page-path">/'.e(ltrim($slug, '/')).'</code>'
                        : '<span class="page-badge page-badge--muted">Нет адреса</span>';
                }),
                Preview::make('Тип', 'template', static function (mixed $item): string {
                    $template = $item instanceof Page ? $item->template : (string) $item;

                    return sprintf(
                        '<span class="page-badge">%s</span>',
                        e(self::pageTemplateLabel($template)),
                    );
                }),
                Preview::make('Меню', 'id', static function (mixed $item): string {
                    if (! $item instanceof Page) {
                        return '';
                    }

                    return $item->menuItems()->exists()
                        ? '<span class="page-badge page-badge--menu">Есть пункт</span>'
                        : '<span class="page-badge page-badge--muted">Без меню</span>';
                }),
                Switcher::make('Опубликована', 'is_published'),
            ];
        }

        return [
            ...self::pageSection('main'),
            ...self::pageSection('seo'),
            ...self::pageSection('publish'),
            ...self::pageSection('menu'),
        ];
    }

    public static function pageSection(string $section): array
    {
        return match ($section) {
            'main' => [
                Text::make('Заголовок страницы RU', 'title_ru')
                    ->required()
                    ->placeholder('О нас')
                    ->hint('Русский заголовок страницы. Он будет виден посетителю и поможет создать адрес.'),
                Text::make('Заголовок страницы EN', 'title_en')
                    ->placeholder('About Us')
                    ->hint('Английский заголовок для переключателя RU/EN.'),
                Slug::make('Адрес страницы', 'slug')
                    ->from('title_ru')
                    ->unique()
                    ->required()
                    ->placeholder('o-nas')
                    ->hint('Часть ссылки после домена. Для новой страницы обычно достаточно адреса, который предложит форма.'),
                Select::make('Тип страницы', 'template')
                    ->options([
                        'text' => 'Обычная текстовая страница',
                        'content' => 'Страница-конструктор с блоками',
                        'legal' => 'Юридическая страница с узким текстом',
                        'about' => 'О нас: служебный раздел',
                        'blog' => 'Блог: лента новостей',
                    ])
                    ->default('text')
                    ->hint('Для новой страницы обычно выбирайте "Текстовая" или "Страница-конструктор". Служебные типы нужны только существующим разделам вроде блога и юридических страниц.'),
                CKEditor::make('Текст страницы RU', 'body_ru')
                    ->hint('Русский текст страницы. Для /o-nas чаще редактируются блоки страницы ниже в разделе "Блоки страниц".'),
                CKEditor::make('Текст страницы EN', 'body_en')
                    ->hint('Английская версия текста страницы. Если оставить пустой, сайт покажет русский fallback.'),
            ],
            'seo' => [
                Text::make('SEO-заголовок RU', 'seo_title_ru')
                    ->placeholder('О нас | 3D Smart Design')
                    ->hint('Русский заголовок во вкладке и в поиске. Можно оставить пустым.'),
                Text::make('SEO-заголовок EN', 'seo_title_en')
                    ->placeholder('About Us | 3D Smart Design')
                    ->hint('Английский SEO-заголовок.'),
                Textarea::make('SEO-описание RU', 'seo_description_ru')
                    ->placeholder('Коротко опишите, что посетитель найдет на этой странице.')
                    ->hint('Русское описание для поиска и превью. Достаточно одного-двух предложений.'),
                Textarea::make('SEO-описание EN', 'seo_description_en')
                    ->placeholder('Briefly describe what visitors will find on this page.')
                    ->hint('Английское описание для поиска и превью.'),
            ],
            'publish' => [
                Switcher::make('Опубликовать страницу', 'is_published')
                    ->default(true)
                    ->hint('Выключите, чтобы подготовить страницу и пока не показывать ее посетителям.'),
            ],
            'menu' => [
                Switcher::make('Создать или обновить пункт меню', 'create_menu_item')
                    ->onApply(static fn (Page $page): Page => $page)
                    ->hint('Включите при сохранении, если эта страница должна появиться в меню сайта.'),
                Text::make('Название в меню RU', 'menu_label_ru')
                    ->nullable()
                    ->placeholder('О нас')
                    ->onApply(static fn (Page $page): Page => $page)
                    ->hint('Можно оставить пустым: тогда пункт меню получит русский заголовок страницы.'),
                Text::make('Название в меню EN', 'menu_label_en')
                    ->nullable()
                    ->placeholder('About Us')
                    ->onApply(static fn (Page $page): Page => $page)
                    ->hint('Английское название пункта меню. Можно оставить пустым, если есть EN-заголовок страницы.'),
            ],
            default => [],
        };
    }

    public static function pageTemplateLabel(?string $template): string
    {
        return match ($template) {
            'text' => 'Текстовая',
            'content' => 'С блоками',
            'legal' => 'Юридическая',
            'about' => 'О нас',
            'blog' => 'Блог',
            default => filled($template) ? $template : 'Не задан',
        };
    }

    private static function pageBlock(bool $compact): array
    {
        if ($compact) {
            return [
                ID::make()->sortable(),
                self::pageBlockPageField(),
                Preview::make('Блок', 'type', static function (mixed $item): string {
                    $type = $item instanceof PageBlock ? $item->type : (string) $item;

                    return sprintf(
                        '<span class="page-block-badge">%s</span>',
                        e(self::pageBlockTypeLabel($type)),
                    );
                }),
                Text::make('Вариант', 'visual_variant'),
                Text::make('Motion', 'motion_preset'),
                Text::make('Заголовок RU', 'title_ru')->sortable(),
                Text::make('Заголовок EN', 'title_en'),
                Number::make('Порядок', 'position')->sortable(),
                Switcher::make('Показывать', 'is_active'),
            ];
        }

        return [
            ...self::pageBlockSection('target'),
            ...self::pageBlockSection('content'),
            ...self::pageBlockSection('media'),
            ...self::pageBlockSection('action'),
            ...self::pageBlockSection('behavior'),
            ...self::pageBlockSection('display'),
        ];
    }

    public static function pageBlockSection(string $section): array
    {
        return match ($section) {
            'target' => [
                self::pageBlockPageField(),
                Select::make('Вид блока', 'type')
                    ->options([
                        'hero' => 'Первый экран / крупный заголовок',
                        'text' => 'Текстовый блок',
                        'media' => 'Текст + изображение',
                        'gallery' => 'Галерея / слайдер',
                        'quote' => 'Акцентная цитата',
                        'cta' => 'Призыв к действию',
                    ])
                    ->default((string) request()->query('type', 'hero'))
                    ->required()
                    ->hint('После смены типа сохраните блок: форма перестроится и оставит только поля, которые реально используются этим типом.'),
            ],
            'behavior' => [
                Select::make('Визуальный вариант блока', 'visual_variant')
                    ->options([
                        'default' => 'Обычный',
                        'wide' => 'Широкий / больше воздуха',
                        'accent' => 'Акцентный',
                        'compact' => 'Компактный',
                        'split' => 'Разделенный текст + медиа',
                    ])
                    ->default('default')
                    ->hint('Влияет на плотность и акцент блока на контентных страницах.'),
                Select::make('Положение медиа', 'media_position')
                    ->options([
                        '' => 'Автоматически',
                        'right' => 'Картинка справа',
                        'left' => 'Картинка слева',
                        'top' => 'Картинка сверху',
                        'background' => 'Картинка фоном',
                    ])
                    ->nullable()
                    ->hint('Для блоков с изображением. Если оставить "Автоматически", сайт сам чередует стороны.'),
                Select::make('Motion / подпись слайдера', 'motion_preset')
                    ->options([
                        'none' => 'Без motion-подписи',
                        'motion' => 'Motion',
                        'slides' => 'Slides',
                        'story' => 'Story',
                        'read' => 'Read',
                        'preview' => 'Preview',
                    ])
                    ->default('motion')
                    ->hint('Подпись и режим интерактивной карточки с несколькими изображениями.'),
                Select::make('Состояние карточки', 'card_state')
                    ->options([
                        'normal' => 'Обычная',
                        'featured' => 'Выделенная',
                        'muted' => 'Спокойная',
                        'disabled' => 'Приглушенная',
                    ])
                    ->default('normal')
                    ->hint('Состояние для карточек/секций. Удобно выделять важный блок без правки кода.'),
                Json::make('Дополнительные настройки JSON', 'settings')
                    ->hint('Для редких тонких настроек: подписи, технические ключи, будущие параметры. Если не нужно - оставьте пустым.'),
            ],
            'content' => [
                Text::make('Маленькая строка над заголовком RU', 'eyebrow_ru')
                    ->placeholder('Студия дизайна интерьера и архитектуры в Самаре')
                    ->hint('Русская тонкая подпись над крупным заголовком. Можно оставить пустой.'),
                Text::make('Маленькая строка над заголовком EN', 'eyebrow_en')
                    ->placeholder('Interior design and architecture studio in Samara')
                    ->hint('Английская подпись над заголовком.'),
                Text::make('Главный заголовок блока RU', 'title_ru')
                    ->placeholder('О нас')
                    ->hint('Русский главный заголовок блока.'),
                Text::make('Главный заголовок блока EN', 'title_en')
                    ->placeholder('About Us')
                    ->hint('Английский главный заголовок блока.'),
                Textarea::make('Описание под заголовком RU', 'subtitle_ru')
                    ->placeholder('Редактируемый блок страницы из MoonShine.')
                    ->hint('Русский короткий абзац рядом с кнопкой или сразу под заголовком.'),
                Textarea::make('Описание под заголовком EN', 'subtitle_en')
                    ->placeholder('Editable page block from MoonShine.')
                    ->hint('Английский короткий абзац рядом с кнопкой или сразу под заголовком.'),
                Textarea::make('Дополнительный текст блока RU', 'text_ru')
                    ->hint('Русский дополнительный текст блока.'),
                Textarea::make('Дополнительный текст блока EN', 'text_en')
                    ->hint('Английский дополнительный текст блока.'),
            ],
            'action' => [
                Text::make('Текст кнопки RU', 'link_label_ru')
                    ->placeholder('Обсудить проект')
                    ->hint('Русская надпись на кнопке. Если кнопка в этом блоке не нужна, оставьте поле пустым.'),
                Text::make('Текст кнопки EN', 'link_label_en')
                    ->placeholder('Discuss a project')
                    ->hint('Английская надпись на кнопке.'),
                Text::make('Куда ведет кнопка', 'link_href')
                    ->placeholder('/kontakty')
                    ->hint('Для страницы сайта укажите путь с /. Например: /kontakty. Можно вставить и полную внешнюю ссылку.'),
            ],
            'media' => [
                Textarea::make('Изображения / слайды', 'image')
                    ->customAttributes(['data-gallery-lines' => '1'])
                    ->placeholder("/images/cms/slide-1.webp\n/images/cms/slide-2.webp")
                    ->hint('Выберите изображения из галереи или укажите URL. Для слайдера и галереи добавляйте каждую картинку с новой строки.'),
                Text::make('Alt изображения RU', 'image_alt_ru')
                    ->placeholder('Интерьер гостиной в современном стиле')
                    ->hint('Описание изображения для доступности и SEO. Если пусто, сайт возьмет заголовок блока.'),
                Text::make('Alt изображения EN', 'image_alt_en')
                    ->placeholder('Modern living room interior')
                    ->hint('Английский alt изображения.'),
            ],
            'display' => [
                Number::make('Порядок на странице', 'position')
                    ->default(1)
                    ->sortable()
                    ->hint('Меньшее число ставит блок раньше. Для первого экрана обычно указывают 1.'),
                Switcher::make('Показывать блок на сайте', 'is_active')
                    ->default(true)
                    ->hint('Выключите, чтобы временно скрыть блок без удаления текста.'),
            ],
            default => [],
        };
    }

    public static function pageBlockTypeLabel(?string $type): string
    {
        return match ($type) {
            'hero' => 'Первый экран',
            'text' => 'Текстовый блок',
            'media' => 'Текст + изображение',
            'gallery' => 'Галерея',
            'quote' => 'Цитата',
            'cta' => 'CTA',
            default => filled($type) ? $type : 'Не задан',
        };
    }

    private static function pageBlockPageField(): BelongsTo
    {
        return BelongsTo::make(
            'Страница сайта',
            'page',
            formatted: static function (Page $page): string {
                if (! $page->exists) {
                    return '';
                }

                $path = $page->slug === 'home' ? 'Главная' : '/'.ltrim((string) $page->slug, '/');

                return sprintf(
                    '%s - %s%s',
                    $page->title,
                    $path,
                    $page->is_published ? '' : ' (не опубликована)',
                );
            },
            resource: PageResource::class,
        )
            ->searchable()
            ->valuesQuery(static fn (Builder $query): Builder => $query
                ->select(['id', 'title', 'slug', 'is_published'])
                ->orderBy('title'))
            ->default(request()->integer('page_id') ?: null)
            ->required()
            ->hint('Выберите страницу, на которой должен появиться блок. Для первого экрана сайта выбирайте страницу "Главная".');
    }

    private static function project(bool $compact): array
    {
        $fields = [
            ID::make()->sortable(),
            Text::make('Slug', 'slug')->required(),
            Text::make('Заголовок RU', 'title_ru')->required(),
            Text::make('Заголовок EN', 'title_en'),
            Text::make('Категория RU', 'category_ru')->required(),
            Text::make('Категория EN', 'category_en'),
            Number::make('Позиция', 'position')->sortable(),
            Switcher::make('Опубликовано', 'is_published'),
        ];

        return $compact ? $fields : [
            ...$fields,
            Text::make('Локация RU', 'location_ru'),
            Text::make('Локация EN', 'location_en'),
            Text::make('Год', 'year'),
            Textarea::make('Описание RU', 'description_ru'),
            Textarea::make('Описание EN', 'description_en'),
            Image::make('Загрузить главное изображение', 'image_file')
                ->disk('public')
                ->dir('projects')
                ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'avif'])
                ->disableDeleteFiles()
                ->removable()
                ->hint('Загрузите файл на сервер. Имеет приоритет над URL ниже.'),
            Textarea::make('Или URL главного изображения', 'image')
                ->customAttributes(['data-gallery-lines' => '1'])
                ->hint('Вставьте путь из галереи или внешнюю ссылку. Используется если файл выше не загружен.'),
            Image::make('Загрузить изображение «До»', 'before_image_file')
                ->disk('public')
                ->dir('projects')
                ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'avif'])
                ->disableDeleteFiles()
                ->removable()
                ->hint('Загрузите файл на сервер. Имеет приоритет над URL ниже.'),
            Textarea::make('Или URL изображения «До»', 'before_image')
                ->customAttributes(['data-gallery-lines' => '1'])
                ->hint('Вставьте путь из галереи или внешнюю ссылку. Используется если файл выше не загружен.'),
            Image::make('Загрузить изображение «После»', 'after_image_file')
                ->disk('public')
                ->dir('projects')
                ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'avif'])
                ->disableDeleteFiles()
                ->removable()
                ->hint('Загрузите файл на сервер. Имеет приоритет над URL ниже.'),
            Textarea::make('Или URL изображения «После»', 'after_image')
                ->customAttributes(['data-gallery-lines' => '1'])
                ->hint('Вставьте путь из галереи или внешнюю ссылку. Используется если файл выше не загружен.'),
            Switcher::make('Избранный', 'is_featured'),
        ];
    }

    private static function service(bool $compact): array
    {
        $fields = [
            ID::make()->sortable(),
            Text::make('Заголовок RU', 'title_ru')->required()->sortable(),
            Text::make('Заголовок EN', 'title_en')->sortable(),
            Text::make('Стоимость RU', 'price_ru')->sortable(),
            Text::make('Срок RU', 'timeline_ru'),
            Number::make('Позиция', 'position')->sortable(),
            Switcher::make('Опубликовано', 'is_published'),
        ];

        return $compact ? $fields : [
            ...self::serviceSection('main'),
            ...self::serviceSection('pricing'),
            ...self::serviceSection('content'),
            ...self::serviceSection('media'),
            ...self::serviceSection('lists'),
            ...self::serviceSection('publish'),
        ];
    }

    public static function serviceSection(string $section): array
    {
        return match ($section) {
            'main' => [
                Text::make('Заголовок RU', 'title_ru')
                    ->required()
                    ->placeholder('Дизайн-проект интерьера под ключ')
                    ->hint('Главный заголовок услуги на сайте. Можно писать длинно, верстка теперь переносит строки аккуратно.'),
                Text::make('Заголовок EN', 'title_en')
                    ->placeholder('Turnkey interior design project')
                    ->hint('Английская версия. Если оставить пустым, сайт покажет русский вариант.'),
                Text::make('Slug', 'slug')
                    ->required()
                    ->placeholder('dizajn-proekt-interyera')
                    ->hint('Адрес страницы латиницей: только буквы, цифры и дефисы. Пример: 3d-vizualizaciya-interyera.'),
                Text::make('Надзаголовок RU', 'eyebrow_ru')
                    ->placeholder('Интерьеры / полный цикл')
                    ->hint('Небольшая подпись над заголовком или в карточке услуги. Лучше 2-5 слов.'),
                Text::make('Надзаголовок EN', 'eyebrow_en')
                    ->placeholder('Interiors / full cycle')
                    ->hint('Короткая английская подпись для переключателя языка.'),
            ],
            'pricing' => [
                Text::make('Стоимость RU', 'price_ru')
                    ->placeholder('от 2 500 ₽/м²')
                    ->hint('Текстом, как должно быть на сайте: "от 2 500 ₽/м²", "по запросу", "от 45 000 ₽".'),
                Text::make('Стоимость EN', 'price_en')
                    ->placeholder('from 2,500 RUB/m²')
                    ->hint('Английский вариант цены. Если поле пустое, в EN-версии будет fallback на русский текст.'),
                Text::make('Срок RU', 'timeline_ru')
                    ->placeholder('3-8 недель')
                    ->hint('Короткий срок выполнения услуги. Показывается рядом с ценой на посадочной странице.'),
                Text::make('Срок EN', 'timeline_en')
                    ->placeholder('3-8 weeks')
                    ->hint('Английская версия срока.'),
            ],
            'content' => [
                CKEditor::make('Описание RU', 'text_ru')
                    ->hint('Основной текст услуги. Можно использовать абзацы, списки и выделения.'),
                CKEditor::make('Описание EN', 'text_en')
                    ->hint('Английская версия описания. Если не нужна, оставьте пустой.'),
            ],
            'media' => [
                Image::make('Главная обложка', 'image_file')
                    ->disk('public')
                    ->dir('services')
                    ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'avif'])
                    ->disableDeleteFiles()
                    ->removable()
                    ->hint('Файл для карточки и первого слайда. Имеет приоритет над URL ниже.'),
                Textarea::make('Или URL главной обложки', 'image')
                    ->customAttributes(['data-gallery-lines' => '1'])
                    ->placeholder('/images/cms/service.webp или https://...')
                    ->hint('Запасной вариант, если файл не загружен. Можно выбрать изображение из галереи.'),
                Textarea::make('Слайды услуги, по одному URL в строке', 'hero_images')
                    ->customAttributes(['data-gallery-lines' => '1'])
                    ->placeholder("/images/cms/slide-1.webp\n/images/cms/slide-2.webp")
                    ->hint('Карусель на посадочной странице услуги. Нажмите "Вставить из галереи" и добавляйте слайды построчно.'),
            ],
            'lists' => [
                Textarea::make('Что входит RU, по одному пункту в строке', 'deliverables_ru')
                    ->placeholder("Планировочное решение\n3D-визуализации\nРабочая документация")
                    ->hint('Список документов, результатов или частей услуги. Каждый пункт с новой строки.'),
                Textarea::make('Что входит EN, по одному пункту в строке', 'deliverables_en')
                    ->hint('Английская версия списка результатов.'),
                Textarea::make('Картинки для пунктов "что входит", по одному изображению в строке', 'deliverable_images')
                    ->customAttributes(['data-gallery-lines' => '1'])
                    ->placeholder("/images/cms/sheet-1.webp\n/images/cms/sheet-2.webp")
                    ->hint('Порядок строк соответствует пунктам "Что входит". Если строк меньше, сайт подставит обложку услуги.'),
                Textarea::make('Преимущества RU, по одному пункту в строке', 'benefits_ru')
                    ->placeholder("Понятный бюджет\nКонтроль материалов\nМеньше переделок")
                    ->hint('Короткие выгоды услуги. Лучше 3-6 пунктов.'),
                Textarea::make('Преимущества EN, по одному пункту в строке', 'benefits_en')
                    ->hint('Английская версия преимуществ.'),
                Textarea::make('Этапы RU, по одному пункту в строке', 'process_ru')
                    ->placeholder("Бриф\nКонцепция\nВизуализация\nЧертежи\nПередача")
                    ->hint('Порядок работы. На сайте отображается как горизонтальный процесс.'),
                Textarea::make('Этапы EN, по одному пункту в строке', 'process_en')
                    ->hint('Английская версия этапов.'),
            ],
            'publish' => [
                Number::make('Позиция', 'position')
                    ->default(0)
                    ->sortable()
                    ->hint('Меньшее число ставит услугу выше в списках. Например: 10, 20, 30.'),
                Switcher::make('Опубликовано', 'is_published')
                    ->default(true)
                    ->hint('Выключите, чтобы скрыть услугу на сайте без удаления.'),
            ],
            default => [],
        };
    }

    private static function newsArticle(bool $compact): array
    {
        $fields = [
            ID::make()->sortable(),
            Text::make('Заголовок RU', 'title_ru')->required()->sortable(),
            Text::make('Заголовок EN', 'title_en'),
            Text::make('Категория RU', 'category_ru'),
            Date::make('Дата ISO', 'date_iso'),
            Number::make('Позиция', 'position')->sortable(),
            Switcher::make('Опубликовано', 'is_published'),
        ];

        return $compact ? $fields : [
            ...self::newsArticleSection('main'),
            ...self::newsArticleSection('date'),
            ...self::newsArticleSection('content'),
            ...self::newsArticleSection('media'),
            ...self::newsArticleSection('publish'),
        ];
    }

    public static function newsArticleSection(string $section): array
    {
        return match ($section) {
            'main' => [
                Text::make('Заголовок RU', 'title_ru')
                    ->required()
                    ->placeholder('Один клик - вся квартира: виртуальный тур 360')
                    ->hint('Главный заголовок новости. Длинные заголовки допустимы, но лучше держать одну мысль.'),
                Text::make('Заголовок EN', 'title_en')
                    ->placeholder('One click - the whole apartment: 360 tour')
                    ->hint('Английская версия заголовка.'),
                Text::make('Slug', 'slug')
                    ->required()
                    ->placeholder('virtualnyj-tur-360')
                    ->hint('Адрес статьи латиницей: только буквы, цифры и дефисы.'),
                Text::make('Категория RU', 'category_ru')
                    ->placeholder('Услуги')
                    ->hint('Короткая категория для бейджа в списке новостей.'),
                Text::make('Категория EN', 'category_en')
                    ->placeholder('Services')
                    ->hint('Английская версия категории.'),
            ],
            'date' => [
                Date::make('Дата для сортировки', 'date_iso')
                    ->hint('Реальная дата публикации в формате календаря. По ней удобно сортировать и проверять актуальность.'),
                Text::make('Дата для показа RU', 'date_ru')
                    ->placeholder('20 августа 2026')
                    ->hint('Как дата будет выглядеть на русском сайте.'),
                Text::make('Дата для показа EN', 'date_en')
                    ->placeholder('August 20, 2026')
                    ->hint('Как дата будет выглядеть на английском сайте.'),
                Text::make('Время чтения RU', 'reading_time_ru')
                    ->placeholder('4 мин')
                    ->hint('Короткая подпись рядом с датой.'),
                Text::make('Время чтения EN', 'reading_time_en')
                    ->placeholder('4 min')
                    ->hint('Английская версия времени чтения.'),
            ],
            'content' => [
                Textarea::make('Анонс RU', 'preview_ru')
                    ->placeholder('Короткий лид для карточки и начала статьи.')
                    ->hint('2-3 предложения. Показывается в карточках, hero и начале статьи.'),
                Textarea::make('Анонс EN', 'preview_en')
                    ->hint('Английская версия анонса.'),
                Textarea::make('Текст статьи RU', 'body_ru')
                    ->placeholder("Первый абзац статьи.\n\nВторой абзац статьи.")
                    ->hint('Разделяйте абзацы пустой строкой. На сайте они автоматически превратятся в читабельные блоки.'),
                Textarea::make('Текст статьи EN', 'body_en')
                    ->hint('Английская версия текста.'),
            ],
            'media' => [
                Image::make('Главная обложка', 'image_file')
                    ->disk('public')
                    ->dir('news')
                    ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'avif'])
                    ->disableDeleteFiles()
                    ->removable()
                    ->hint('Файл для карточки новости и первого слайда. Имеет приоритет над URL ниже.'),
                Textarea::make('Или URL главной обложки', 'image')
                    ->customAttributes(['data-gallery-lines' => '1'])
                    ->placeholder('/images/cms/news.webp или https://...')
                    ->hint('Запасной вариант, если файл не загружен. Можно выбрать изображение из галереи.'),
                Textarea::make('Слайды новости, по одному URL в строке', 'hero_images')
                    ->customAttributes(['data-gallery-lines' => '1'])
                    ->placeholder("/images/cms/news-slide-1.webp\n/images/cms/news-slide-2.webp")
                    ->hint('Карусель в первом экране статьи и на странице новостей. Добавляйте изображения из галереи построчно.'),
            ],
            'publish' => [
                Number::make('Позиция', 'position')
                    ->default(0)
                    ->sortable()
                    ->hint('Меньшее число ставит материал выше в списке.'),
                Switcher::make('Опубликовано', 'is_published')
                    ->default(true)
                    ->hint('Выключите, чтобы сохранить черновик и скрыть его от посетителей.'),
            ],
            default => [],
        };
    }

    private static function promo(bool $compact): array
    {
        $fields = [
            ID::make()->sortable(),
            Text::make('Slug', 'slug')->required(),
            Text::make('Заголовок RU', 'title_ru')->required(),
            Text::make('Заголовок EN', 'title_en'),
            Text::make('Бейдж RU', 'badge_ru'),
            Text::make('Бейдж EN', 'badge_en'),
            Number::make('Позиция', 'position')->sortable(),
            Switcher::make('Активно', 'is_active'),
        ];

        return $compact ? $fields : [
            ...$fields,
            Text::make('Акцент RU', 'highlight_ru'),
            Text::make('Акцент EN', 'highlight_en'),
            Text::make('Действует до RU', 'valid_until_ru'),
            Text::make('Действует до EN', 'valid_until_en'),
            Textarea::make('Описание RU', 'description_ru'),
            Textarea::make('Описание EN', 'description_en'),
            Textarea::make('Условия RU, по одному в строке', 'conditions_ru'),
            Textarea::make('Условия EN, по одному в строке', 'conditions_en'),
            Image::make('Загрузить изображение на сервер', 'image_file')
                ->disk('public')
                ->dir('promo')
                ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'avif'])
                ->disableDeleteFiles()
                ->removable()
                ->hint('Загрузите файл — он сохранится в хранилище. Если загружен файл, он имеет приоритет над URL ниже.'),
            Textarea::make('Или URL изображения', 'image')
                ->customAttributes(['data-gallery-lines' => '1'])
                ->hint('Вставьте путь из галереи или внешнюю ссылку. Используется только если файл выше не загружен.'),
        ];
    }

    private static function award(bool $compact): array
    {
        $fields = [
            ID::make()->sortable(),
            Text::make('Заголовок RU', 'title_ru')->required(),
            Text::make('Заголовок EN', 'title_en'),
            Text::make('Кем выдано RU', 'issuer_ru'),
            Text::make('Кем выдано EN', 'issuer_en'),
            Text::make('Год', 'year'),
            Number::make('Позиция', 'position')->sortable(),
            Switcher::make('Активно', 'is_active'),
        ];

        return $compact ? $fields : [
            ...$fields,
            Textarea::make('Описание RU', 'description_ru'),
            Textarea::make('Описание EN', 'description_en'),
            Image::make('Загрузить изображение на сервер', 'image_file')
                ->disk('public')
                ->dir('awards')
                ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'avif'])
                ->disableDeleteFiles()
                ->removable()
                ->hint('Загрузите файл — он сохранится в хранилище. Если загружен файл, он имеет приоритет над URL ниже.'),
            Textarea::make('Или URL изображения', 'image')
                ->customAttributes(['data-gallery-lines' => '1'])
                ->hint('Вставьте путь из галереи или внешнюю ссылку. Используется только если файл выше не загружен.'),
        ];
    }

    private static function partner(bool $compact): array
    {
        $fields = [
            ID::make()->sortable(),
            Text::make('Название RU', 'name_ru')->required(),
            Text::make('Название EN', 'name_en'),
            Text::make('Описание RU', 'note_ru'),
            Text::make('Описание EN', 'note_en'),
            Number::make('Позиция', 'position')->sortable(),
            Switcher::make('Активно', 'is_active'),
        ];

        return $compact ? $fields : [
            ...$fields,
            Image::make('Загрузить логотип на сервер', 'logo_file')
                ->disk('public')
                ->dir('partners')
                ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'avif', 'svg'])
                ->disableDeleteFiles()
                ->removable()
                ->hint('Загрузите файл — он сохранится в хранилище. Если загружен файл, он имеет приоритет над URL ниже.'),
            Textarea::make('Или URL логотипа', 'logo')
                ->customAttributes(['data-gallery-lines' => '1'])
                ->hint('Вставьте путь из галереи или внешнюю ссылку. Используется только если файл выше не загружен.'),
        ];
    }

    private static function review(bool $compact): array
    {
        $fields = [
            ID::make()->sortable(),
            Text::make('Имя RU', 'name_ru')->required(),
            Text::make('Имя EN', 'name_en'),
            Text::make('Услуга RU', 'service_ru'),
            Text::make('Услуга EN', 'service_en'),
            Text::make('Заголовок RU', 'title_ru'),
            Text::make('Заголовок EN', 'title_en'),
            Number::make('Позиция', 'position')->sortable(),
            Switcher::make('Опубликовано', 'is_published'),
        ];

        return $compact ? $fields : [
            ...$fields,
            Text::make('Дата RU', 'date_ru'),
            Text::make('Дата EN', 'date_en'),
            Textarea::make('Текст RU', 'text_ru'),
            Textarea::make('Текст EN', 'text_en'),
            Textarea::make('Ответ администратора RU', 'admin_reply_ru'),
            Textarea::make('Ответ администратора EN', 'admin_reply_en'),
            Textarea::make('URL изображения или путь в хранилище', 'image')
                ->customAttributes(['data-gallery-lines' => '1']),
        ];
    }

    private static function faq(bool $compact): array
    {
        $fields = [
            ID::make()->sortable(),
            Text::make('Вопрос RU', 'question_ru')->required(),
            Text::make('Вопрос EN', 'question_en'),
            Number::make('Позиция', 'position')->sortable(),
            Switcher::make('Опубликовано', 'is_published'),
        ];

        return $compact ? $fields : [
            ...$fields,
            Textarea::make('Ответ RU', 'answer_ru')->required(),
            Textarea::make('Ответ EN', 'answer_en'),
        ];
    }

    private static function vacancy(bool $compact): array
    {
        $fields = [
            ID::make()->sortable(),
            Text::make('Заголовок RU', 'title_ru')->required(),
            Text::make('Заголовок EN', 'title_en'),
            Text::make('Занятость RU', 'employment_ru'),
            Text::make('Занятость EN', 'employment_en'),
            Text::make('Локация RU', 'location_ru'),
            Text::make('Локация EN', 'location_en'),
            Text::make('Зарплата RU', 'salary_ru'),
            Text::make('Зарплата EN', 'salary_en'),
            Number::make('Позиция', 'position')->sortable(),
            Switcher::make('Активно', 'is_active'),
        ];

        return $compact ? $fields : [
            ...$fields,
            Textarea::make('Описание RU', 'description_ru'),
            Textarea::make('Описание EN', 'description_en'),
            Textarea::make('Требования RU, по одному в строке', 'requirements_ru'),
            Textarea::make('Требования EN, по одному в строке', 'requirements_en'),
            Textarea::make('Обязанности RU, по одной в строке', 'responsibilities_ru'),
            Textarea::make('Обязанности EN, по одной в строке', 'responsibilities_en'),
        ];
    }

    private static function uiText(bool $compact): array
    {
        $fields = [
            ID::make()->sortable(),
            Text::make('Группа', 'group')->sortable(),
            Text::make('Ключ', 'key')->required()->sortable(),
            Text::make('Название в админке', 'label')->required(),
            Number::make('Позиция', 'position')->sortable(),
            Switcher::make('Активно', 'is_active'),
        ];

        return $compact ? [
            ...$fields,
            Text::make('RU', 'value_ru')->customAttributes(['class' => 'max-w-[280px] truncate']),
            Text::make('EN', 'value_en')->customAttributes(['class' => 'max-w-[280px] truncate']),
        ] : [
            ...$fields,
            Textarea::make('Текст RU', 'value_ru')
                ->hint('Русский вариант строки. Используется как основной fallback.'),
            Textarea::make('Текст EN', 'value_en')
                ->hint('Английский вариант строки для переключателя RU/EN. Если оставить пустым, сайт покажет RU.'),
            Textarea::make('Комментарий для редактора', 'description')
                ->hint('Внутренняя подсказка: где строка используется или что в ней важно не сломать.'),
        ];
    }

    private static function lead(bool $compact): array
    {
        $fields = [
            ID::make()->sortable(),
            Text::make('Источник', 'source'),
            Text::make('Канал', 'channel'),
            Text::make('Имя', 'name'),
            Text::make('Контакт', 'contact')->required(),
            Text::make('Статус', 'status'),
        ];

        return $compact ? $fields : [
            ...$fields,
            Text::make('Услуга', 'service'),
            Textarea::make('Сообщение', 'message'),
            Json::make('Данные', 'payload'),
        ];
    }
}
