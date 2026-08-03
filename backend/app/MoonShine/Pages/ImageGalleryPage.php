<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\MoonShine\Support\ImageGallery;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Pages\Page;
use MoonShine\UI\Components\FlexibleRender;

#[\MoonShine\MenuManager\Attributes\SkipMenu]
class ImageGalleryPage extends Page
{
    public function getBreadcrumbs(): array
    {
        return ['#' => $this->getTitle()];
    }

    public function getTitle(): string
    {
        return $this->title ?: 'Галерея';
    }

    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
    {
        return [
            FlexibleRender::make($this->buildGalleryHtml()),
        ];
    }

    private function buildGalleryHtml(): string
    {
        $media = ImageGallery::items();
        $totalSize = (int) $media->sum('size');
        $directories = $media
            ->pluck('directory')
            ->map(static fn (string $directory): string => $directory !== '' ? $directory : 'Корень')
            ->unique()
            ->sort()
            ->values();

        $directoryChips = $directories->isNotEmpty()
            ? $directories->map(static fn (string $directory): string => '<span class="gallery-page__chip">' . e($directory) . '</span>')->implode('')
            : '<span class="gallery-page__chip">Папок пока нет</span>';

        $deleteUrl = e(route('admin.image-gallery.destroy'));
        $cards = $media->isNotEmpty()
            ? $media->map(fn (array $image): string => $this->imageCard($image, $deleteUrl))->implode('')
            : $this->emptyState();

        $imagesCount = $media->where('type', 'image')->count();
        $videosCount = $media->where('type', 'video')->count();
        $directoriesCount = $directories->count();
        $formattedSize = $this->formatSize($totalSize);
        $uploadUrl = e(route('admin.image-gallery.upload'));
        $csrf = csrf_field();
        $uploadFeedback = $this->uploadFeedback();

        $csrf = csrf_field();
        $method = method_field('DELETE');

        return <<<HTML
        <section class="gallery-page">
            <div class="gallery-page__hero">
                <div class="gallery-page__intro">
                    <p class="gallery-page__eyebrow">Медиафайлы</p>
                    <h1>Галерея медиа</h1>
                    <p>Изображения и видео из публичного хранилища. Фотографии можно выбирать в формах услуг, новостей, акций и других разделов; видео хранится здесь и доступно по пути.</p>
                </div>

                <div class="gallery-page__stats" aria-label="Статистика галереи">
                    <div class="gallery-page__stat">
                        <span>{$imagesCount}</span>
                        <strong>изображений</strong>
                    </div>
                    <div class="gallery-page__stat">
                        <span>{$videosCount}</span>
                        <strong>видео</strong>
                    </div>
                    <div class="gallery-page__stat">
                        <span>{$directoriesCount}</span>
                        <strong>папок</strong>
                    </div>
                    <div class="gallery-page__stat">
                        <span>{$formattedSize}</span>
                        <strong>общий размер</strong>
                    </div>
                </div>
            </div>

            <form class="gallery-page-upload" action="{$uploadUrl}" method="post" enctype="multipart/form-data">
                {$csrf}
                <div class="gallery-page-upload__main">
                    <div>
                        <strong>Загрузить фото или видео</strong>
                        <span>Поддерживаются jpg, png, webp, avif, gif, svg, mp4, webm и mov. Можно выбрать несколько файлов сразу.</span>
                    </div>
                    <label>
                        <span>Папка</span>
                        <input type="text" name="directory" value="cms" placeholder="services или portfolio/hero">
                    </label>
                    <label>
                        <span>Файлы</span>
                        <input type="file" name="files[]" multiple accept="image/*,video/mp4,video/webm,video/quicktime,.webp,.avif,.svg,.mp4,.webm,.mov">
                    </label>
                    <button type="submit">Загрузить</button>
                </div>
                {$uploadFeedback}
            </form>

            <div class="gallery-page__folders">
                {$directoryChips}
            </div>

            <div class="gallery-page__grid">
                {$cards}
            </div>
        </section>
        HTML;
    }

    /**
     * @param array{
     *     path: string,
     *     url: string,
     *     name: string,
     *     directory: string,
     *     size: int,
     *     updatedAt: int,
     *     updatedAtLabel: string,
     *     type: string
     * } $media
     */
    private function imageCard(array $media, string $deleteUrl): string
    {
        $name = e($media['name']);
        $path = e($media['path']);
        $url = e($media['url']);
        $directory = e($media['directory'] !== '' ? $media['directory'] : 'Корень');
        $meta = e($this->formatSize((int) $media['size']) . ' · ' . $media['updatedAtLabel']);
        $type = ($media['type'] ?? 'image') === 'video' ? 'video' : 'image';
        $typeLabel = $type === 'video' ? 'Видео' : 'Изображение';
        $preview = $type === 'video'
            ? '<video src="' . $url . '" muted preload="metadata"></video><span class="gallery-page-card__type">Видео</span>'
            : '<img src="' . $url . '" alt="' . $name . '" loading="lazy"><span class="gallery-page-card__type">Фото</span>';

        return <<<HTML
        <article class="gallery-page-card">
            <a class="gallery-page-card__preview" href="{$url}" target="_blank" rel="noopener">
                {$preview}
            </a>
            <div class="gallery-page-card__body">
                <strong title="{$name}">{$name}</strong>
                <span>{$directory} · {$typeLabel}</span>
                <code title="{$path}">{$path}</code>
                <small>{$meta}</small>
                <form action="{$deleteUrl}" method="post" onsubmit="return confirm('Удалить этот файл из галереи?');">
                    {$csrf}
                    {$method}
                    <input type="hidden" name="path" value="{$path}">
                    <button type="submit" class="gallery-page-card__delete">Удалить</button>
                </form>
            </div>
        </article>
        HTML;
    }

    private function emptyState(): string
    {
        return <<<HTML
        <div class="gallery-page__empty">
            <strong>В галерее пока нет медиафайлов</strong>
            <span>Загрузите фото или видео через форму выше.</span>
        </div>
        HTML;
    }

    private function uploadFeedback(): string
    {
        if (session()->has('gallery_status')) {
            return '<div class="gallery-page-upload__status">' . e((string) session('gallery_status')) . '</div>';
        }

        if ($errors = session('errors')) {
            $messages = collect($errors->all())
                ->map(static fn (string $message): string => '<span>' . e($message) . '</span>')
                ->implode('');

            return '<div class="gallery-page-upload__errors">' . $messages . '</div>';
        }

        return '';
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 Б';
        }

        $units = ['Б', 'КБ', 'МБ', 'ГБ'];
        $index = min((int) floor(log($bytes, 1024)), count($units) - 1);
        $value = $bytes / (1024 ** $index);

        return number_format($value, $index === 0 ? 0 : 1, ',', ' ') . ' ' . $units[$index];
    }
}
