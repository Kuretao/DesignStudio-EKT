<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\Models\UiText;
use App\Support\DefaultUiTexts;
use MoonShine\AssetManager\InlineCss;
use MoonShine\AssetManager\InlineJs;
use MoonShine\Contracts\AssetManager\AssetElementContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Pages\Page;
use MoonShine\UI\Components\FlexibleRender;

#[\MoonShine\MenuManager\Attributes\SkipMenu]
class StyleLabEditorPage extends Page
{
    private array $texts = [];

    public function getBreadcrumbs(): array
    {
        return ['#' => $this->getTitle()];
    }

    public function getTitle(): string
    {
        return $this->title ?: 'Редактор Style Lab';
    }

    /**
     * @return list<AssetElementContract>
     */
    protected function assets(): array
    {
        return [
            ...parent::assets(),
            InlineCss::make((string) file_get_contents(resource_path('css/style-lab-editor-admin.css'))),
            InlineJs::make((string) file_get_contents(resource_path('js/style-lab-editor-admin.js'))),
        ];
    }

    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
    {
        $this->texts = $this->loadTexts();

        return [
            FlexibleRender::make($this->html()),
        ];
    }

    private function html(): string
    {
        $action = e(route('admin.style-lab-editor.update'));
        $csrf = csrf_field();
        $status = session()->has('style_lab_status')
            ? '<div class="sl-editor__status">' . e((string) session('style_lab_status')) . '</div>'
            : '';

        return <<<HTML
        <form class="sl-editor" action="{$action}" method="post" data-style-lab-editor>
            {$csrf}
            <section class="sl-editor__hero">
                <div>
                    <p>Постоянный блок</p>
                    <h1>Редактор Style Lab</h1>
                    <span>Все тексты, картинки, палитры, материалы и световые состояния блока с живым предпросмотром.</span>
                </div>
                <button type="submit">Сохранить Style Lab</button>
            </section>
            {$status}

            <div class="sl-editor__layout">
                <div class="sl-editor__fields">
                    {$this->generalSection()}
                    {$this->stylesSection()}
                    {$this->materialsSection()}
                    {$this->lightsSection()}
                    {$this->buttonsSection()}
                </div>
                {$this->preview()}
            </div>
        </form>
        HTML;
    }

    private function generalSection(): string
    {
        return '<section class="sl-card"><h2>Общее</h2><div class="sl-grid sl-grid--2">'
            . $this->field('Метка RU', 'styleLab.eyebrow', 'ru')
            . $this->field('Метка EN', 'styleLab.eyebrow', 'en')
            . $this->area('Заголовок RU', 'styleLab.title', 'ru')
            . $this->area('Заголовок EN', 'styleLab.title', 'en')
            . $this->area('Описание RU', 'styleLab.text', 'ru')
            . $this->area('Описание EN', 'styleLab.text', 'en')
            . $this->field('Метка брифа RU', 'styleLab.briefLabel', 'ru')
            . $this->field('Метка брифа EN', 'styleLab.briefLabel', 'en')
            . '</div></section>';
    }

    private function stylesSection(): string
    {
        $html = '<section class="sl-card"><h2>Стили</h2>';

        foreach ($this->styles() as $style) {
            $id = $style['id'];
            $html .= '<div class="sl-subcard" data-sl-style-card="' . e($id) . '"><h3>' . e($style['title']) . '</h3><div class="sl-grid sl-grid--2">'
                . $this->field('Название RU', "styleLab.styles.{$id}.label", 'ru')
                . $this->field('Название EN', "styleLab.styles.{$id}.label", 'en')
                . $this->area('Заголовок RU', "styleLab.styles.{$id}.headline", 'ru')
                . $this->area('Заголовок EN', "styleLab.styles.{$id}.headline", 'en')
                . $this->area('Настроение RU', "styleLab.styles.{$id}.mood", 'ru')
                . $this->area('Настроение EN', "styleLab.styles.{$id}.mood", 'en')
                . $this->techArea('Картинка', "styleLab.styles.{$id}.image", true)
                . $this->techArea('Палитра, по одному HEX в строке', "styleLab.styles.{$id}.colors")
                . '</div></div>';
        }

        return $html . '</section>';
    }

    private function materialsSection(): string
    {
        $html = '<section class="sl-card"><h2>Материалы</h2>';

        foreach ($this->materials() as $material) {
            $id = $material['id'];
            $html .= '<div class="sl-subcard"><h3>' . e($material['title']) . '</h3><div class="sl-grid sl-grid--2">'
                . $this->field('Название RU', "styleLab.materials.{$id}.label", 'ru')
                . $this->field('Название EN', "styleLab.materials.{$id}.label", 'en')
                . $this->field('Текстура RU', "styleLab.materials.{$id}.texture", 'ru')
                . $this->field('Текстура EN', "styleLab.materials.{$id}.texture", 'en')
                . $this->techField('Акцентный цвет HEX', "styleLab.materials.{$id}.accent")
                . '</div></div>';
        }

        return $html . '</section>';
    }

    private function lightsSection(): string
    {
        $html = '<section class="sl-card"><h2>Свет</h2>';

        foreach ($this->lights() as $light) {
            $id = $light['id'];
            $html .= '<div class="sl-subcard"><h3>' . e($light['title']) . '</h3><div class="sl-grid sl-grid--2">'
                . $this->field('Название RU', "styleLab.lights.{$id}.label", 'ru')
                . $this->field('Название EN', "styleLab.lights.{$id}.label", 'en')
                . $this->field('Заметка RU', "styleLab.lights.{$id}.note", 'ru')
                . $this->field('Заметка EN', "styleLab.lights.{$id}.note", 'en')
                . $this->techArea('CSS overlay', "styleLab.lights.{$id}.overlay")
                . '</div></div>';
        }

        return $html . '</section>';
    }

