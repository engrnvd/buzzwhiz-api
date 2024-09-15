<?php

namespace Database\Seeders;

use App\Models\NewsArticle;
use Illuminate\Database\Seeder;

class SaveAuthorsSeeder extends Seeder
{
    public function run(): void
    {
        NewsArticle::query()->select(['id', 'author', 'author_id'])->chunk(100, function ($articles) {
            foreach ($articles as $article) {
                /* @var $article NewsArticle */
                $article->saveAuthor($article->author);
            }
        });
    }
}
