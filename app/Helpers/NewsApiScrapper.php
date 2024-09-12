<?php

namespace App\Helpers;

use App\Models\NewsArticle;
use App\Models\NewsCategory;
use App\Models\NewsSource;
use App\Traits\HasLogs;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class NewsApiScrapper
{
    use HasLogs;

    protected string $url = 'https://newsapi.org/v2/everything';
    protected string $key = 'NEWS_API_KEY';
    protected string $logDir = 'news-api';

    public function scrape(NewsCategory $category, Carbon $from): void
    {
        $this->log('Scraping category ' . $category->name);

        $response = Http::get($this->url, [
            'apiKey' => env($this->key),
            'language' => 'en',
            'from' => $from->format('Y-m-d'),
            'q' => $category->parentCategory->name . " " . $category->name,
        ]);

        if ($response->failed()) {
            $this->log("Request failed with error {$response->status()}. Error: {$response->body()}");
            return;
        }

        $json = $response->json();

        $this->log($json);

        $this->log("Articles found: " . Arr::get($json, 'totalResults'));

        foreach (Arr::get($json, 'articles', []) as $article) {
            // create / find the source if needed
            $sourceName = Arr::get($article, 'source.name');
            if (!$sourceName || Str::contains($sourceName, 'Removed')) continue;

            if (!preg_match('/^(https:\/\/.+?)\//', Arr::get($article, 'url'), $matches)) continue;

            $website = Arr::get($matches, 1);
            if (!$website) continue;

            $newsSource = NewsSource::firstOrCreate([
                'name' => $sourceName,
                'website' => $website,
            ]);

            // create / find the article
            $newsArticle = NewsArticle::whereUrl(Arr::get($article, 'url'))->first();

            if (!$newsArticle) {
                $newsArticle = NewsArticle::create([
                    'source_id' => $newsSource->id,
                    'title' => Arr::get($article, 'title'),
                    'description' => Arr::get($article, 'description'),
                    'author' => Arr::get($article, 'author'),
                    'url' => Arr::get($article, 'url'),
                    'img_url' => Arr::get($article, 'img_url'),
                    'published_at' => Arr::get($article, 'published_at'),
                ]);
            }

            // link to the category
            $newsArticle->categories()->attach($category->id);
        }
    }
}
