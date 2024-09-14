<?php

namespace App\Http\Controllers;

use App\Models\NewsArticle;
use App\Models\NewsCategory;
use Illuminate\Contracts\Pagination\CursorPaginator;

class NewsFeedController extends Controller
{
    public function index(): CursorPaginator
    {
        return NewsArticle::with('source', 'categories')
            ->orderByDesc('published_at')
            ->cursorPaginate(20);
    }

    public function breaking(): array|CursorPaginator
    {
        $breaking = NewsCategory::breaking();
        if (!$breaking) {
            \Log::error("Breaking news category not found.");
            return [];
        }

        return $breaking->articles()->with('source', 'categories')
            ->orderByDesc('published_at')
            ->cursorPaginate(20);
    }
}
