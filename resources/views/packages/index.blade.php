@extends('layouts.app')
@section('title', 'Paket wisata')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/travel-cards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/packages.css') }}">
@endpush
@section('content')
<section class="page-heading">
    <span class="eyebrow">Temukan perjalanan Anda</span>
    <h1>Destinasi dan paket wisata</h1>
    <p>Jelajahi pilihan perjalanan domestik, dari warisan budaya hingga pantai tropis. Pilih paket yang sesuai untuk liburan, perjalanan grup, atau study tour.</p>
</section>

<section class="recommendation-panel" aria-labelledby="recommendation-title">
    <div class="recommendation-heading">
        <span class="eyebrow">Pencarian sesuai keinginan Anda</span>
        <h2 id="recommendation-title">Bantu pilihkan paket wisata</h2>
        <p>Ceritakan minat dan kebutuhan perjalanan Anda untuk melihat paket yang paling sesuai.</p>
    </div>
    <form class="recommendation-form" method="get" action="{{ route('packages.index') }}">
        <div class="filter-field">
            <label for="interest-filter">Saya suka wisata</label>
            <select id="interest-filter" name="minat">
                <option value="">Semua jenis wisata</option>
                @foreach($interests as $interest)
                    <option value="{{ $interest['key'] }}" @selected(($preferences['minat'] ?? null) === $interest['key'])>{{ $interest['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-field">
            <label for="preferred-destination-filter">Destinasi pilihan</label>
            <select id="preferred-destination-filter" name="destinasi_preferensi">
                <option value="">Bebas / belum tahu</option>
                @foreach($destinations as $destination)
                    <option value="{{ $destination->id }}" @selected((string) ($preferences['destinasi_preferensi'] ?? '') === (string) $destination->id)>{{ $destination->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-field">
            <label for="budget-filter">Budget maks. per orang (Rp)</label>
            <input id="budget-filter" name="budget" type="number" min="0" max="1000000000" step="50000" placeholder="Contoh: 2000000" value="{{ $preferences['budget'] ?? '' }}">
        </div>
        <div class="filter-field">
            <label for="duration-filter">Durasi yang diinginkan</label>
            <select id="duration-filter" name="durasi">
                <option value="">Bebas durasi</option>
                @foreach([1, 2, 3, 4, 5, 7] as $days)
                    <option value="{{ $days }}" @selected((string) ($preferences['durasi'] ?? '') === (string) $days)>{{ $days }} hari</option>
                @endforeach
            </select>
        </div>
        <button class="button button-small" type="submit">Cari rekomendasi</button>
        @if($preferences)
            <a class="clear-filter" href="{{ route('packages.index') }}">Atur ulang</a>
        @endif
    </form>

    @if($recommendations->isNotEmpty())
        <div class="recommendation-results" aria-live="polite">
            <div class="recommendation-results-heading">
                <h3>Rekomendasi untuk Anda</h3>
                <p>Paket diurutkan berdasarkan kecocokan minat, destinasi, budget, dan durasi yang dipilih.</p>
            </div>
            <div class="card-grid">
                @foreach($recommendations as $recommendation)
                    @php($paket = $recommendation['package'])
                    <article class="package-card recommendation-card">
                        <div class="package-art has-image">
                            <img class="package-art-image" src="{{ $paket->wisata->image_url }}" alt="Pemandangan {{ $paket->wisata->name }}" loading="lazy">
                            <span>{{ $paket->category }}</span>
                        </div>
                        <div class="package-body">
                            <div class="muted small">{{ $paket->wisata->name }}{{ $paket->wisata->province ? ', '.$paket->wisata->province : '' }}</div>
                            <h3>{{ $paket->title }}</h3>
                            @if($recommendation['reasons'])
                                <ul class="recommendation-reasons">
                                    @foreach($recommendation['reasons'] as $reason)
                                        <li>{{ $reason }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="recommendation-fallback">Pilihan tersedia yang paling mendekati preferensi Anda.</p>
                            @endif
                            <div class="package-meta">{{ $paket->duration_days }} hari @if($paket->duration_nights) · {{ $paket->duration_nights }} malam @endif · Kuota {{ $paket->quota }} orang</div>
                            <div class="package-bottom">
                                <strong>Rp {{ number_format($paket->price, 0, ',', '.') }}<small> / orang</small></strong>
                                <a class="text-link" href="{{ route('packages.show', $paket) }}">Lihat detail <span aria-hidden="true">→</span></a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    @elseif($preferences)
        <p class="recommendation-empty" role="status">Belum ada paket yang dapat direkomendasikan. Coba ubah preferensi Anda atau lihat semua paket di bawah.</p>
    @endif
</section>

<section class="package-listing" aria-label="Daftar paket wisata">
    <form class="package-filter" method="get" action="{{ route('packages.index') }}">
        <div class="filter-field">
            <label for="category-filter">Kategori</label>
            <select id="category-filter" name="kategori">
                <option value="">Semua kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" @selected($selectedCategory === $category)>{{ $category }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-field">
            <label for="destination-filter">Destinasi</label>
            <select id="destination-filter" name="destinasi">
                <option value="">Semua destinasi</option>
                @foreach($destinations as $destination)
                    <option value="{{ $destination->id }}" @selected((string) $selectedDestination === (string) $destination->id)>{{ $destination->name }}</option>
                @endforeach
            </select>
        </div>
        <button class="button button-small" type="submit">Terapkan filter</button>
        @if($selectedCategory || $selectedDestination)
            <a class="clear-filter" href="{{ route('packages.index') }}">Hapus filter</a>
        @endif
    </form>

    <div class="card-grid">
    @forelse($packages as $paket)
        <article class="package-card">
            <div class="package-art has-image">
                <img class="package-art-image" src="{{ $paket->wisata->image_url }}" alt="Pemandangan {{ $paket->wisata->name }}" loading="lazy">
                <span>{{ $paket->category }}</span>
            </div>
            <div class="package-body">
                <div class="muted small">{{ $paket->wisata->name }}{{ $paket->wisata->province ? ', '.$paket->wisata->province : '' }}</div>
                <h3>{{ $paket->title }}</h3>
                <p class="clamp">{{ $paket->description }}</p>
                <div class="package-meta">{{ $paket->duration_days }} hari @if($paket->duration_nights) · {{ $paket->duration_nights }} malam @endif · Kuota {{ $paket->quota }} orang</div>
                <div class="package-bottom">
                    <strong>Rp {{ number_format($paket->price, 0, ',', '.') }}<small> / orang</small></strong>
                    <a class="text-link" href="{{ route('packages.show', $paket) }}">Lihat detail <span aria-hidden="true">→</span></a>
                </div>
            </div>
        </article>
    @empty
        <div class="empty-state">{{ $selectedCategory ? 'Belum ada paket pada kategori ini.' : 'Belum ada paket wisata yang dipublikasikan.' }}</div>
    @endforelse
    </div>
    @if($packages->hasPages())
        <nav class="package-pagination" aria-label="Navigasi halaman paket wisata">{{ $packages->links() }}</nav>
    @endif
</section>
@endsection
