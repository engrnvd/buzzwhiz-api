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
}
