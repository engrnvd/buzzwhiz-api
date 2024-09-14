<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('news_categories', function (Blueprint $table) {
            $table->string('slug')->nullable();
        });
        Schema::table('news_sources', function (Blueprint $table) {
            $table->string('slug')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('news_categories', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
        Schema::table('news_sources', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
