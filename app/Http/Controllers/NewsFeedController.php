<?php

namespace App\Http\Controllers;

use App\Models\NewsArticle;
use App\Models\NewsCategory;
use App\Models\NewsSource;
use Illuminate\Contracts\Pagination\CursorPaginator;

class NewsFeedController extends Controller
{
    public function index(): CursorPaginator
    {
        $records = NewsArticle::with('source', 'categories')
            ->orderByDesc('published_at');

        if (request()->has('source') && $source = NewsSource::whereSlug(request()->source)->first()) {
            $records->where('source_id', $source->id);
        }

        return $records->cursorPaginate(20);
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
