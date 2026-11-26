<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class EditorialArticle extends Model
{
    protected $fillable = [
        'section',
        'slug',
        'title',
        'summary',
        'image',
        'alt',
        'body',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'body' => 'array',
            'is_published' => 'boolean',
        ];
    }

    public static function publicForSection(string $section): Collection
    {
        $storedArticles = static::query()
            ->where('section', $section)
            ->orderByDesc('id')
            ->get();

        return $storedArticles
            ->where('is_published', true)
            ->map(fn (self $article): array => $article->contentArray())
            ->values();
    }

    public function contentArray(): array
    {
        return $this->only(['slug', 'title', 'summary', 'image', 'alt', 'body']);
    }
}
