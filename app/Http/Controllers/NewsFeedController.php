<?php

namespace App\Http\Controllers;

use App\Models\NewsArticle;
use App\Models\NewsCategory;
use App\Models\NewsSource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Builder;

class NewsFeedController extends Controller
{
    public function index(): CursorPaginator
    {
        if (request()->has('category') && $category = NewsCategory::whereSlug(request()->category)->first()) {
            $query = $category->articles();
        } else {
            $query = NewsArticle::query();
        }

        // user-specific feed
        if ($user = request()->user()) {
            /* @var $user User */

            $query->where(function (Builder $query) use ($user) {
                $categoryIds = $user->categories()->pluck('news_category_id');
                $sourceIds = $user->sources()->pluck('news_source_id');
                $authorIds = $user->authors()->pluck('author_id');
                $query->whereIn('source_id', $sourceIds);
                $query->orWhereIn('author_id', $authorIds);
                $query->orWhereHas('categories', fn(Builder $query) => $query->whereIn('news_category_id', $categoryIds));
            });
        }

        if (request()->has('date')) {
            $date = Carbon::parse(request()->date);
            $query->whereBetween('published_at', [$date->startOfDay(), (clone $date)->endOfDay()]);
        }

        $query->with('source', 'categories')
            ->orderByDesc('published_at');

        if (request()->has('source') && $source = NewsSource::whereSlug(request()->source)->first()) {
            $query->where('source_id', $source->id);
        }

        if (request()->has('query')) {
            $query->where(function (Builder $q) {
                $q->where('title', 'like', '%' . request('query') . '%')
                    ->orWhere('description', 'like', '%' . request('query') . '%')
                    ->orWhere('author', 'like', '%' . request('query') . '%');
            });
        }

        return $query->cursorPaginate(20);
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
