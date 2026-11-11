@extends('layouts.app')
@section('title', 'Profil perusahaan')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endpush

@section('content')
<section class="profile-hero" aria-labelledby="profile-title">
    <div class="profile-hero-overlay"></div>
    <div class="profile-hero-copy">
        <span class="profile-hero-kicker">Tentang kami</span>
        <h1 id="profile-title">Profil Perusahaan</h1>
        <p>{{ $profile?->name ?: 'PT. Innes Arsen Wisata' }} · Melayani perjalanan sejak 2011</p>
        <a class="button profile-hero-button" href="#sejarah">Kenali perusahaan kami</a>
    </div>
</section>

<section class="profile-intro" id="sejarah" aria-labelledby="history-title">
    <div class="profile-intro-copy">
        <span class="eyebrow">Sejak 2011 · Kota Metro, Lampung</span>
        <h2 id="history-title">Mitra perjalanan Anda menjelajahi Indonesia</h2>
        <p>{{ $content['history'] }}</p>
        <p>{{ $profile?->description ?: 'Menyediakan layanan transportasi dan perjalanan pariwisata untuk membantu pelanggan merencanakan perjalanan dengan nyaman.' }}</p>
    </div>
    <aside class="profile-fact-card">
        <span class="profile-fact-icon" aria-hidden="true">IA</span>
        <span class="eyebrow">Lokasi perusahaan</span>
        <h3>{{ $profile?->name ?: 'PT. Innes Arsen Wisata' }}</h3>
        <p>{{ $content['address'] }}</p>
        @if($profile?->phone)<a href="tel:{{ $profile->phone }}">Telepon: {{ $profile->phone }}</a>@endif
        @if($profile?->email)<a href="mailto:{{ $profile->email }}">Email: {{ $profile->email }}</a>@endif
        <a class="profile-instagram" href="{{ $content['instagram_url'] }}" target="_blank" rel="noopener noreferrer">
            <span aria-hidden="true">◎</span> Instagram {{ $content['instagram_handle'] }}
        </a>
    </aside>
</section>

<section class="profile-values" aria-label="Visi dan misi perusahaan">
    <article class="profile-vision">
        <span class="eyebrow">Arah dan tujuan</span>
        <h2>Visi</h2>
        <p>{{ $content['vision'] }}</p>
    </article>
    <article class="profile-mission">
        <span class="eyebrow">Komitmen pelayanan</span>
        <h2>Misi</h2>
        <ol>
            @foreach($content['missions'] as $mission)
                <li><span aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>{{ $mission }}</li>
            @endforeach
        </ol>
    </article>
</section>

<section class="profile-services" aria-labelledby="services-title">
    <div class="profile-section-heading">
        <div><span class="eyebrow">Layanan kami</span><h2 id="services-title">Perjalanan untuk berbagai kebutuhan</h2></div>
        <a class="text-link" href="{{ route('packages.index') }}">Lihat paket wisata <span aria-hidden="true">→</span></a>
    </div>
    <div class="row g-3">
        @foreach($content['services'] as $service)
            <div class="col-12 col-md-6 col-xl-4">
                <article class="profile-service-card h-100">
                    <span class="profile-service-icon" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <h3>{{ $service['title'] }}</h3>
                    <p>{{ $service['description'] }}</p>
                </article>
            </div>
        @endforeach
    </div>
</section>

<section class="profile-management" aria-labelledby="management-title">
    <div class="profile-section-heading">
        <div><span class="eyebrow">Orang di balik perjalanan</span><h2 id="management-title">Struktur manajemen</h2></div>
        <span class="profile-section-note">Informasi mengikuti data manajemen perusahaan.</span>
    </div>
    <div class="profile-team-grid">
        @forelse($managers as $manager)
            <article class="profile-team-card">
                <span class="manager-avatar" aria-hidden="true">{{ mb_substr($manager->name, 0, 1) }}</span>
                <div>
                    <h3>{{ $manager->name }}</h3>
                    <p class="profile-team-position">{{ $manager->position }}</p>
                    @if($manager->bio)<p>{{ $manager->bio }}</p>@endif
                </div>
            </article>
        @empty
            <p class="profile-empty">Informasi struktur manajemen belum ditambahkan.</p>
        @endforelse
    </div>
</section>

<section class="profile-lower-grid" aria-label="Galeri dan artikel terbaru">
    <div class="profile-gallery">
        <div class="profile-section-heading">
            <div><span class="eyebrow">Galeri</span><h2>Suasana perjalanan dan pelayanan</h2></div>
            <a class="profile-instagram-button" href="{{ $content['instagram_url'] }}" target="_blank" rel="noopener noreferrer">
                <span aria-hidden="true">◎</span> Instagram {{ $content['instagram_handle'] }}
            </a>
        </div>
        <p class="profile-gallery-note">Dokumentasi kegiatan kantor dan perjalanan wisata perusahaan.</p>
        <div class="profile-gallery-grid">
            @forelse($galleryImages as $photo)
                <figure class="profile-gallery-item">
                    <img src="{{ Storage::disk('public')->url($photo->image_path) }}" alt="{{ $photo->caption ?: $photo->title }}" loading="lazy">
                    <figcaption>{{ $photo->caption ?: $photo->title }}</figcaption>
                </figure>
            @empty
                <div class="profile-gallery-empty">
                    <p>Foto dokumentasi asli akan tampil di galeri setelah diunggah melalui panel admin.</p>
                    <a href="{{ $content['instagram_url'] }}" target="_blank" rel="noopener noreferrer">Lihat Instagram perusahaan <span aria-hidden="true">↗</span></a>
                </div>
            @endforelse
        </div>
    </div>

    <aside class="profile-latest-posts" aria-labelledby="latest-posts-title">
        <span class="eyebrow">Informasi perjalanan</span>
        <h2 id="latest-posts-title">Artikel terbaru</h2>
        <div class="profile-post-list">
            @forelse($latestPosts as $post)
                <article class="profile-post">
                    <img src="{{ $post['image'] }}" alt="" loading="lazy">
                    <div>
                        <span>Berita &amp; informasi</span>
                        <h3><a href="{{ route('editorial.show', $post['slug']) }}">{{ $post['title'] }}</a></h3>
                        <a class="profile-post-link" href="{{ route('editorial.show', $post['slug']) }}">Baca artikel <span aria-hidden="true">→</span></a>
                    </div>
                </article>
            @empty
                <p class="profile-empty">Artikel terbaru akan segera ditampilkan.</p>
            @endforelse
        </div>
        <a class="button button-light profile-all-posts" href="{{ route('editorial.news') }}">Lihat semua berita</a>
    </aside>
</section>

<section class="profile-contact-cta">
    <div>
        <span class="eyebrow">Mari rencanakan perjalanan</span>
        <h2>Butuh informasi untuk perjalanan Anda?</h2>
        <p>Jelajahi paket yang tersedia atau hubungi tim kami untuk mendiskusikan kebutuhan perjalanan.</p>
    </div>
    <div class="profile-cta-actions">
        <a class="button" href="{{ route('packages.index') }}">Jelajahi destinasi</a>
        @if($profile?->phone)<a class="button button-light" href="tel:{{ $profile->phone }}">Hubungi kami</a>@endif
    </div>
</section>
@endsection
