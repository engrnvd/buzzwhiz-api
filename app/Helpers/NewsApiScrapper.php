<?php

namespace App\Helpers;

use App\Models\NewsCategory;
use App\Traits\HasLogs;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class NewsApiScrapper extends BaseNewsScrapper
{
    use HasLogs;

    protected string $url = 'https://newsapi.org/v2/everything';
    protected string $topHeadlinesUrl = 'https://newsapi.org/v2/top-headlines';
    protected string $apiKeyInEnv = 'NEWS_API_KEY';
    protected string $logDir = 'news-api';

    protected function essentialParams(): array
    {
        return [
            'apiKey' => env($this->apiKeyInEnv),
            'language' => 'en',
        ];
    }

    protected function paramsForSearch(NewsCategory $category, Carbon $startDate): array
    {
        return [
            'q' => $category->parentCategory->name . " " . $category->name,
            'from' => $startDate->format('Y-m-d')
        ];
    }

    protected function getArticlesFromResponse($json): array
    {
        return Arr::get($json, 'articles', []);
    }

    protected function getSourceNameFromArticle($article): string
    {
        return Arr::get($article, 'source.name');
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
}
