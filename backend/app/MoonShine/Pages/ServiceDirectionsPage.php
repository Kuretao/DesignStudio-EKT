<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\Models\MenuItem as NavigationItem;
use App\Models\Service;
use MoonShine\AssetManager\InlineCss;
use MoonShine\AssetManager\InlineJs;
use MoonShine\Contracts\AssetManager\AssetElementContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Pages\Page;
use MoonShine\UI\Components\FlexibleRender;

#[\MoonShine\MenuManager\Attributes\SkipMenu]
class ServiceDirectionsPage extends Page
{
    public function getBreadcrumbs(): array
    {
        return ['#' => $this->getTitle()];
    }

    public function getTitle(): string
    {
        return $this->title ?: 'Направления услуг';
    }

    /**
     * @return list<AssetElementContract>
     */
    protected function assets(): array
    {
        return [
            ...parent::assets(),
            InlineCss::make($this->css()),
            InlineJs::make($this->js()),
        ];
    }

    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
    {
        return [
            FlexibleRender::make($this->html()),
        ];
    }

    private function html(): string
    {
        $directions = NavigationItem::query()
            ->withCount(['children as visible_nav_items_count' => static fn ($query) => $query->where('is_active', true)])
            ->where('menu_area', NavigationItem::AREA_SERVICES)
            ->whereNull('parent_id')
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        $services = Service::query()
            ->with('direction')
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        $directionLandingHrefs = $directions
            ->pluck('href')
            ->filter()
            ->map(static fn (string $href): string => '/' . ltrim($href, '/'))
            ->all();
        $assignableServices = $services
            ->reject(static fn (Service $service): bool => in_array('/' . ltrim((string) $service->slug, '/'), $directionLandingHrefs, true))
            ->values();

        $publishedServices = $assignableServices->where('is_published', true)->count();
        $assignedServices = $assignableServices->whereNotNull('service_direction_id')->count();
        $unassignedServices = $assignableServices
            ->where('is_published', true)
            ->whereNull('service_direction_id')
            ->count();

        $flash = $this->flashHtml();
        $heroPicker = $this->heroPicker($directions, $assignableServices);
        $cards = $directions->isNotEmpty()
            ? $directions->map(fn (NavigationItem $direction): string => $this->directionCard($direction, $assignableServices))->implode('')
            : $this->emptyState();

        return <<<HTML
        <section class="service-directions">
            {$flash}
            <div class="service-directions__hero">
                <div>
                    <p class="service-directions__eyebrow">Страница услуг</p>
                    <h1>Направления услуг</h1>
                    <p>
                        Это нормальный редактор направлений, без ручного меню и родителей. Заполните карточку направления,
                        выберите услуги ниже, сохраните — сайт сам соберет структуру на странице услуг.
                    </p>
                </div>
                <div class="service-directions__stats">
                    {$this->statCard((string) $directions->count(), 'направлений')}
                    {$this->statCard((string) $publishedServices, 'опубликованных услуг')}
                    {$this->statCard((string) $assignedServices, 'услуг распределено')}
                    {$this->statCard((string) $unassignedServices, 'без направления')}
                </div>
            </div>

            {$heroPicker}

            {$this->createForm($assignableServices)}

            <div class="service-directions__grid">
                {$cards}
            </div>
        </section>
        HTML;
    }

