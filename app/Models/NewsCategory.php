<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property int $parent_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|NewsCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsCategory whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsCategory whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\NewsArticle> $articles
 * @property-read int|null $articles_count
 * @property-read NewsCategory|null $parentCategory
 * @mixin \Eloquent
 */
class NewsCategory extends Model
{
    use HasFactory;

    protected $hidden = ['pivot'];

    protected $fillable = ['name', 'parent_id'];

    const TOP_HEADLINES = 'Top Headlines';
    const BREAKING_NEWS = 'Breaking News';

    public static function breaking(): self|null
    {
        return static::whereName(static::BREAKING_NEWS)->first();
    }

    public function isTopHeadlines(): bool
    {
        return $this->name === self::TOP_HEADLINES;
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(NewsArticle::class, 'news_article_categories');
    }

    public function parentCategory(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'parent_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(NewsCategory::class, 'parent_id');
    }
}
