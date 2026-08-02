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

                return in_array($extension, self::MEDIA_EXTENSIONS, true);
            })
            ->map(static function (string $path) use ($disk): array {
                $timestamp = $disk->lastModified($path);
                $extension = Str::lower(pathinfo($path, PATHINFO_EXTENSION));

                return [
                    'path' => $path,
                    'url' => $disk->url($path),
                    'name' => basename($path),
                    'directory' => trim(dirname($path), '.'),
                    'size' => $disk->size($path),
                    'updatedAt' => $timestamp,
                    'updatedAtLabel' => date('d.m.Y H:i', $timestamp),
                    'type' => in_array($extension, self::VIDEO_EXTENSIONS, true) ? 'video' : 'image',
                ];
            })
            ->sortByDesc('updatedAt')
            ->values();
    }
}