    /**
     * @param \Illuminate\Support\Collection<int, NavigationItem> $directions
     * @param \Illuminate\Support\Collection<int, Service> $services
     */
    private function heroPicker($directions, $services): string
    {
        if ($directions->isEmpty()) {
            return '';
        }

        $selectedIds = $directions
            ->where('show_in_services_hero', true)
            ->sortBy('services_hero_position')
            ->pluck('id')
            ->map(static fn ($id): int => (int) $id)
            ->values();

        if ($selectedIds->isEmpty()) {
            $eligibleIds = $services
                ->where('is_published', true)
                ->pluck('service_direction_id')
                ->filter()
                ->unique();
            $selectedIds = $directions
                ->where('is_active', true)
                ->whereIn('id', $eligibleIds)
                ->take(3)
                ->pluck('id')
                ->map(static fn ($id): int => (int) $id)
                ->values();
        }

        $options = $directions->map(function (NavigationItem $direction) use ($services): array {
            $publishedCount = $services
                ->where('is_published', true)
                ->where('service_direction_id', $direction->id)
                ->count();
            $notes = [];

            if (! $direction->is_active) {
                $notes[] = 'скрыто';
            }

            if ($publishedCount === 0) {
                $notes[] = 'нет опубликованных услуг';
            }

            return [
                'id' => (int) $direction->id,
                'label' => $direction->labelRu() . ($notes === [] ? '' : ' (' . implode(', ', $notes) . ')'),
            ];
        });

        $selects = collect(range(0, 2))->map(function (int $index) use ($options, $selectedIds): string {
            $selectedId = (int) ($selectedIds->get($index) ?? 0);
            $optionHtml = '<option value="">Не показывать карточку</option>' . $options
                ->map(static function (array $option) use ($selectedId): string {
                    $selected = $option['id'] === $selectedId ? ' selected' : '';

                    return '<option value="' . $option['id'] . '"' . $selected . '>' . e($option['label']) . '</option>';
                })
                ->implode('');
            $slot = $index + 1;

            return <<<HTML
            <label>
                <span>Карточка {$slot}</span>
                <select name="direction_ids[]" data-hero-direction-select>{$optionHtml}</select>
            </label>
            HTML;
        })->implode('');

        $action = e(route('admin.service-directions.hero'));
        $csrf = csrf_field();
        $put = method_field('PUT');

        return <<<HTML
        <form class="service-directions__hero-picker" method="post" action="{$action}" data-hero-direction-picker>
            {$csrf}
            {$put}
            <div class="service-directions__hero-picker-head">
                <div>
                    <p class="service-directions__eyebrow">Первый экран страницы услуг</p>
                    <h2>Три карточки направлений справа</h2>
                    <span>Выберите состав и порядок карточек. Название, картинка и описание берутся из настроек самого направления ниже.</span>
                </div>
                <button type="submit">Сохранить карточки</button>
            </div>
            <div class="service-directions__hero-picker-grid">{$selects}</div>
            <small>Скрытые направления и направления без опубликованных услуг на сайте не показываются.</small>
        </form>
        HTML;
    }

    /**
     * @param \Illuminate\Support\Collection<int, Service> $services
     */
    private function createForm($services): string
    {
        $action = e(route('admin.service-directions.store'));
        $csrf = csrf_field();
        $position = (int) NavigationItem::query()
            ->where('menu_area', NavigationItem::AREA_SERVICES)
            ->whereNull('parent_id')
            ->max('position') + 10;

        return <<<HTML
        <form class="service-directions__form service-directions__form--create" method="post" action="{$action}" enctype="multipart/form-data">
            {$csrf}
            <div class="service-directions__form-head">
                <div>
                    <p class="service-directions__eyebrow">Новое направление</p>
                    <h2>Создать карточку направления</h2>
                    <span>Например: Архитектурное проектирование, Дизайн интерьера, 3D-визуализация.</span>
                </div>
                <button type="submit">Создать направление</button>
            </div>
            {$this->directionFields(null, $services, $position)}
        </form>
        HTML;
    }

