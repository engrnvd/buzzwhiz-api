<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 *
 *
 * @property int $id
 * @property string $title
 * @property int $source_id
 * @property string $author
 * @property string $description
 * @property string $url
 * @property string $img_url
 * @property \Illuminate\Support\Carbon $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|NewsArticle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsArticle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsArticle query()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsArticle whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsArticle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsArticle whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsArticle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsArticle whereImgUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsArticle wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsArticle whereSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsArticle whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsArticle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsArticle whereUrl($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\NewsCategory> $categories
 * @property-read int|null $categories_count
 * @property-read \App\Models\NewsSource|null $source
 * @property int|null $author_id
 * @method static \Illuminate\Database\Eloquent\Builder|NewsArticle whereAuthorId($value)
 * @mixin \Eloquent
 */
class NewsArticle extends Model
{
    use HasFactory;

    protected $hidden = ['pivot'];

    protected $fillable = [
        'title',
        'source_id',
        'author',
        'description',
        'url',
        'img_url',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(NewsCategory::class, 'news_article_categories')
            ->select(['news_categories.id', 'name']);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(NewsSource::class)
            ->select(['news_sources.id', 'name', 'website']);
    }

    public function saveAuthor($authorName): void
    {
        $author = Author::whereName($authorName)->first();
        if (!$author) {
            $author = new Author();
            $author->name = $authorName;
            $author->save();
        }
        $this->author_id = $author->id;
        $this->save();
    }
}
