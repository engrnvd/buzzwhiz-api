<?php

namespace App\Http\Controllers;

use App\Models\NewsSource;

class NewsSourceController extends Controller
{
    public function index()
    {
        return NewsSource::get();
    }

    public function userSources()
    {
        $user = auth()->user();
        $favorites = \DB::table('users_news_sources')
            ->where('user_id', $user->id)
            ->pluck('news_source_id');

        $sources = NewsSource::select(['id', 'name', 'website', 'slug'])->get();

        return ['favorites' => $favorites, 'items' => $sources];
    }

    public function toggleFavorite($id)
    {
        auth()->user()->sources()->toggle([$id]);
        return '';
    }
}
