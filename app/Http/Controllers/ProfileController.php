<?php

namespace App\Http\Controllers;

use App\Models\CompanyGalleryImage;
use App\Models\CompanyProfile;
use App\Models\ManagementMember;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        return view('profile.show', [
            'profile' => CompanyProfile::first(),
            'managers' => ManagementMember::orderBy('sort_order')->get(),
            'content' => config('profile'),
            'latestPosts' => array_slice(config('editorial.berita.articles', []), 0, 3),
            'galleryImages' => CompanyGalleryImage::where('published', true)->orderBy('sort_order')->latest()->get(),
        ]);
    }
}
