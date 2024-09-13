<?php

use App\Http\Controllers\NewsFeedController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/news-feed', [NewsFeedController::class, 'index']);
