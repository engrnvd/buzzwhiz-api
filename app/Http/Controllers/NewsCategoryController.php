<?php

namespace App\Http\Controllers;

use App\Models\NewsCategory;

class NewsCategoryController extends Controller
{
    public function index()
    {
        return NewsCategory::with('categories')
            ->whereNull('parent_id')
            ->get();
    }


    public function userCategories()
    {
        $user = auth()->user();
        $favorites = \DB::table('users_news_categories')
            ->where('user_id', $user->id)
            ->pluck('news_category_id');

        $sources = NewsCategory::select(['id', 'name', 'slug'])
            ->with('categories')
            ->whereNull('parent_id')
            ->get();

        return ['favorites' => $favorites, 'items' => $sources];
    }

    public function toggleFavorite($id)
    {
        auth()->user()->categories()->toggle([$id]);
        return '';
    }
}
