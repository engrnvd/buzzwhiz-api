<?php

namespace Database\Seeders;

use App\Models\NewsCategory;
use App\Models\NewsSource;
use Illuminate\Database\Seeder;

class AddSlugToNewsTablesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = NewsCategory::all();
        foreach ($categories as $category) {
            $category->save();
        }

        $sources = NewsSource::all();
        foreach ($sources as $source) {
            $source->save();
        }
    }
}
