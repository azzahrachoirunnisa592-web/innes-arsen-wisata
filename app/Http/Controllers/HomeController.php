<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use App\Models\ManagementMember;
use App\Models\Paket;
use App\Models\Wisata;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $editorial = config('editorial');

        return view('home', [
            'profile' => CompanyProfile::first(),
            'managers' => ManagementMember::orderBy('sort_order')->get(),
            'packages' => Paket::with('wisata')->where('published', true)->latest()->take(6)->get(),
            'articles' => collect([
                $editorial['jelajah']['articles'][0],
                $editorial['budaya']['articles'][0],
                $editorial['kuliner']['articles'][0],
            ])->values(),
            'latestNews' => $editorial['berita']['articles'],
            'heroSlides' => [
                [
                    'title' => 'Jelajahi keindahan Indonesia bersama kami.',
                    'description' => 'Temukan destinasi pilihan, layanan perjalanan yang personal, dan pengalaman berkesan bersama PT. Innes Arsen Wisata.',
                    'image' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=2200&q=90',
                    'alt' => 'Pemandangan pesisir dan pura di Bali',
                ],
                [
                    'title' => 'Temukan cerita di setiap perjalanan.',
                    'description' => 'Dari warisan budaya hingga lanskap alam, rencanakan perjalanan yang sesuai dengan minat Anda.',
                    'image' => 'https://images.unsplash.com/photo-1604999333679-b86d54738315?auto=format&fit=crop&w=2200&q=90',
                    'alt' => 'Pemandangan candi dan kawasan hijau di Jawa Tengah',
                ],
                [
                    'title' => 'Rencanakan liburan dengan lebih mudah.',
                    'description' => 'Jelajahi pilihan paket dan konsultasikan kebutuhan perjalanan Anda bersama tim kami.',
                    'image' => 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=2200&q=90',
                    'alt' => 'Pemandangan pantai tropis di Indonesia',
                ],
            ],
        ]);
    }

    public function packages(): View
    {
        $categories = Paket::where('published', true)
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
        $destinations = Wisata::whereHas('packages', fn ($query) => $query->where('published', true))
            ->orderBy('name')
            ->get(['id', 'name']);
        $selectedCategory = request()->query('kategori');
        $selectedDestination = request()->query('destinasi');

        return view('packages.index', [
            'packages' => Paket::with('wisata')
                ->where('published', true)
                ->when($selectedCategory, fn ($query) => $query->where('category', $selectedCategory))
                ->when($selectedDestination, fn ($query) => $query->where('wisata_id', $selectedDestination))
                ->latest()
                ->paginate(9)
                ->withQueryString(),
            'categories' => $categories,
            'destinations' => $destinations,
            'selectedCategory' => $selectedCategory,
            'selectedDestination' => $selectedDestination,
        ]);
    }

    public function package(Paket $paket): View
    {
        abort_unless($paket->published, 404);

        return view('packages.show', ['paket' => $paket->load('wisata')]);
    }
}
