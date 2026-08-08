<?php

use App\Http\Controllers\Api\CmsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('bootstrap', [CmsController::class, 'bootstrap']);
    Route::get('all', [CmsController::class, 'all']);
    Route::get('pages/{slug}', [CmsController::class, 'page'])->where('slug', '.*');
    Route::get('projects', [CmsController::class, 'projects']);
    Route::get('projects/{slug}', [CmsController::class, 'project']);
    Route::get('services', [CmsController::class, 'services']);
    Route::get('services/{slug}', [CmsController::class, 'service']);
    Route::get('news', [CmsController::class, 'news']);
    Route::get('news/{slug}', [CmsController::class, 'newsArticle']);
    Route::get('promos', [CmsController::class, 'promos']);
    Route::get('awards', [CmsController::class, 'awards']);
    Route::get('partners', [CmsController::class, 'partners']);
    Route::get('reviews', [CmsController::class, 'reviews']);
    Route::get('faqs', [CmsController::class, 'faqs']);
    Route::post('leads', [CmsController::class, 'storeLead']);
});
