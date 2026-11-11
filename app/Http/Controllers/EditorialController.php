<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class EditorialController extends Controller
{
    public function explore(): View
    {
        return $this->section('jelajah');
    }

    public function culture(): View
    {
        return $this->section('budaya');
    }

    public function culinary(): View
    {
        return $this->section('kuliner');
    }

    public function advertorial(): View
    {
        return $this->section('advertorial');
    }

    public function news(): View
    {
        return $this->section('berita');
    }

    public function article(string $slug): View
    {
        foreach (config('editorial') as $key => $section) {
            foreach ($section['articles'] as $article) {
                if ($article['slug'] === $slug) {
                    return view('editorial.show', [
                        'article' => $article,
                        'section' => $section,
                        'sectionKey' => $key,
                    ]);
                }
            }
        }

        abort(404);
    }

    private function section(string $key): View
    {
        $section = config("editorial.{$key}");

        return view('editorial.index', [
            'section' => $section,
            'articles' => $section['articles'],
        ]);
    }
}
