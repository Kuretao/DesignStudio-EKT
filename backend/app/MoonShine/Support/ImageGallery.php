<?php

declare(strict_types=1);

namespace App\MoonShine\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class ImageGallery
{
    private const IMAGE_EXTENSIONS = [
        'avif',
        'gif',
        'jpeg',
        'jpg',
        'png',
        'svg',
        'webp',
    ];

    private const VIDEO_EXTENSIONS = [
        'mov',
        'mp4',
        'webm',
    ];

    public const MEDIA_EXTENSIONS = [
        'avif',
        'gif',
        'jpeg',
        'jpg',
        'mov',
        'mp4',
        'png',
        'svg',
        'webm',
        'webp',
    ];

    /**
     * @return Collection<int, array{
     *     path: string,
     *     url: string,
     *     name: string,
     *     directory: string,
     *     size: int,
     *     updatedAt: int,
     *     updatedAtLabel: string,
     *     type: string
     * }>
     */
    public static function items(): Collection
    {
        $disk = Storage::disk('public');

        return collect($disk->allFiles())
            ->filter(static function (string $path): bool {
                $extension = Str::lower(pathinfo($path, PATHINFO_EXTENSION));

                return ! str_starts_with($path, '.thumbnails/')
                    && in_array($extension, self::MEDIA_EXTENSIONS, true);
            })
            ->map(static function (string $path) use ($disk): array {
                $timestamp = $disk->lastModified($path);
                $extension = Str::lower(pathinfo($path, PATHINFO_EXTENSION));
                $type = in_array($extension, self::VIDEO_EXTENSIONS, true) ? 'video' : 'image';
                $thumbnailPath = $type === 'image' ? self::ensureThumbnail($path) : null;

                return [
                    'path' => $path,
                    'url' => $disk->url($path),
                    'thumbUrl' => $thumbnailPath ? $disk->url($thumbnailPath) : $disk->url($path),
                    'name' => basename($path),
                    'directory' => trim(dirname($path), '.'),
                    'size' => $disk->size($path),
                    'updatedAt' => $timestamp,
                    'updatedAtLabel' => date('d.m.Y H:i', $timestamp),
                    'type' => $type,
                ];
            })
            ->sortByDesc('updatedAt')
            ->values();
    }

    public static function ensureThumbnail(string $path): ?string
    {
        $disk = Storage::disk('public');
        $extension = Str::lower(pathinfo($path, PATHINFO_EXTENSION));

        if (
            ! in_array($extension, self::IMAGE_EXTENSIONS, true)
            || in_array($extension, ['gif', 'svg'], true)
            || ! function_exists('imagewebp')
            || ! $disk->exists($path)
        ) {
            return null;
        }

        $thumbnailPath = self::thumbnailPath($path);

        if ($disk->exists($thumbnailPath)) {
            return $thumbnailPath;
        }

        $source = @imagecreatefromstring($disk->get($path));

        if (! $source) {
            return null;
        }

        $width = imagesx($source);
        $height = imagesy($source);

        if ($width <= 0 || $height <= 0) {
            imagedestroy($source);

            return null;
        }

        $maxSide = 420;
        $scale = min(1, $maxSide / max($width, $height));
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));
        $target = imagecreatetruecolor($targetWidth, $targetHeight);

        imagealphablending($target, false);
        imagesavealpha($target, true);
        $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
        imagefill($target, 0, 0, $transparent);
        imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        $temp = tmpfile();
        $meta = $temp ? stream_get_meta_data($temp) : null;
        $tempPath = $meta['uri'] ?? null;

        if (! $tempPath || ! imagewebp($target, $tempPath, 62)) {
            imagedestroy($source);
            imagedestroy($target);

            if ($temp) {
                fclose($temp);
            }

            return null;
        }

        $contents = file_get_contents($tempPath);

        if ($contents === false) {
            imagedestroy($source);
            imagedestroy($target);
            fclose($temp);

            return null;
        }

        $disk->put($thumbnailPath, $contents);

        imagedestroy($source);
        imagedestroy($target);
        fclose($temp);

        return $thumbnailPath;
    }

    public static function thumbnailPath(string $path): string
    {
        $directory = trim(dirname($path), '.');
        $filename = pathinfo($path, PATHINFO_FILENAME);
        $prefix = $directory !== '' ? trim($directory, '/') . '/' : '';

        return '.thumbnails/' . $prefix . $filename . '.webp';
    }
}
