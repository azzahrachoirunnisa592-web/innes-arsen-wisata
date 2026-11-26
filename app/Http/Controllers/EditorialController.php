<?php

namespace App\Http\Controllers;

use App\Models\EditorialArticle;
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
        $storedArticle = EditorialArticle::where('slug', $slug)->first();
        if ($storedArticle) {
            abort_unless($storedArticle->is_published, 404);

            return view('editorial.show', [
                'article' => $storedArticle->contentArray(),
                'section' => config("editorial.{$storedArticle->section}"),
                'sectionKey' => $storedArticle->section,
            ]);
        }

        abort(404);
    }

    private function section(string $key): View
    {
        $section = config("editorial.{$key}");
        abort_unless($section, 404);

        return view('editorial.index', [
            'section' => $section,
            'articles' => EditorialArticle::publicForSection($key),
        ]);
    }
}
