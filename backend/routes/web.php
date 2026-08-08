<?php

use App\Http\Controllers\Admin\ImageGalleryController;
use App\Http\Controllers\Admin\ServiceDirectionController;
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
        Route::delete('/image-gallery', [ImageGalleryController::class, 'destroy'])->name('admin.image-gallery.destroy');
        Route::get('/style-lab-editor', [StyleLabEditorController::class, 'show'])->name('admin.style-lab-editor');
        Route::post('/style-lab-editor', [StyleLabEditorController::class, 'update'])->name('admin.style-lab-editor.update');
        Route::post('/service-directions', [ServiceDirectionController::class, 'store'])->name('admin.service-directions.store');
        Route::put('/service-directions/{direction}', [ServiceDirectionController::class, 'update'])->name('admin.service-directions.update');
        Route::delete('/service-directions/{direction}', [ServiceDirectionController::class, 'destroy'])->name('admin.service-directions.destroy');
    });
