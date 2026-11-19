<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use App\Models\ManagementMember;
use App\Models\Paket;
use App\Models\Wisata;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    private const TRAVEL_INTERESTS = [
        'kuliner' => [
            'label' => 'Wisata kuliner',
            'keywords' => ['kuliner', 'makanan', 'hidangan', 'rasa', 'masakan', 'seafood'],
        ],
        'budaya' => [
            'label' => 'Budaya & sejarah',
            'keywords' => ['budaya', 'sejarah', 'candi', 'museum', 'tradisi', 'warisan'],
        ],
        'alam' => [
            'label' => 'Alam & petualangan',
            'keywords' => ['alam', 'gunung', 'sunrise', 'telaga', 'kawah', 'petualangan'],
        ],
        'pantai' => [
            'label' => 'Pantai & pulau',
            'keywords' => ['pantai', 'pulau', 'gili', 'laut', 'pesisir', 'snorkeling'],
        ],
        'edukasi' => [
            'label' => 'Edukasi / study tour',
            'keywords' => ['edukasi', 'study tour', 'pendidikan', 'belajar', 'sekolah'],
        ],
    ];

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

    public function packages(Request $request): View
    {
        $preferences = $request->validate([
            'minat' => ['nullable', 'in:'.implode(',', array_keys(self::TRAVEL_INTERESTS))],
            'destinasi_preferensi' => ['nullable', 'integer', 'exists:wisatas,id'],
            'budget' => ['nullable', 'numeric', 'min:0', 'max:1000000000'],
            'durasi' => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);
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
        $hasPreferences = collect($preferences)->contains(fn ($value) => $value !== null);
        $recommendations = collect();

        if ($hasPreferences) {
            $recommendations = Paket::with('wisata')
                ->where('published', true)
                ->get()
                ->map(function (Paket $paket) use ($preferences): array {
                    $score = 0;
                    $reasons = [];

                    if (isset($preferences['minat'])) {
                        $interest = self::TRAVEL_INTERESTS[$preferences['minat']];
                        $packageContent = mb_strtolower(implode(' ', [
                            $paket->title,
                            $paket->category,
                            $paket->description,
                            $paket->includes,
                            $paket->wisata?->name,
                            $paket->wisata?->description,
                        ]));
                        $matchesInterest = collect($interest['keywords'])
                            ->contains(fn ($keyword) => str_contains($packageContent, $keyword));

                        if ($matchesInterest) {
                            $score += 5;
                            $reasons[] = 'Sesuai minat '.$interest['label'];
                        }
                    }

                    if (isset($preferences['destinasi_preferensi'])) {
                        if ((int) $preferences['destinasi_preferensi'] === $paket->wisata_id) {
                            $score += 6;
                            $reasons[] = 'Destinasi pilihan Anda';
                        }
                    }

                    if (isset($preferences['budget'])) {
                        $budget = (float) $preferences['budget'];
                        $price = (float) $paket->price;

                        if ($price <= $budget) {
                            $score += 3;
                            $reasons[] = 'Harga sesuai budget';
                        } else {
                            $overBudgetRatio = ($price - $budget) / max($budget, 1);
                            $score -= min(5, max(1, (int) ceil($overBudgetRatio * 5)));
                            $reasons[] = 'Harga melebihi budget pilihan';
                        }
                    }

                    if (isset($preferences['durasi'])) {
                        $dayDifference = abs($paket->duration_days - (int) $preferences['durasi']);
                        $score += 4 - min(8, $dayDifference * 2);
                        $reasons[] = $dayDifference === 0
                            ? 'Durasi sesuai pilihan Anda'
                            : 'Durasi '.$paket->duration_days.' hari';
                    }

                    return [
                        'package' => $paket,
                        'score' => $score,
                        'reasons' => $reasons,
                    ];
                })
                ->sort(function (array $first, array $second): int {
                    return ($second['score'] <=> $first['score'])
                        ?: ((float) $first['package']->price <=> (float) $second['package']->price);
                })
                ->take(3)
                ->values();
        }

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
            'interests' => collect(self::TRAVEL_INTERESTS)
                ->map(fn (array $interest, string $key) => ['key' => $key, 'label' => $interest['label']])
                ->values(),
            'preferences' => $preferences,
            'recommendations' => $recommendations,
        ]);
    }

    public function package(Paket $paket): View
    {
        abort_unless($paket->published, 404);

        return view('packages.show', ['paket' => $paket->load('wisata')]);
    }
}
