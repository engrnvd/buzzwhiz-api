<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string $website
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|NewsSource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsSource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsSource query()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsSource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsSource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsSource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsSource whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsSource whereWebsite($value)
 * @property string $slug
 * @method static \Illuminate\Database\Eloquent\Builder|NewsSource whereSlug($value)
 * @mixin \Eloquent
 */
class NewsSource extends Model
{
    use HasFactory;

    protected $fillable = ["name", "website", "slug"];

    public function articles(): HasMany
    {
        return $this->hasMany(NewsArticle::class, 'source_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::saving(function (NewsSource $source) {
            $source->slug = \Str::slug($source->name) . "-" . $source->id;
        });
    }
}
