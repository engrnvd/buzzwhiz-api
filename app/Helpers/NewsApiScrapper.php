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
    protected string $topHeadlinesUrl = 'https://newsapi.org/v2/top-headlines';
    protected string $key = 'NEWS_API_KEY';
    protected string $logDir = 'news-api';

    public function scrape(NewsCategory $category, Carbon $from): void
    {
        $this->log('Scraping category ' . $category->name);

        $url = $category->isTopHeadlines() ? $this->topHeadlinesUrl : $this->url;
        $params = [
            'apiKey' => env($this->key),
            'language' => 'en',
        ];
        if (!$category->isTopHeadlines()) {
            $params['q'] = $category->parentCategory->name . " " . $category->name;
            $params['from'] = $from->format('Y-m-d');
        }

        $response = Http::get($url, $params);

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

            $newsSource = NewsSource::whereName($sourceName)->first();
            if (!$newsSource) {
                $newsSource = new NewsSource();
                $newsSource->fill([
                    'name' => $sourceName,
                    'website' => $website,
                ]);
                $newsSource->save();
            }

            // create / find the article
            $newsArticle = NewsArticle::whereUrl(Arr::get($article, 'url'))->first();

            if (!$newsArticle) {
                $articleData = [
                    'source_id' => $newsSource->id,
                    'title' => Arr::get($article, 'title'),
                    'description' => Arr::get($article, 'description'),
                    'author' => Str::substr(Arr::get($article, 'author'), 0, 254),
                    'url' => Arr::get($article, 'url'),
                    'img_url' => Arr::get($article, 'urlToImage'),
                    'published_at' => Arr::get($article, 'publishedAt'),
                ];

                // Make sure all the fields are non-empty to filter articles with bad quality
                if (array_filter($articleData) !== $articleData) continue;

                $newsArticle = NewsArticle::create($articleData);
            }

            // link to the category
            $newsArticle->categories()->attach($category->id);
        }
    }
}
