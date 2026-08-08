<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\MoonShine\Support\ImageGallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ImageGalleryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'images' => ImageGallery::items(),
        ]);
    }

    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'directory' => ['nullable', 'string', 'max:120'],
            'files' => ['required', 'array', 'min:1', 'max:20'],
            'files.*' => ['required', 'file', 'max:131072'],
        ], [
            'files.required' => 'Выберите хотя бы один файл.',
            'files.*.max' => 'Файл слишком большой. Лимит: 128 МБ.',
        ]);

        $directory = $this->normalizeDirectory((string) $request->input('directory', 'cms'));
        $uploaded = 0;

        foreach ($request->file('files', []) as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $extension = Str::lower($file->getClientOriginalExtension());

            if (! in_array($extension, ImageGallery::MEDIA_EXTENSIONS, true)) {
                throw ValidationException::withMessages([
                    'files' => 'Можно загружать только изображения и видео: ' . implode(', ', ImageGallery::MEDIA_EXTENSIONS) . '.',
                ]);
            }

            $storedPath = $this->storeMediaFile($directory, $file, $extension);

            if ($storedPath !== null) {
                ImageGallery::ensureThumbnail($storedPath);
            }

            $uploaded++;
        }

        return back()->with(
            'gallery_status',
            $uploaded > 0
                ? 'Загружено файлов: ' . $uploaded . '.'
                : 'Файлы не были загружены.'
        );
    }

    public function destroy(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'path' => ['required', 'string', 'max:2048'],
        ]);

        $path = $this->normalizePublicPath((string) $data['path']);
        $disk = Storage::disk('public');

        if ($path === null || ! in_array(Str::lower(pathinfo($path, PATHINFO_EXTENSION)), ImageGallery::MEDIA_EXTENSIONS, true)) {
            return $this->deleteResponse($request, false, 'Нельзя удалить этот файл из галереи.');
        }

        if (! $disk->exists($path)) {
            return $this->deleteResponse($request, false, 'Файл уже не найден в галерее.');
        }

        $disk->delete($path);
        $disk->delete(ImageGallery::thumbnailPath($path));

        return $this->deleteResponse($request, true, 'Файл удален из галереи.');
    }

    private function normalizeDirectory(string $directory): string
    {
        $segments = collect(preg_split('#[\\\\/]+#', $directory) ?: [])
            ->map(static fn (string $segment): string => Str::slug($segment, '-'))
            ->filter()
            ->take(3)
            ->values();

        return $segments->isNotEmpty() ? $segments->implode('/') : 'cms';
    }

    private function normalizePublicPath(string $path): ?string
    {
        $value = trim(str_replace('\\', '/', $path));
        $value = preg_replace('#^https?://[^/]+/storage/#i', '', $value) ?? $value;
        $value = preg_replace('#^/?storage/#i', '', $value) ?? $value;
        $value = ltrim($value, '/');

        if ($value === '' || str_contains($value, '..') || str_starts_with($value, '/')) {
            return null;
        }

        return $value;
    }

    private function storeMediaFile(string $directory, UploadedFile $file, string $extension): ?string
    {
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'avif'], true) && function_exists('imagewebp')) {
            $compressedPath = $this->storeCompressedImage($directory, $file);

            if ($compressedPath !== null) {
                return $compressedPath;
            }
        }

        return Storage::disk('public')->putFileAs($directory, $file, $this->filename($file, $extension));
    }

    private function storeCompressedImage(string $directory, UploadedFile $file): ?string
    {
        $source = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));

        if (! $source) {
            return null;
        }

        $width = imagesx($source);
        $height = imagesy($source);

        if ($width <= 0 || $height <= 0) {
            imagedestroy($source);

            return null;
        }

        $maxSide = 1920;
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

        if (! $tempPath || ! imagewebp($target, $tempPath, 78)) {
            imagedestroy($source);
            imagedestroy($target);

            if ($temp) {
                fclose($temp);
            }

            return null;
        }

        $path = $directory . '/' . $this->filename($file, 'webp');
        $contents = file_get_contents($tempPath);

        if ($contents === false) {
            imagedestroy($source);
            imagedestroy($target);
            fclose($temp);

            return null;
        }

        Storage::disk('public')->put($path, $contents);

        imagedestroy($source);
        imagedestroy($target);
        fclose($temp);

        return $path;
    }

    private function deleteResponse(Request $request, bool $ok, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'ok' => $ok,
                'message' => $message,
                'images' => ImageGallery::items(),
            ], $ok ? 200 : 422);
        }

        return back()->with('gallery_status', $message);
    }

    private function filename(UploadedFile $file, string $extension): string
    {
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $slug = Str::slug($baseName, '-');

        if ($slug === '') {
            $slug = 'media';
        }

        return $slug . '-' . now()->format('YmdHis') . '-' . Str::lower(Str::random(6)) . '.' . $extension;
    }
}
