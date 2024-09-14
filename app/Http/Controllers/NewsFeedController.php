<?php

namespace App\Http\Controllers;

use App\Models\NewsArticle;
use App\Models\NewsCategory;
use App\Models\NewsSource;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Builder;

class NewsFeedController extends Controller
{
    public function index(): CursorPaginator
    {
        if (request()->has('category') && $category = NewsCategory::whereSlug(request()->category)->first()) {
            $records = $category->articles();
        } else {
            $records = NewsArticle::query();
        }

        if (request()->has('date')) {
            $date = Carbon::parse(request()->date);
            $records->whereBetween('published_at', [$date->startOfDay(), (clone $date)->endOfDay()]);
        }

        $records->with('source', 'categories')
            ->orderByDesc('published_at');

        if (request()->has('source') && $source = NewsSource::whereSlug(request()->source)->first()) {
            $records->where('source_id', $source->id);
        }

        if (request()->has('query')) {
            $records->where(function (Builder $q) {
                $q->where('title', 'like', '%' . request('query') . '%')
                    ->orWhere('description', 'like', '%' . request('query') . '%')
                    ->orWhere('author', 'like', '%' . request('query') . '%');
            });
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
