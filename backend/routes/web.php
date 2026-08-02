<?php

use App\Http\Controllers\Admin\ImageGalleryController;
use App\Http\Controllers\Admin\StyleLabEditorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['name' => config('app.name'), 'status' => 'ok']);
});

Route::middleware(config('moonshine.auth.middleware', []))
    ->prefix(config('moonshine.prefix', 'admin'))
    ->group(static function (): void {
        Route::get('/image-gallery', [ImageGalleryController::class, 'index'])->name('admin.image-gallery');
        Route::post('/image-gallery/upload', [ImageGalleryController::class, 'upload'])->name('admin.image-gallery.upload');
        Route::post('/style-lab-editor', [StyleLabEditorController::class, 'update'])->name('admin.style-lab-editor.update');
    });
