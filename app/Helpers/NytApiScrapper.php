<?php

namespace App\Helpers;

use App\Models\NewsCategory;
use App\Traits\HasLogs;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class NytApiScrapper extends BaseNewsScrapper
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
            'begin_date' => $startDate->format('Ymd')
        ];

    }

    protected function getArticlesFromResponse($json): array
    {
        return Arr::get($json, 'response.docs', []);
    }

    protected function getSourceNameFromArticle($article): string
    {
        return 'The New York Times';
    }

    protected function getWebUrlFromArticle($article): string
    {
        return Arr::get($article, 'web_url');
    }

    protected function getAuthorFromArticle($article): string
    {
        $fullName = Arr::get($article, 'byline.person.firstname') . " " . Arr::get($article, 'byline.person.lastname');
        return Str::substr($fullName, 0, 254);
    }

    protected function getDataDataForArticle($article): array
    {
        $multimedia = Arr::get($article, 'multimedia');
        $item = collect($multimedia)->where('type', 'image')->first();
        return [
            'title' => Arr::get($article, 'abstract'),
            'description' => Arr::get($article, 'lead_paragraph'),
            'img_url' => Arr::get($item, 'url'),
            'published_at' => Arr::get($article, 'pub_date'),
        ];
    }
}
