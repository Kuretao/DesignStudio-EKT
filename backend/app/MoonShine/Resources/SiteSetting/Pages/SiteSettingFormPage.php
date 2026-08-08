<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\SiteSetting\Pages;

use App\MoonShine\Resources\SiteSetting\SiteSettingResource;
use App\MoonShine\Support\CmsFieldSets;
use Closure;
use Illuminate\Validation\Rules\File as FileRule;
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
 * @extends FormPage<SiteSettingResource>
 */
class SiteSettingFormPage extends FormPage
{
    /**
     * @return list<AssetElementContract>
     */
    protected function assets(): array
    {
        return [
            ...parent::assets(),
            InlineCss::make((string) file_get_contents(resource_path('css/settings-admin.css'))),
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
                ->content('Заполняйте только нужные поля. Пустые ссылки и файлы не будут показываться посетителям сайта.'),
            Tabs::make([
                Tab::make('Доступ', [
                    Box::make('Публикация сайта', [
                        $this->sectionNote(
                            'Режим технических работ',
                            'Включите заглушку перед обновлением сайта или отключите её, когда сайт готов принимать посетителей.'
                        ),
                        Grid::make([
                            Column::make(CmsFieldSets::siteSettingSection('maintenance'))->columnSpan(12),
                        ]),
                    ])->icon('lock-closed')->customAttributes(['class' => 'settings-section']),
                ])->icon('lock-closed')->active(),
                Tab::make('Сайт и SEO', [
                    Box::make('Основное', [
                        $this->sectionNote(
                            'Название сайта',
                            'Начните с названия. Оно используется как запасной текст там, где логотип или SEO-заголовок не заполнены.'
                        ),
                        Grid::make([
                            Column::make(CmsFieldSets::siteSettingSection('main'))->columnSpan(5),
                            Column::make(CmsFieldSets::siteSettingSection('seo'))->columnSpan(7),
                        ]),
                    ])->icon('magnifying-glass')->customAttributes(['class' => 'settings-section']),
                ])->icon('globe-alt'),
                Tab::make('Логотипы', [
                    Box::make('Внешний вид', [
                        $this->sectionNote(
                            'Файлы бренда',
                            'Загрузите логотипы и иконки. Для логотипов удобнее SVG или PNG с прозрачным фоном.'
                        ),
                        Grid::make([
                            Column::make(array_slice(CmsFieldSets::siteSettingSection('branding'), 0, 2))->columnSpan(6),
                            Column::make(array_slice(CmsFieldSets::siteSettingSection('branding'), 2))->columnSpan(6),
                        ]),
                    ])->icon('photo')->customAttributes(['class' => 'settings-section']),
                ])->icon('photo'),
                Tab::make('Анимации', [
                    Box::make('Поведение интерфейса', [
                        $this->sectionNote(
                            'Контроль движения',
                            'Здесь можно быстро выключить тяжелые эффекты, масляный скролл или появления блоков без изменения кода.'
                        ),
                        Grid::make([
                            Column::make(CmsFieldSets::siteSettingSection('animations'))->columnSpan(12),
                        ]),
                    ])->icon('sparkles')->customAttributes(['class' => 'settings-section']),
                ])->icon('sparkles'),
                Tab::make('Контакты', [
                    Box::make('Контакты на сайте', [
                        $this->sectionNote(
                            'Телефон, почта и карта',
                            'Эти данные попадают в контакты и в формы связи. Проверьте ссылку для звонка и URL карты перед сохранением.'
                        ),
                        Grid::make([
                            Column::make(array_slice(CmsFieldSets::siteSettingSection('contacts'), 0, 3))->columnSpan(6),
                            Column::make(array_slice(CmsFieldSets::siteSettingSection('contacts'), 3))->columnSpan(6),
                        ]),
                    ])->icon('phone')->customAttributes(['class' => 'settings-section']),
                ])->icon('phone'),
                Tab::make('Портфолио', [
                    Box::make('Блок «До / После»', [
                        $this->sectionNote(
                            'Сравнение визуальной проработки',
                            'Этот текст показывается над слайдером «до / после» в портфолио. Изображения «до» и «после» задаются отдельно в карточке каждого проекта.'
                        ),
                        Grid::make([
                            Column::make(CmsFieldSets::siteSettingSection('compare'))->columnSpan(12),
                        ]),
                    ])->icon('photo')->customAttributes(['class' => 'settings-section']),
                ])->icon('arrows-right-left'),
                Tab::make('Ссылки', [
                    Box::make('Мессенджеры и соцсети', [
                        $this->sectionNote(
                            'Внешние страницы',
                            'Вставляйте полные ссылки, которые открываются в браузере и начинаются с https://.'
                        ),
                        Grid::make([
                            Column::make(array_slice(CmsFieldSets::siteSettingSection('links'), 0, 2))->columnSpan(6),
                            Column::make(array_slice(CmsFieldSets::siteSettingSection('links'), 2))->columnSpan(6),
                        ]),
                    ])->icon('link')->customAttributes(['class' => 'settings-section']),
                ])->icon('link'),
            ])->vertical()->customAttributes(['class' => 'settings-tabs']),
        ];
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'site_name' => ['required', 'string', 'max:255'],
            'site_logo' => ['nullable', FileRule::types(['svg', 'png', 'jpg', 'jpeg', 'webp'])->extensions(['svg', 'png', 'jpg', 'jpeg', 'webp'])->max('4mb')],
            'site_logo_small' => ['nullable', FileRule::types(['svg', 'png', 'jpg', 'jpeg', 'webp'])->extensions(['svg', 'png', 'jpg', 'jpeg', 'webp'])->max('4mb')],
            'favicon' => ['nullable', FileRule::types(['ico', 'svg', 'png'])->extensions(['ico', 'svg', 'png'])->max('2mb')],
            'apple_touch_icon' => ['nullable', FileRule::types(['png', 'jpg', 'jpeg', 'webp'])->extensions(['png', 'jpg', 'jpeg', 'webp'])->max('4mb')],
            'social_preview_image' => ['nullable', FileRule::types(['png', 'jpg', 'jpeg', 'webp'])->extensions(['png', 'jpg', 'jpeg', 'webp'])->max('6mb')],
            'phone' => ['nullable', 'string', 'max:255'],
            'phone_href' => ['nullable', 'string', 'max:255', 'regex:/^(tel:|https?:\/\/)/i'],
            'emails' => ['nullable', 'string', 'max:4000', $this->emailLinesRule()],
            'lead_notification_email' => ['nullable', 'email', 'max:255'],
            'schedule' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:2000'],
            'map_src' => ['nullable', 'url', 'max:2048'],
            'telegram_url' => ['nullable', 'url', 'max:2048'],
            'max_url' => ['nullable', 'url', 'max:2048'],
            'vk_url' => ['nullable', 'url', 'max:2048'],
            'linkedin_url' => ['nullable', 'url', 'max:2048'],
            'behance_url' => ['nullable', 'url', 'max:2048'],
            'pinterest_url' => ['nullable', 'url', 'max:2048'],
            'seo_title' => ['nullable', 'string', 'max:120'],
            'seo_description' => ['nullable', 'string', 'max:320'],
            'compare_eyebrow' => ['nullable', 'string', 'max:255'],
            'compare_title' => ['nullable', 'string', 'max:255'],
            'compare_text' => ['nullable', 'string', 'max:2000'],
            'animations_enabled' => ['boolean'],
            'smooth_scroll_enabled' => ['boolean'],
            'page_reveal_enabled' => ['boolean'],
            'maintenance_enabled' => ['boolean'],
        ];
    }

    public function validationMessages(): array
    {
        return [
            'site_name.required' => 'Укажите название сайта.',
            'site_name.max' => 'Название сайта должно быть короче 255 символов.',
            'phone_href.regex' => 'Ссылка для звонка должна начинаться с tel:, http:// или https://.',
            'map_src.url' => 'Для карты нужна обычная ссылка, а не HTML-код.',
            '*.url' => 'Проверьте ссылку: она должна открываться в браузере.',
            'seo_title.max' => 'SEO-заголовок должен быть короче 120 символов.',
            'seo_description.max' => 'SEO-описание должно быть короче 320 символов.',
        ];
    }

    /**
     * @return list<ComponentContract>
     */
    protected function getTopButtons(): array
    {
        return [];
    }

    private function sectionNote(string $title, string $text): ComponentContract
    {
        return FlexibleRender::make(sprintf(
            '<div class="settings-note"><strong>%s</strong><span>%s</span></div>',
            e($title),
            e($text),
        ));
    }

    private function overviewHtml(): string
    {
        $item = $this->getResource()->getItem();
        $siteName = e($item?->site_name ?: 'Сайт без названия');
        $updatedAt = $item?->updated_at?->format('d.m.Y, H:i') ?? 'еще не сохранялись';

        return <<<HTML
        <section class="settings-overview">
            <div class="settings-overview__intro">
                <div class="settings-overview__eyebrow">Общие настройки сайта</div>
                <h1>{$siteName}</h1>
                <p>Здесь меняются контакты, логотипы, иконки браузера, ссылки и текст для поисковых систем.</p>
                <div class="settings-overview__saved">Последнее сохранение: {$updatedAt}</div>
            </div>
            <div class="settings-guide">
                {$this->guideCard('pencil-square', '1. Текст', 'Проверьте название, контакты и SEO.')}
                {$this->guideCard('photo', '2. Файлы', 'Загрузите логотипы и иконки сайта.')}
                {$this->guideCard('check-circle', '3. Сохранить', 'Исправьте подсвеченные ошибки и сохраните форму.')}
            </div>
        </section>
        HTML;
    }

    private function guideCard(string $icon, string $title, string $text): string
    {
        $iconHtml = (string) Icon::make($icon, 6)->render();

        return sprintf(
            '<div class="settings-guide__card"><div class="settings-guide__icon">%s</div><strong>%s</strong><span>%s</span></div>',
            $iconHtml,
            e($title),
            e($text),
        );
    }

    private function emailLinesRule(): Closure
    {
        return static function (string $attribute, mixed $value, Closure $fail): void {
            $lines = preg_split('/\R/u', (string) $value) ?: [];

            foreach ($lines as $index => $line) {
                $line = trim($line);

                if ($line === '') {
                    continue;
                }

                $parts = preg_split('/\s+-\s+/', $line, 2) ?: [];
                $email = trim((string) ($parts[0] ?? ''));

                if (filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
                    continue;
                }

                $lineNumber = $index + 1;
                $fail("В строке {$lineNumber} сначала укажите корректный email.");

                return;
            }
        };
    }
}
