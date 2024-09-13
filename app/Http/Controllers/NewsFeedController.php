<?php

namespace App\Http\Controllers;

use App\Models\NewsArticle;

class NewsFeedController extends Controller
{
    public function index()
    {
        return NewsArticle::with('source', 'categories')
            ->orderByDesc('published_at')
            ->cursorPaginate(50);
    }
}
