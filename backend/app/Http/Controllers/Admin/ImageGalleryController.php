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

            Storage::disk('public')->putFileAs($directory, $file, $this->filename($file, $extension));
            $uploaded++;
        }

        return back()->with(
            'gallery_status',
            $uploaded > 0
                ? 'Загружено файлов: ' . $uploaded . '.'
                : 'Файлы не были загружены.'
        );
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
