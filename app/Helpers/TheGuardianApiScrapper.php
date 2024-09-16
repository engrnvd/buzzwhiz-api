<?php

namespace App\Helpers;

use App\Models\NewsCategory;
use App\Traits\HasLogs;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class TheGuardianApiScrapper extends BaseNewsScrapper
{
    use HasLogs;

    protected string $url = 'https://content.guardianapis.com/search';
    protected string $topHeadlinesUrl = 'https://content.guardianapis.com/search';
    protected string $apiKeyInEnv = 'THE_GUARDIAN_KEY';
    protected string $logDir = 'the-guardian-api';

    protected function essentialParams(): array
    {
        return [
            'api-key' => env($this->apiKeyInEnv),
            'show-fields' => 'byline,headline,body,thumbnail',
            'page-size' => 50,
        ];
    }

    protected function paramsForSearch(NewsCategory $category, Carbon $startDate): array
    {
        return [
            'q' => $category->parentCategory->name . " " . $category->name,
            'from-date' => $startDate->format('Y-m-d')
        ];

    }

    protected function paramsForTopHeadlines(NewsCategory $category, Carbon $startDate): array
    {
        return [
            'q' => 'top headlines',
            'from-date' => $startDate->format('Y-m-d')
        ];
    }

    protected function getArticlesFromResponse($json): array
    {
        return Arr::get($json, 'response.results', []);
    }

    protected function getSourceNameFromArticle($article): string
    {
        return 'The Guardian';
    }

    protected function getWebUrlFromArticle($article): string
    {
        return Arr::get($article, 'webUrl');
    }

    protected function getAuthorFromArticle($article): string
    {
        return Str::substr(Arr::get($article, 'fields.byline'), 0, 254);
    }

    protected function getDataDataForArticle($article): array
    {
        return [
            'title' => Arr::get($article, 'fields.headline'),
            'description' => Str::substr(strip_tags(Arr::get($article, 'fields.body')), 0, 200),
            'img_url' => Arr::get($article, 'fields.thumbnail'),
            'published_at' => Arr::get($article, 'webPublicationDate'),
        ];
    }
}
