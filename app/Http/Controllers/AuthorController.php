<?php

namespace App\Http\Controllers;

use App\Models\Author;

class AuthorController extends Controller
{
    public function index()
    {
        return Author::get();
    }

    public function userAuthors()
    {
        $user = auth()->user();
        $favorites = \DB::table('users_authors')
            ->where('user_id', $user->id)
            ->pluck('author_id');

        $sources = Author::select(['id', 'name', 'slug'])->get();

        return ['favorites' => $favorites, 'items' => $sources];
    }

    public function toggleFavorite($id)
    {
        auth()->user()->authors()->toggle([$id]);
        return '';
    }
}