    /**
     * @param \Illuminate\Support\Collection<int, Service> $services
     */
    private function directionCard(NavigationItem $direction, $services): string
    {
        $title = e($direction->labelRu());
        $description = e((string) ($direction->descriptionRu() ?: 'Описание пока не заполнено.'));
        $image = $direction->effective_image;
        $imageHtml = filled($image)
            ? '<img src="' . e((string) $image) . '" alt="" loading="lazy">'
            : '<span>' . e(mb_strtoupper(mb_substr($direction->labelRu() ?: 'D', 0, 1))) . '</span>';
        $updateUrl = e(route('admin.service-directions.update', $direction));
        $deleteUrl = e(route('admin.service-directions.destroy', $direction));
        $csrf = csrf_field();
        $put = method_field('PUT');
        $delete = method_field('DELETE');
        $servicesCount = Service::query()->where('service_direction_id', $direction->getKey())->count();
        $active = $direction->is_active
            ? '<span class="service-directions__badge service-directions__badge--ok">Показывается</span>'
            : '<span class="service-directions__badge">Скрыто</span>';
        $heroBadge = $direction->show_in_services_hero
            ? '<span class="service-directions__badge service-directions__badge--hero">Первый экран #' . (int) $direction->services_hero_position . '</span>'
            : '';

        return <<<HTML
        <article class="service-directions__card">
            <div class="service-directions__summary">
                <div class="service-directions__media">{$imageHtml}</div>
                <div class="service-directions__body">
                    <div class="service-directions__card-top">
                        <span class="service-directions__badge-row">{$active}{$heroBadge}</span>
                        <span class="service-directions__position">#{$direction->position}</span>
                    </div>
                    <h2>{$title}</h2>
                    <p>{$description}</p>
                    <div class="service-directions__meta">
                        <span>{$servicesCount} услуг привязано</span>
                        <span>{$direction->visible_nav_items_count} пунктов на сайте</span>
                    </div>
                </div>
            </div>
            <details class="service-directions__details">
                <summary>Редактировать направление</summary>
                <form class="service-directions__form" method="post" action="{$updateUrl}" enctype="multipart/form-data">
                    {$csrf}
                    {$put}
                    {$this->directionFields($direction, $services, (int) $direction->position)}
                    <div class="service-directions__actions">
                        <button type="submit">Сохранить направление</button>
                    </div>
                </form>
                <form method="post" action="{$deleteUrl}" class="service-directions__delete" onsubmit="return confirm('Удалить направление? Услуги останутся в CMS, но будут без направления.');">
                    {$csrf}
                    {$delete}
                    <button type="submit">Удалить направление</button>
                </form>
            </details>
        </article>
        HTML;
    }

