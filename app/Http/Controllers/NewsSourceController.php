<?php

namespace App\Http\Controllers;

use App\Models\NewsSource;

class NewsSourceController extends Controller
{
    public function index()
    {
        return NewsSource::get();
    }
}