    private function buttonsSection(): string
    {
        return '<section class="sl-card"><h2>Кнопки и подписи</h2><div class="sl-grid sl-grid--2">'
            . $this->field('Кнопка сохранить RU', 'styleLab.saveButton', 'ru')
            . $this->field('Кнопка сохранить EN', 'styleLab.saveButton', 'en')
            . $this->field('Кнопка расчета RU', 'styleLab.calculateButton', 'ru')
            . $this->field('Кнопка расчета EN', 'styleLab.calculateButton', 'en')
            . $this->field('Сообщение RU', 'styleLab.savedMessage', 'ru')
            . $this->field('Сообщение EN', 'styleLab.savedMessage', 'en')
            . '</div></section>';
    }

    private function preview(): string
    {
        return <<<HTML
        <aside class="sl-preview" data-sl-preview>
            <div class="sl-preview__stage">
                <img data-sl-preview-bg alt="">
                <div class="sl-preview__shade"></div>
                <div class="sl-preview__light" data-sl-preview-light></div>
                <div class="sl-preview__copy">
                    <p data-sl-preview-eyebrow></p>
                    <h2 data-sl-preview-title></h2>
                    <span data-sl-preview-text></span>
                </div>
                <div class="sl-preview__panel">
                    <div class="sl-preview__swatches" data-sl-preview-swatches></div>
                    <p data-sl-preview-style-label></p>
                    <h3 data-sl-preview-headline></h3>
                    <small data-sl-preview-pill></small>
                </div>
            </div>
            <div class="sl-preview__controls">
                <div data-sl-preview-style-buttons></div>
                <div data-sl-preview-material-buttons></div>
                <div data-sl-preview-light-buttons></div>
            </div>
        </aside>
        HTML;
    }

    private function field(string $label, string $key, string $locale): string
    {
        $value = e($this->value($key, $locale));
        $name = e("texts[{$key}][{$locale}]");

        return "<label class=\"sl-field\"><span>{$label}</span><input name=\"{$name}\" value=\"{$value}\" data-sl-key=\"{$key}\" data-sl-locale=\"{$locale}\"></label>";
    }

    private function area(string $label, string $key, string $locale): string
    {
        $value = e($this->value($key, $locale));
        $name = e("texts[{$key}][{$locale}]");

        return "<label class=\"sl-field\"><span>{$label}</span><textarea name=\"{$name}\" rows=\"3\" data-sl-key=\"{$key}\" data-sl-locale=\"{$locale}\">{$value}</textarea></label>";
    }

    private function techField(string $label, string $key): string
    {
        $value = e($this->value($key, 'ru'));
        $name = e("texts[{$key}][value]");

        return "<label class=\"sl-field\"><span>{$label}</span><input name=\"{$name}\" value=\"{$value}\" data-sl-key=\"{$key}\" data-sl-locale=\"value\"></label>";
    }

    private function techArea(string $label, string $key, bool $gallery = false): string
    {
        $value = e($this->value($key, 'ru'));
        $name = e("texts[{$key}][value]");
        $galleryAttr = $gallery ? ' data-gallery-lines="1"' : '';

        return "<label class=\"sl-field\"><span>{$label}</span><textarea name=\"{$name}\" rows=\"3\" data-sl-key=\"{$key}\" data-sl-locale=\"value\"{$galleryAttr}>{$value}</textarea></label>";
    }

    private function value(string $key, string $locale): string
    {
        $item = $this->texts[$key] ?? null;

        if (! $item) {
            return '';
        }

        return (string) ($locale === 'en' ? ($item['value_en'] ?? '') : ($item['value_ru'] ?? ''));
    }

    private function loadTexts(): array
    {
        $defaults = collect(DefaultUiTexts::rows())
            ->filter(static fn (array $row): bool => str_starts_with((string) $row['key'], 'styleLab.'))
            ->keyBy('key');
        $stored = UiText::query()
            ->whereIn('key', $defaults->keys())
            ->get()
            ->keyBy('key');

        return $defaults->map(static function (array $row, string $key) use ($stored): array {
            $item = $stored->get($key);

            return [
                'value_ru' => $item?->value_ru ?? $row['value_ru'] ?? '',
                'value_en' => $item?->value_en ?? $row['value_en'] ?? '',
            ];
        })->all();
    }

    private function styles(): array
    {
        return [
            ['id' => 'minimal', 'title' => 'Современный'],
            ['id' => 'classic', 'title' => 'Неоклассика'],
            ['id' => 'loft', 'title' => 'Лофт'],
            ['id' => 'eco', 'title' => 'Эко'],
        ];
    }

    private function materials(): array
    {
        return [
            ['id' => 'wood', 'title' => 'Теплое дерево'],
            ['id' => 'stone', 'title' => 'Крупный камень'],
            ['id' => 'textile', 'title' => 'Мягкий текстиль'],
            ['id' => 'metal', 'title' => 'Темный металл'],
        ];
    }

    private function lights(): array
    {
        return [
            ['id' => 'morning', 'title' => 'Утро'],
            ['id' => 'evening', 'title' => 'Вечер'],
            ['id' => 'gallery', 'title' => 'Галерея'],
        ];
    }
}