    /**
     * @param \Illuminate\Support\Collection<int, Service> $services
     */
    private function directionFields(?NavigationItem $direction, $services, int $position): string
    {
        $labelRu = e((string) old('label_ru', $direction?->label_ru ?? ''));
        $labelEn = e((string) old('label_en', $direction?->label_en ?? ''));
        $descriptionRu = e((string) old('description_ru', $direction?->description_ru ?? ''));
        $descriptionEn = e((string) old('description_en', $direction?->description_en ?? ''));
        $image = e((string) old('image', $direction?->image ?? ''));
        $imageAltRu = e((string) old('image_alt_ru', $direction?->image_alt_ru ?? ''));
        $imageAltEn = e((string) old('image_alt_en', $direction?->image_alt_en ?? ''));
        $isActive = (bool) old('is_active', $direction?->is_active ?? true);
        $checked = $isActive ? 'checked' : '';
        $filePreview = filled($direction?->effective_image)
            ? '<div class="service-directions__preview"><img src="' . e((string) $direction->effective_image) . '" alt=""><label><input type="checkbox" name="remove_image_file" value="1"> убрать загруженный файл</label></div>'
            : '';
        $selected = $direction
            ? Service::query()->where('service_direction_id', $direction->id)->pluck('id')->map(static fn ($id): int => (int) $id)->all()
            : [];
        $serviceCards = $this->serviceOptions($services, $selected, $direction?->id);
        $selectedCount = count($selected);

        return <<<HTML
        <div class="service-directions__fields">
            <label>
                <span>Название RU *</span>
                <input name="label_ru" value="{$labelRu}" required placeholder="Дизайн интерьера">
            </label>
            <label>
                <span>Название EN</span>
                <input name="label_en" value="{$labelEn}" placeholder="Interior design">
            </label>
            <label class="service-directions__wide">
                <span>Описание RU</span>
                <textarea name="description_ru" rows="3" placeholder="Коротко о направлении для карточки на странице услуг">{$descriptionRu}</textarea>
            </label>
            <label class="service-directions__wide">
                <span>Описание EN</span>
                <textarea name="description_en" rows="3">{$descriptionEn}</textarea>
            </label>
            <label>
                <span>Загрузить картинку</span>
                <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp,image/avif">
            </label>
            <label>
                <span>Или выбрать из галереи / URL</span>
                <textarea name="image" rows="2" data-gallery-lines="1" placeholder="/storage/service-directions/card.webp">{$image}</textarea>
            </label>
            {$filePreview}
            <label>
                <span>Alt картинки RU</span>
                <input name="image_alt_ru" value="{$imageAltRu}">
            </label>
            <label>
                <span>Alt картинки EN</span>
                <input name="image_alt_en" value="{$imageAltEn}">
            </label>
            <label>
                <span>Порядок</span>
                <input type="number" name="position" min="0" max="9999" value="{$position}">
            </label>
            <label class="service-directions__toggle">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {$checked}>
                <span>Показывать направление на сайте</span>
            </label>
            <label class="service-directions__wide">
                <span>Какие услуги входят в направление</span>
                <div class="service-picker" data-service-picker>
                    <div class="service-picker__top">
                        <input type="search" data-service-search placeholder="Найти услугу по названию или текущему направлению">
                        <span data-service-counter>{$selectedCount} выбрано</span>
                    </div>
                    <div class="service-picker__list">
                        {$serviceCards}
                    </div>
                </div>
                <small>Можно выбрать несколько. Если услуга была в другом направлении, она аккуратно переедет сюда.</small>
            </label>
        </div>
        HTML;
    }

    /**
     * @param \Illuminate\Support\Collection<int, Service> $services
     * @param array<int, int> $selected
     */
    private function serviceOptions($services, array $selected, int|string|null $currentDirectionId): string
    {
        return $services
            ->sortBy([
                static fn (Service $service): int => in_array((int) $service->id, $selected, true) ? 0 : 1,
                static fn (Service $service): string => mb_strtolower($service->fieldRu('title') ?: $service->slug ?: ''),
            ])
            ->map(function (Service $service) use ($selected, $currentDirectionId): string {
                $id = (int) $service->id;
                $title = $service->fieldRu('title') ?: $service->slug ?: ('Услуга #' . $id);
                $direction = $service->direction?->labelRu();
                $isSelected = in_array($id, $selected, true);
                $checked = $isSelected ? 'checked' : '';
                $publishedBadge = $service->is_published
                    ? '<span class="service-picker__badge service-picker__badge--ok">опубликована</span>'
                    : '<span class="service-picker__badge">черновик</span>';
                $directionBadge = match (true) {
                    $isSelected => '<span class="service-picker__badge service-picker__badge--current">уже здесь</span>',
                    filled($service->service_direction_id) && (string) $service->service_direction_id !== (string) $currentDirectionId => '<span class="service-picker__badge service-picker__badge--move">сейчас: ' . e((string) $direction) . '</span>',
                    default => '<span class="service-picker__badge">без направления</span>',
                };
                $search = e(mb_strtolower($title . ' ' . (string) $direction . ' ' . (string) $service->slug));

                return sprintf(
                    '<label class="service-picker__item" data-service-card data-search="%s"><input type="checkbox" name="service_ids[]" value="%d" %s><span class="service-picker__check"></span><span class="service-picker__text"><strong>%s</strong><small>/%s</small></span><span class="service-picker__badges">%s%s</span></label>',
                    $search,
                    $id,
                    $checked,
                    e($title),
                    e((string) $service->slug),
                    $directionBadge,
                    $publishedBadge,
                );
            })
            ->implode('');
    }

