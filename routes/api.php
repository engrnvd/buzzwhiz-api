<?php

use App\Http\Controllers\NewsCategoryController;
use App\Http\Controllers\NewsFeedController;
use App\Http\Controllers\NewsSourceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/user-sources', [NewsSourceController::class, 'userSources']);
    Route::post('/user-sources/toggle/{id}', [NewsSourceController::class, 'toggleFavorite']);
});

Route::get('/news-feed', [NewsFeedController::class, 'index']);
Route::get('/news-categories', [NewsCategoryController::class, 'index']);
Route::get('/news-sources', [NewsSourceController::class, 'index']);
Route::get('/breaking-news', [NewsFeedController::class, 'breaking']);
