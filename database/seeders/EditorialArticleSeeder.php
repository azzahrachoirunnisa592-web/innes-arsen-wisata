<?php

namespace Database\Seeders;

use App\Models\EditorialArticle;
use Illuminate\Database\Seeder;

class EditorialArticleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('editorial') as $sectionKey => $section) {
            foreach ($section['articles'] as $article) {
                EditorialArticle::firstOrCreate(
                    ['slug' => $article['slug']],
                    [
                        'section' => $sectionKey,
                        'title' => $article['title'],
                        'summary' => $article['summary'],
                        'image' => $article['image'],
                        'alt' => $article['alt'],
                        'body' => $article['body'],
                        'is_published' => true,
                    ],
                );
            }
        }
    }
}
