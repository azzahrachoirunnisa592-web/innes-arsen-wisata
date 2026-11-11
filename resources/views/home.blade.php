@extends('layouts.app')
@section('title', 'Beranda')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/travel-cards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/editorial.css') }}">
@endpush

@section('content')
<section class="home-hero" aria-labelledby="home-title">
    <div id="homeHero" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="6500">
        <div class="carousel-inner">
            @foreach($heroSlides as $slide)
                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                    <img class="home-hero-image" src="{{ $slide['image'] }}" alt="{{ $slide['alt'] }}" @if($loop->first) fetchpriority="high" @else loading="lazy" @endif>
                    <div class="home-hero-shade"></div>
                    <div class="home-hero-copy">
                        <span class="hero-kicker"><span aria-hidden="true">✦</span> Perjalanan Anda, cerita istimewa</span>
                        <h1 @if($loop->first) id="home-title" @endif>{{ $slide['title'] }}</h1>
                        <p>{{ $slide['description'] }}</p>
                        <div class="hero-actions">
                            <a class="button" href="{{ route('packages.index') }}">Pesan paket sekarang <span aria-hidden="true">→</span></a>
                            <a class="button button-outline-light" href="{{ route('editorial.explore') }}">Jelajahi wisata</a>
                        </div>
                        <div class="hero-assurance"><span aria-hidden="true">✓</span> Perjalanan domestik, study tour, dan layanan grup</div>
                    </div>
                    <div class="hero-caption"><span>INDONESIA</span><strong>Begitu banyak cerita untuk dijelajahi.</strong></div>
                </div>
            @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#homeHero" data-bs-slide="prev" aria-label="Slide sebelumnya">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#homeHero" data-bs-slide="next" aria-label="Slide berikutnya">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
    </div>
</section>

<section class="section home-packages" id="destinasi" aria-labelledby="packages-title">
    <div class="section-heading">
        <div><span class="eyebrow">Temukan perjalanan Anda</span><h2 id="packages-title">Paket wisata unggulan</h2></div>
        <a class="text-link" href="{{ route('packages.index') }}">Lihat semua paket <span aria-hidden="true">→</span></a>
    </div>
    <div class="card-grid">
        @forelse($packages as $paket)
            @php
                $destinationImageUrl = $paket->wisata?->image_url;
            @endphp
            <article class="package-card">
                <div class="package-art has-image">
                <img class="package-art-image" src="{{ $destinationImageUrl }}" alt="Pemandangan {{ $paket->wisata?->name }}" loading="lazy">
                    <span>{{ $paket->category }}</span>
                </div>
                <div class="package-body">
                <div class="muted small">{{ $paket->wisata?->name }}{{ $paket->wisata?->province ? ', '.$paket->wisata->province : '' }}</div>
                    <h3>{{ $paket->title }}</h3>
                <div class="package-meta">{{ $paket->duration_days }} hari @if($paket->duration_nights) · {{ $paket->duration_nights }} malam @endif · Kuota {{ $paket->quota }} orang</div>
                    <div class="package-bottom">
                        <strong>Rp {{ number_format($paket->price, 0, ',', '.') }}<small> / orang</small></strong>
                        <a class="text-link" href="{{ route('packages.show', $paket) }}">Detail <span aria-hidden="true">→</span></a>
                    </div>
                </div>
            </article>
        @empty
            <div class="empty-state">Paket wisata akan segera tersedia. Silakan hubungi tim kami untuk informasi lebih lanjut.</div>
        @endforelse
    </div>
</section>

<section class="section inspiration-section" id="artikel" aria-labelledby="articles-title">
    <div class="section-heading">
        <div><span class="eyebrow">Jelajahi lebih dalam</span><h2 id="articles-title">Cerita dan inspirasi perjalanan</h2></div>
        <span class="section-aside">Destinasi, budaya, dan kuliner Indonesia</span>
    </div>
    <div class="article-grid">
        @foreach($articles as $article)
            <article class="article-card" id="{{ ['artikel-jelajah', 'artikel-budaya', 'artikel-kuliner'][$loop->index] }}">
                <img src="{{ $article['image'] }}" alt="{{ $article['alt'] }}" loading="lazy">
                <div class="article-copy">
                    <span class="article-category">{{ $article['category'] ?? ['Jelajah', 'Budaya & Sejarah', 'Kuliner'][$loop->index] }}</span>
                    <h3><a href="{{ route('editorial.show', $article['slug']) }}">{{ $article['title'] }}</a></h3>
                    <p>{{ $article['summary'] }}</p>
                </div>
            </article>
        @endforeach
    </div>
    <section class="latest-news" aria-labelledby="latest-news-title">
        <div class="section-heading">
            <div><span class="eyebrow">Informasi perjalanan</span><h2 id="latest-news-title">Kabar dan panduan terbaru</h2></div>
            <a class="text-link" href="{{ route('editorial.news') }}">Lihat semua berita <span aria-hidden="true">→</span></a>
        </div>
        <div class="news-list">
            @foreach($latestNews as $article)
                <article class="news-item">
                    <span class="news-label">Informasi</span>
                    <h3><a href="{{ route('editorial.show', $article['slug']) }}">{{ $article['title'] }}</a></h3>
                    <p>{{ $article['summary'] }}</p>
                </article>
            @endforeach
        </div>
    </section>
</section>

<section class="company-panel" id="profil" aria-labelledby="profile-title">
    <div class="company-copy">
        <span class="eyebrow">Mitra perjalanan Anda</span>
        <h2 id="profile-title">{{ $profile?->name ?: 'PT. Innes Arsen Wisata' }}</h2>
        <p>{{ $profile?->description ?: 'Mitra perjalanan Anda untuk menjelajahi destinasi pilihan di Indonesia.' }}</p>
        @if($profile?->address)<p class="company-address"><strong>Alamat</strong><br>{{ $profile->address }}</p>@endif
        <div class="contact-list">
            @if($profile?->phone)<a href="tel:{{ $profile->phone }}">☎ {{ $profile->phone }}</a>@endif
            @if($profile?->email)<a href="mailto:{{ $profile->email }}">✉ {{ $profile->email }}</a>@endif
        </div>
        <a class="text-link profile-packages-link" href="{{ route('profile.show') }}">Selengkapnya tentang perusahaan <span aria-hidden="true">→</span></a>
    </div>
    <div class="management">
        <h3>Tim manajemen</h3>
        @forelse($managers as $manager)
            <div class="manager"><span class="manager-avatar">{{ mb_substr($manager->name, 0, 1) }}</span><div><strong>{{ $manager->name }}</strong><small>{{ $manager->position }}</small></div></div>
        @empty
            <p class="muted">Informasi tim manajemen akan diperbarui.</p>
        @endforelse
    </div>
</section>
@endsection
