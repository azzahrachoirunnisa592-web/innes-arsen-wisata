<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Innes Arsen Wisata') - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @if(request()->is('admin*'))<link rel="stylesheet" href="{{ asset('css/admin.css') }}">@endif
    @stack('styles')
</head>
<body>
<header class="site-header no-print">
    <nav class="navbar navbar-expand-xl nav-wrap container" aria-label="Navigasi situs">
        <a class="brand" href="{{ route('home') }}"><span class="brand-mark">IA</span><span>INNES ARSEN<small>WISATA & TRAVEL</small></span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavigation" aria-controls="mainNavigation" aria-expanded="false" aria-label="Buka navigasi">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-collapse collapse" id="mainNavigation">
            <div class="nav-links ms-auto">
                <a href="{{ route('home') }}" @class(['active' => request()->routeIs('home')])>Beranda</a>
                <a href="{{ route('packages.index') }}" @class(['active' => request()->routeIs('packages.*')])>Destinasi</a>
                <a href="{{ route('editorial.explore') }}" @class(['active' => request()->routeIs('editorial.explore')])>Jelajah</a>
                <a href="{{ route('editorial.culture') }}" @class(['active' => request()->routeIs('editorial.culture')])>Budaya &amp; Sejarah</a>
                <a href="{{ route('editorial.culinary') }}" @class(['active' => request()->routeIs('editorial.culinary')])>Kuliner</a>
                <a href="{{ route('profile.show') }}" @class(['active' => request()->routeIs('profile.show')])>Profil</a>
                <a href="{{ route('editorial.advertorial') }}" @class(['active' => request()->routeIs('editorial.advertorial')])>Advertorial</a>
                <a href="{{ route('editorial.news') }}" @class(['active' => request()->routeIs('editorial.news')])>Berita</a>
                <div class="dropdown">
                    <button class="language-button dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">ID</button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item active" href="{{ route('home') }}" aria-current="page">Bahasa Indonesia</a></li>
                        <li><span class="dropdown-item disabled">English — segera hadir</span></li>
                    </ul>
                </div>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}">Panel admin</a>
                        <a href="{{ route('admin.settings.index') }}">Pengaturan</a>
                    @else
                        <a href="{{ route('customer.dashboard') }}">Pesanan saya</a>
                        <a href="{{ route('mous.index') }}">Kerja sama MoU</a>
                    @endif
                    <span class="nav-user">{{ auth()->user()->name }}</span>
                    <form method="post" action="{{ route('logout') }}">@csrf<button class="nav-logout" type="submit">Keluar</button></form>
                @else
                    <a class="button button-small" href="{{ route('login') }}">Masuk</a>
                @endauth
            </div>
        </div>
    </nav>
</header>

<main class="container page-content">
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())
        <div class="alert alert-error" role="alert"><strong>Mohon periksa kembali:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    @yield('content')
</main>

<footer class="site-footer no-print">
    <div class="container footer-inner">
        <div class="footer-about">
            <a class="brand" href="{{ route('home') }}"><span class="brand-mark">IA</span><span>INNES ARSEN<small>WISATA &amp; TRAVEL</small></span></a>
            <p>{{ isset($profile) && $profile?->description ? $profile->description : 'Mitra perjalanan Anda untuk menjelajahi destinasi pilihan di Indonesia.' }}</p>
        </div>
        <div class="footer-links">
            <strong>Tautan cepat</strong>
            <a href="{{ route('home') }}">Beranda</a>
            <a href="{{ route('packages.index') }}">Paket wisata</a>
            <a href="{{ route('profile.show') }}">Profil perusahaan</a>
            <a href="{{ route('editorial.news') }}">Berita dan informasi</a>
        </div>
        <div class="footer-contact">
            <strong>Hubungi kami</strong>
            @if(isset($profile) && $profile?->phone)<a href="tel:{{ $profile->phone }}">{{ $profile->phone }}</a>@endif
            @if(isset($profile) && $profile?->email)<a href="mailto:{{ $profile->email }}">{{ $profile->email }}</a>@endif
            @if(config('profile.address'))<span>{{ config('profile.address') }}</span>
            @elseif(isset($profile) && $profile?->address)<span>{{ $profile->address }}</span>@endif
            @if(!isset($profile) || (!$profile?->phone && !$profile?->email && !$profile?->address))<a href="{{ route('profile.show') }}">Lihat profil perusahaan</a>@endif
        </div>
        <div class="footer-copyright">&copy; {{ date('Y') }} PT. Innes Arsen Wisata.<br>Hak cipta dilindungi.</div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const navigationToggle = document.querySelector('.navbar-toggler');
const mainNavigation = document.getElementById('mainNavigation');

if (navigationToggle && mainNavigation && !window.bootstrap) {
    navigationToggle.addEventListener('click', () => {
        const isOpen = mainNavigation.classList.toggle('show');
        navigationToggle.setAttribute('aria-expanded', String(isOpen));
    });
}

document.querySelectorAll('.language-button').forEach((button) => {
    button.addEventListener('click', () => {
        if (window.bootstrap) {
            return;
        }

        const menu = button.parentElement.querySelector('.dropdown-menu');
        const isOpen = menu.classList.toggle('show');
        button.setAttribute('aria-expanded', String(isOpen));
    });
});

document.querySelectorAll('[data-copy]').forEach((button) => {
    button.addEventListener('click', async () => {
        const value = button.dataset.copy;
        try {
            await navigator.clipboard.writeText(value);
            button.textContent = 'Tersalin';
            setTimeout(() => button.textContent = 'Salin nomor', 1800);
        } catch {
            const input = document.createElement('textarea');
            input.value = value;
            input.setAttribute('readonly', '');
            input.style.position = 'absolute';
            input.style.left = '-9999px';
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            input.remove();
            button.textContent = 'Tersalin';
            setTimeout(() => button.textContent = 'Salin nomor', 1800);
        }
    });
});

document.querySelectorAll('.package-art-image').forEach((image) => {
    image.addEventListener('error', () => {
        const artwork = image.closest('.package-art');
        if (artwork) {
            image.hidden = true;
            artwork.classList.add('image-unavailable');
        }
    }, { once: true });
});
</script>
@stack('scripts')
</body>
</html>
