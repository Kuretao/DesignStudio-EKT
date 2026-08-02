<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\SiteSetting\Pages;

use App\MoonShine\Resources\SiteSetting\SiteSettingResource;
use Closure;
use Illuminate\Validation\Rules\File as FileRule;
use MoonShine\AssetManager\InlineCss;
use MoonShine\Contracts\AssetManager\AssetElementContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Crud\Components\Fragment;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Components\Alert;
use MoonShine\UI\Components\FlexibleRender;
use MoonShine\UI\Components\Icon;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Url;

/**
 * @extends FormPage<SiteSettingResource>
 */
class SocialLinksFormPage extends FormPage
{
    public function getTitle(): string
    {
        return 'Мессенджеры и соцсети';
    }

    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle(),
        ];
    }

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
                ->content('Эти ссылки используются в подвале сайта, на странице контактов и в быстрых кнопках связи. Пустые поля не показываются посетителям.'),
            Box::make('Каналы связи', [
                Grid::make([
                    Column::make([
                        $this->channelNote('paper-airplane', 'Telegram', 'Чат, канал или профиль для быстрых обращений.'),
                        Url::make('Telegram', 'telegram_url')
                            ->placeholder('https://t.me/company')
                            ->hint('Ссылка на чат, канал или профиль Telegram.'),
                    ])->columnSpan(6),
                    Column::make([
                        $this->channelNote('chat-bubble-left-right', 'MAX', 'Профиль или страница компании в мессенджере MAX.'),
                        Url::make('MAX', 'max_url')
                            ->placeholder('https://max.ru/company')
                            ->hint('Ссылка на страницу в MAX.'),
                    ])->columnSpan(6),
                    Column::make([
                        $this->channelNote('phone', 'Звонок', 'Кнопка звонка в контактах и формах обратной связи.'),
                        Text::make('Ссылка для звонка', 'phone_href')
                            ->placeholder('tel:+79879421242')
                            ->hint('Для звонка с телефона начните значение с tel:. Также допустимы http:// и https://.'),
                    ])->columnSpan(6),
                    Column::make([
                        $this->channelNote('users', 'VK', 'Сообщество или профиль, который показывается и как мессенджер, и как соцсеть.'),
                        Url::make('VK', 'vk_url')
                            ->placeholder('https://vk.com/company')
                            ->hint('Ссылка на сообщество или профиль VK.'),
                    ])->columnSpan(6),
                ]),
            ])->icon('chat-bubble-left-right')->customAttributes(['class' => 'settings-section social-form-section']),
            Box::make('Социальные сети в подвале', [
                Grid::make([
                    Column::make([
                        $this->channelNote('briefcase', 'LinkedIn', 'Профессиональный профиль или страница студии.'),
                        Url::make('LinkedIn', 'linkedin_url')
                            ->placeholder('https://www.linkedin.com/in/company')
                            ->hint('Оставьте пустым, если ссылка не нужна на сайте.'),
                    ])->columnSpan(4),
                    Column::make([
                        $this->channelNote('sparkles', 'Behance', 'Портфолио проектов и визуальных кейсов.'),
                        Url::make('Behance', 'behance_url')
                            ->placeholder('https://www.behance.net/company')
                            ->hint('Оставьте пустым, если портфолио не ведется в Behance.'),
                    ])->columnSpan(4),
                    Column::make([
                        $this->channelNote('squares-2x2', 'Pinterest', 'Подборки, идеи и визуальные референсы студии.'),
                        Url::make('Pinterest', 'pinterest_url')
                            ->placeholder('https://pinterest.com/company')
                            ->hint('Оставьте пустым, если Pinterest не используется.'),
                    ])->columnSpan(4),
                ]),
            ])->icon('share')->customAttributes(['class' => 'settings-section social-form-section']),
        ];
    }

    public function getFormComponent(bool $withoutFragment = false): ComponentContract
    {
        $resource = $this->getResource();
        $item = $resource->getCastedData();
        $fields = $this->getFields()->formFields();
        $component = $this->getForm(
            $this->getFormAction(),
            $item,
            $fields,
            $this->isAsync(),
        );

        if ($withoutFragment) {
            return $component;
        }

        return Fragment::make([$component])
            ->name('crud-form')
            ->updateWith(['resourceItem' => $resource->getItemID()]);
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'site_name' => ['nullable', 'string', 'max:255'],
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
            'animations_enabled' => ['boolean'],
            'smooth_scroll_enabled' => ['boolean'],
            'page_reveal_enabled' => ['boolean'],
        ];
    }

    public function validationMessages(): array
    {
        return [
            'phone_href.regex' => 'Ссылка для звонка должна начинаться с tel:, http:// или https://.',
            '*.url' => 'Проверьте ссылку: она должна открываться в браузере.',
        ];
    }

    /**
     * @return list<ComponentContract>
     */
    protected function getTopButtons(): array
    {
        return [];
    }

    private function overviewHtml(): string
    {
        $item = $this->getResource()->getItem();
        $updatedAt = $item?->updated_at?->format('d.m.Y, H:i') ?? 'еще не сохранялись';

        return <<<HTML
        <section class="social-overview">
            <div class="social-overview__copy">
                <div class="social-overview__eyebrow">Контакты сайта</div>
                <h1>Мессенджеры и соцсети</h1>
                <p>Управляйте каналами, которые посетители видят в подвале, контактах и быстрых кнопках связи. Заполненные ссылки сразу попадают в API сайта после сохранения.</p>
                <div class="social-overview__saved">Последнее сохранение: {$updatedAt}</div>
            </div>
            <div class="social-overview__grid">
                {$this->statusCard('paper-airplane', 'Telegram', $item?->telegram_url)}
                {$this->statusCard('chat-bubble-left-right', 'MAX', $item?->max_url)}
                {$this->statusCard('users', 'VK', $item?->vk_url)}
                {$this->statusCard('phone', 'Звонок', $item?->phone_href)}
                {$this->statusCard('briefcase', 'LinkedIn', $item?->linkedin_url)}
                {$this->statusCard('sparkles', 'Behance', $item?->behance_url)}
                {$this->statusCard('squares-2x2', 'Pinterest', $item?->pinterest_url)}
            </div>
        </section>
        HTML;
    }

    private function statusCard(string $icon, string $label, ?string $value): string
    {
        $iconHtml = (string) Icon::make($icon, 6)->render();
        $isFilled = filled($value);
        $stateClass = $isFilled ? 'is-active' : 'is-empty';
        $stateText = $isFilled ? 'Заполнено' : 'Не показано';
        $valueText = $isFilled ? e($value) : 'Добавьте ссылку ниже';

        return <<<HTML
        <article class="social-status {$stateClass}">
            <div class="social-status__icon">{$iconHtml}</div>
            <div>
                <strong>{$label}</strong>
                <span>{$stateText}</span>
            </div>
            <p>{$valueText}</p>
        </article>
        HTML;
    }

    private function channelNote(string $icon, string $title, string $text): ComponentContract
    {
        $iconHtml = (string) Icon::make($icon, 6)->render();

        return FlexibleRender::make(sprintf(
            '<div class="social-channel-note"><div class="social-channel-note__icon">%s</div><div><strong>%s</strong><span>%s</span></div></div>',
            $iconHtml,
            e($title),
            e($text),
        ));
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
