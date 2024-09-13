<?php

namespace App\Console\Commands;

use App\Helpers\NewsApiScrapper;
use App\Models\NewsCategory;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ScrapeNewsCommand extends Command
{
    protected $signature = 'app:scrape-news';

    protected $description = 'Command to crawl news articles';

    public function handle(): void
    {
        $categories = NewsCategory::whereNotNull('parent_id')
            ->orWhere('name', NewsCategory::TOP_HEADLINES)
            ->get();

        foreach ($categories as $category) {
            (new NewsApiScrapper())->scrape($category, Carbon::yesterday());
        }
    }
}
