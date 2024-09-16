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

class BaseNewsScrapper
{
    use HasLogs;

    protected string $url = 'https://api.nytimes.com/svc/search/v2/articlesearch.json';
    protected string $topHeadlinesUrl = 'https://api.nytimes.com/svc/topstories/v2/home.json';
    protected string $apiKeyInEnv = 'NYT_KEY';
    protected string $logDir = 'nyt-api';

    protected function essentialParams(): array
    {
        return [
            'api-key' => env($this->apiKeyInEnv),
        ];
    }

    protected function paramsForSearch(NewsCategory $category, Carbon $startDate): array
    {
        return [
            'q' => $category->parentCategory->name . " " . $category->name,
            'begin_date' => $startDate->format('Y-m-d')
        ];

    }

    protected function getArticlesFromResponse($json): array
    {
        return Arr::get($json, 'articles', []);
    }

    protected function getSourceNameFromArticle($article): string
    {
        return 'The New York Times';
    }

    protected function getWebUrlFromArticle($article): string
    {
        return Arr::get($article, 'url');
    }

    protected function getAuthorFromArticle($article): string
    {
        return Str::substr(Arr::get($article, 'author'), 0, 254);
    }

    protected function getDataDataForArticle($article): array
    {
        return [
            'title' => Arr::get($article, 'title'),
            'description' => Arr::get($article, 'description'),
            'img_url' => Arr::get($article, 'urlToImage'),
            'published_at' => Arr::get($article, 'publishedAt'),
        ];
    }

    public function scrape(NewsCategory $category, Carbon $from): void
    {
        $url = $category->isTopHeadlines() ? $this->topHeadlinesUrl : $this->url;
        $params = $this->essentialParams();

        if (!$category->isTopHeadlines()) {
            $params = [...$params, ...$this->paramsForSearch($category, $from)];
        }

        $this->log("Scraping category {$category->name}\nurl: {$url}\nparams: " . json_encode($params));

        $response = Http::get($url, $params);

        if ($response->failed()) {
            $this->log("Request failed with error {$response->status()}. Error: {$response->body()}");
            return;
        }

        $json = $response->json();

        $articles = $this->getArticlesFromResponse($json);

        $this->log("Articles found: " . count($articles));
        if (count($articles) < 1) $this->log("res: " . var_export($json, true));

        foreach ($articles as $article) {
            // create / find the source if needed
            $sourceName = $this->getSourceNameFromArticle($article);
            if (!$sourceName || Str::contains($sourceName, 'Removed')) continue;

            $webUrl = $this->getWebUrlFromArticle($article);
            if (!preg_match('/^(https:\/\/.+?)\//', $webUrl, $matches)) continue;

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
            $newsArticle = NewsArticle::whereUrl($webUrl)->first();

            if (!$newsArticle) {
                $articleData = [
                    'source_id' => $newsSource->id,
                    'author' => $this->getAuthorFromArticle($article),
                    'url' => $this->getWebUrlFromArticle($article),
                    ...$this->getDataDataForArticle($article),
                ];

                // Make sure all the fields are non-empty to filter articles with bad quality
                if (array_filter($articleData) !== $articleData) continue;

                $newsArticle = NewsArticle::create($articleData);
                /* @var $newsArticle NewsArticle */
                $newsArticle->saveAuthor($this->getAuthorFromArticle($article));
            }

            // link to the category
            $newsArticle->categories()->syncWithoutDetaching([$category->id]);
        }
    }
}