    private function emptyState(): string
    {
        return <<<HTML
        <div class="service-directions__empty">
            <strong>Направлений пока нет</strong>
            <span>Создайте первое направление сверху, затем выберите в нем нужные услуги.</span>
        </div>
        HTML;
    }

    private function statCard(string $value, string $label): string
    {
        return '<div class="service-directions__stat"><strong>' . e($value) . '</strong><span>' . e($label) . '</span></div>';
    }

    private function flashHtml(): string
    {
        $success = session('success');
        $errors = session('errors');
        $html = '';

        if (filled($success)) {
            $html .= '<div class="service-directions__flash service-directions__flash--ok">' . e((string) $success) . '</div>';
        }

        if ($errors?->any()) {
            $items = collect($errors->all())
                ->map(static fn (string $error): string => '<li>' . e($error) . '</li>')
                ->implode('');
            $html .= '<div class="service-directions__flash service-directions__flash--error"><strong>Не сохранилось:</strong><ul>' . $items . '</ul></div>';
        }

        return $html;
    }

    private function js(): string
    {
        return <<<'JS'
        (() => {
          const refreshPicker = (picker) => {
            const checked = picker.querySelectorAll('input[type="checkbox"]:checked').length;
            const counter = picker.querySelector('[data-service-counter]');
            if (counter) counter.textContent = `${checked} выбрано`;
          };

          document.querySelectorAll('[data-service-picker]').forEach((picker) => {
            const search = picker.querySelector('[data-service-search]');
            const cards = Array.from(picker.querySelectorAll('[data-service-card]'));

            search?.addEventListener('input', () => {
              const value = search.value.trim().toLowerCase();
              cards.forEach((card) => {
                card.hidden = value !== '' && !String(card.dataset.search || '').includes(value);
              });
            });

            picker.addEventListener('change', (event) => {
              if (event.target?.matches?.('input[type="checkbox"]')) {
                refreshPicker(picker);
              }
            });

            refreshPicker(picker);
          });

          document.querySelectorAll('[data-hero-direction-picker]').forEach((picker) => {
            const selects = Array.from(picker.querySelectorAll('[data-hero-direction-select]'));
            const refreshOptions = () => {
              const selected = selects.map((select) => select.value).filter(Boolean);
              selects.forEach((select) => {
                Array.from(select.options).forEach((option) => {
                  option.disabled = option.value !== '' && option.value !== select.value && selected.includes(option.value);
                });
              });
            };

            picker.addEventListener('change', refreshOptions);
            refreshOptions();
          });
        })();
        JS;
    }

