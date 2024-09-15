<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelWithSlug extends Model
{
    use HasFactory;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::saved(function ($model) {
            $model->slug = \Str::slug($model->name) . "-" . $model->id;
            $model->saveQuietly();
        });
    }
}
