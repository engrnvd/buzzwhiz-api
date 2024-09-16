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
        $user = auth()->user();
        $category = NewsCategory::findOrFail($id);
        /* @var $category NewsCategory */

        $query = $user->categories();
        $query->toggle([$id]);

        if (!$category->parent_id) {
            $checked = \DB::table('users_news_categories')
                ->where('user_id', $user->id)
                ->where('news_category_id', $id)
                ->exists();
            $childIds = $category->categories()->pluck('id')->toArray();
            $checked ? $query->syncWithoutDetaching($childIds) : $query->detach($childIds);
        } else {
            $parentCategory = $category->parentCategory;
            $childrenIds = $parentCategory->categories()->pluck('id');
            $checkedCount = \DB::table('users_news_categories')
                ->where('user_id', $user->id)
                ->whereIn('news_category_id', $childrenIds)
                ->count();
            if ($checkedCount === $childrenIds->count()) $query->syncWithoutDetaching([$parentCategory->id]);
            else $query->detach([$parentCategory->id]);
        }

        return 'ok';
    }
}