    private function css(): string
    {
        return <<<'CSS'
        .service-directions { display: grid; gap: 24px; }
        .service-directions__hero,
        .service-directions__form,
        .service-directions__hero-picker,
        .service-directions__card,
        .service-directions__empty,
        .service-directions__flash { border: 1px solid rgba(148, 163, 184, .24); border-radius: 16px; background: rgba(15, 23, 42, .62); }
        .service-directions__hero { display: grid; grid-template-columns: minmax(0, 1.35fr) minmax(320px, .65fr); gap: 24px; padding: 28px; background: linear-gradient(135deg, rgba(16, 185, 129, .14), rgba(59, 130, 246, .12)); }
        .service-directions__eyebrow { margin: 0 0 10px; color: #6ee7b7; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; }
        .service-directions h1, .service-directions h2 { margin: 0; line-height: 1.04; }
        .service-directions h1 { font-size: clamp(30px, 4vw, 54px); }
        .service-directions h2 { font-size: 24px; }
        .service-directions__hero p { max-width: 760px; margin: 18px 0 0; color: #dbeafe; line-height: 1.65; }
        .service-directions__stats { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .service-directions__stat { padding: 18px; border: 1px solid rgba(148, 163, 184, .22); border-radius: 14px; background: rgba(15, 23, 42, .58); }
        .service-directions__stat strong { display: block; font-size: 30px; color: #fff; }
        .service-directions__stat span { color: #cbd5e1; font-size: 13px; }
        .service-directions__form { display: grid; gap: 18px; padding: 20px; }
        .service-directions__hero-picker { display: grid; gap: 18px; padding: 20px; border-color: rgba(214, 154, 102, .42); background: rgba(88, 51, 28, .18); }
        .service-directions__hero-picker-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 18px; }
        .service-directions__hero-picker-head span, .service-directions__hero-picker > small { color: #cbd5e1; }
        .service-directions__hero-picker-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
        .service-directions__hero-picker-grid label { display: grid; gap: 7px; color: #f8fafc; font-size: 13px; font-weight: 800; }
        .service-directions__hero-picker-grid select { width: 100%; min-width: 0; border: 1px solid rgba(214, 154, 102, .36); border-radius: 10px; background: rgba(2, 6, 23, .72); color: #fff; padding: 12px; font: inherit; }
        .service-directions__hero-picker-grid option { background: #111827; color: #fff; }
        .service-directions__form--create { border-color: rgba(16, 185, 129, .36); background: rgba(6, 78, 59, .22); }
        .service-directions__form-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; }
        .service-directions__form-head span,
        .service-directions__fields small { color: #cbd5e1; }
        .service-directions__fields { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
        .service-directions__fields label { display: grid; gap: 7px; color: #f8fafc; font-weight: 800; font-size: 13px; }
        .service-directions__fields input,
        .service-directions__fields textarea,
        .service-directions__fields select,
        .service-picker__top input { width: 100%; border: 1px solid rgba(148, 163, 184, .28); border-radius: 10px; background: rgba(2, 6, 23, .58); color: #fff; padding: 11px 12px; font: inherit; }
        .service-directions__fields select option { background: #111827; color: #fff; }
        .service-directions__wide { grid-column: 1 / -1; }
        .service-directions__toggle { align-self: end; display: flex !important; grid-template-columns: auto 1fr; align-items: center; }
        .service-directions__toggle input[type="checkbox"] { width: 20px; height: 20px; }
        .service-picker { display: grid; gap: 12px; padding: 12px; border: 1px solid rgba(167, 139, 250, .28); border-radius: 14px; background: rgba(2, 6, 23, .30); }
        .service-picker__top { display: grid; grid-template-columns: minmax(0, 1fr) auto; align-items: center; gap: 10px; }
        .service-picker__top span { min-width: 104px; border-radius: 999px; background: rgba(167, 139, 250, .18); color: #ddd6fe; padding: 9px 12px; text-align: center; font-size: 12px; font-weight: 900; }
        .service-picker__list { display: grid; max-height: 360px; overflow: auto; gap: 8px; padding-right: 4px; }
        .service-picker__item { display: grid !important; grid-template-columns: auto auto minmax(0, 1fr) auto; align-items: center; gap: 12px !important; padding: 12px; border: 1px solid rgba(148, 163, 184, .18); border-radius: 12px; background: rgba(15, 23, 42, .62); cursor: pointer; transition: border-color .18s ease, background .18s ease; }
        .service-picker__item:hover { border-color: rgba(167, 139, 250, .55); background: rgba(30, 41, 59, .72); }
        .service-picker__item input { position: absolute; opacity: 0; pointer-events: none; }
        .service-picker__check { width: 22px; height: 22px; border: 1px solid rgba(148, 163, 184, .46); border-radius: 7px; background: rgba(2, 6, 23, .72); }
        .service-picker__item:has(input:checked) { border-color: rgba(16, 185, 129, .58); background: rgba(6, 78, 59, .24); }
        .service-picker__item:has(input:checked) .service-picker__check { border-color: #34d399; background: #10b981; box-shadow: inset 0 0 0 5px rgba(2, 6, 23, .18); }
        .service-picker__text { min-width: 0; display: grid; gap: 3px; }
        .service-picker__text strong { color: #fff; overflow-wrap: anywhere; }
        .service-picker__text small { color: #94a3b8; overflow-wrap: anywhere; }
        .service-picker__badges { display: flex; flex-wrap: wrap; justify-content: flex-end; gap: 6px; }
        .service-picker__badge { border-radius: 999px; background: rgba(148, 163, 184, .14); color: #cbd5e1; padding: 5px 8px; font-size: 11px; font-weight: 900; white-space: nowrap; }
        .service-picker__badge--ok { background: rgba(16, 185, 129, .16); color: #a7f3d0; }
        .service-picker__badge--current { background: rgba(16, 185, 129, .24); color: #bbf7d0; }
        .service-picker__badge--move { background: rgba(245, 158, 11, .18); color: #fde68a; }
        .service-directions__grid { display: grid; gap: 18px; }
        .service-directions__card { overflow: hidden; }
        .service-directions__summary { display: grid; grid-template-columns: 240px minmax(0, 1fr); }
        .service-directions__media { display: grid; place-items: center; min-height: 190px; background: rgba(2, 6, 23, .45); color: #c4b5fd; font-size: 44px; font-weight: 800; }
        .service-directions__media img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .service-directions__body { display: grid; gap: 12px; padding: 18px; }
        .service-directions__body p { margin: 0; color: #cbd5e1; line-height: 1.55; }
        .service-directions__card-top, .service-directions__meta { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; justify-content: space-between; }
        .service-directions__badge, .service-directions__position, .service-directions__meta span { padding: 6px 10px; border-radius: 999px; background: rgba(148, 163, 184, .13); color: #cbd5e1; font-size: 12px; font-weight: 700; }
        .service-directions__badge--ok { background: rgba(16, 185, 129, .18); color: #a7f3d0; }
        .service-directions__badge--hero { background: rgba(214, 154, 102, .18); color: #fed7aa; }
        .service-directions__badge-row { display: flex; flex-wrap: wrap; gap: 8px; }
        .service-directions__details { border-top: 1px solid rgba(148, 163, 184, .18); }
        .service-directions__details summary { cursor: pointer; padding: 16px 18px; color: #c4b5fd; font-weight: 900; }
        .service-directions__details .service-directions__form { border: 0; border-radius: 0; background: rgba(2, 6, 23, .18); }
        .service-directions__actions { display: flex; justify-content: flex-end; }
        .service-directions button { min-height: 42px; border: 0; border-radius: 10px; background: #a78bfa; color: #140b2d; font-weight: 900; padding: 0 16px; cursor: pointer; }
        .service-directions__delete { padding: 0 20px 20px; }
        .service-directions__delete button { background: rgba(185, 28, 28, .92); color: #fff; }
        .service-directions__preview { grid-column: 1 / -1; display: flex; align-items: center; gap: 12px; color: #cbd5e1; }
        .service-directions__preview img { width: 120px; height: 72px; object-fit: cover; border-radius: 10px; border: 1px solid rgba(148, 163, 184, .24); }
        .service-directions__empty { display: grid; gap: 6px; padding: 28px; color: #cbd5e1; }
        .service-directions__empty strong { color: #fff; font-size: 20px; }
        .service-directions__flash { padding: 14px 18px; }
        .service-directions__flash--ok { border-color: rgba(16, 185, 129, .42); color: #a7f3d0; background: rgba(6, 78, 59, .22); }
        .service-directions__flash--error { border-color: rgba(239, 68, 68, .46); color: #fecaca; background: rgba(127, 29, 29, .24); }
        .service-directions__flash ul { margin: 8px 0 0; padding-left: 18px; }
        @media (max-width: 900px) {
          .service-directions__hero,
          .service-directions__summary,
          .service-directions__fields,
          .service-directions__hero-picker-grid { grid-template-columns: 1fr; }
          .service-directions__form-head,
          .service-directions__hero-picker-head { display: grid; }
          .service-picker__top,
          .service-picker__item { grid-template-columns: 1fr; }
          .service-picker__badges { justify-content: flex-start; }
        }
        CSS;
    }
}
