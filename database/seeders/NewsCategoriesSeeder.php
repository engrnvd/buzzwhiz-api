<?php

namespace Database\Seeders;

use App\Models\NewsCategory;
use Illuminate\Database\Seeder;

class NewsCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            "Top Headlines" => [],
            "Politics" => [
                "Elections",
                "Public Policy",
                "Protests",
                "Human Rights",
                "Law",
                "Military"
            ],
            "World News" => [
                "Local News",
                "National News",
                "Breaking News",
            ],
            "Business" => [
                "Economy",
                "Stock Market",
                "Cryptocurrency",
                "Banking",
                "Venture Capital",
                "Mergers & Acquisitions",
                "Startups",
                "E-commerce",
                "Real Estate",
            ],
            "Technology" => [
                "AI & Robotics",
                "Cybersecurity",
                "Mobile",
                "Social Media",
                "Data Privacy",
                "Streaming",
                "Technology Innovations",
                "Artificial Intelligence"
            ],
            "Entertainment" => [
                "Movies",
                "Television",
                "Music",
                "Box Office",
                "Video Games",
            ],
            "Sports" => [
                "Football",
                "Basketball",
                "Cricket",
                "Olympics",
                "Tennis",
                "Golf",
                "Motorsports"
            ],
            "Health" => [
                "Fitness",
                "Mental Health",
                "Nutrition",
                "Public Health",
                "Medical Research",
            ],
            "Science" => [
                "Space",
                "Astronomy",
                "Physics",
                "Chemistry",
                "Biotechnology",
                "Psychology"
            ],
            "Environment" => [
                "Climate Change",
                "Conservation",
                "Wildlife",
                "Renewable Energy"
            ],
            "Fashion" => [
                "Trends",
                "Luxury",
                "Shopping"
            ],
            "Automotive" => [
                "Electric Vehicles",
                "Car Reviews",
                "Motorsports"
            ],
            "Crime" => [
                "Investigations",
                "Court Cases",
                "Police Reports"
            ],
            "Weather" => [
                "Climate Change",
                "Storms",
                "Natural Disasters"
            ],
            "Space" => [
                "Space Exploration",
                "Astronomy",
                "NASA",
                "Space Technology"
            ],
            "Economy" => [
                "Global Economy",
                "Local Markets",
                "Trade",
                "Manufacturing"
            ],
        ];

        foreach ($categories as $category => $subCategories) {
            $categoryModel = new NewsCategory(['name' => $category, 'parent_id' => null]);
            $categoryModel->saveQuietly();

            foreach ($subCategories as $subCategory) {
                $subCategoryModel = new NewsCategory(['name' => $subCategory, 'parent_id' => $categoryModel->id]);
                $subCategoryModel->saveQuietly();
            }
        }
    }
}
