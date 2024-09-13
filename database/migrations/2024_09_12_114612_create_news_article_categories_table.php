<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('news_article_categories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('news_article_id')->unsigned();
            $table->bigInteger('news_category_id')->unsigned();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_article_categories');
    }
};
