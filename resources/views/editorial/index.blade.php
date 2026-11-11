@extends('layouts.app')
@section('title', $section['name'])
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/editorial.css') }}">
@endpush

@section('content')
<section class="page-heading editorial-heading">
    <span class="eyebrow">{{ $section['name'] }} · Inspirasi perjalanan</span>
    <h1>{{ $section['title'] }}</h1>
    <p>{{ $section['description'] }}</p>
</section>

<section class="editorial-grid" aria-label="Artikel {{ $section['name'] }}">
    @forelse($articles as $article)
        <article class="editorial-card">
            <a class="editorial-image" href="{{ route('editorial.show', $article['slug']) }}" aria-label="Baca {{ $article['title'] }}">
                <img src="{{ $article['image'] }}" alt="{{ $article['alt'] }}" loading="lazy">
            </a>
            <div class="editorial-card-body">
                <span class="article-category">{{ $section['name'] }}</span>
                <h2><a href="{{ route('editorial.show', $article['slug']) }}">{{ $article['title'] }}</a></h2>
                <p>{{ $article['summary'] }}</p>
                <a class="text-link" href="{{ route('editorial.show', $article['slug']) }}">Baca selengkapnya <span aria-hidden="true">→</span></a>
            </div>
        </article>
    @empty
        <p class="empty-state">Konten akan segera diperbarui.</p>
    @endforelse
</section>

<section class="editorial-cta">
    <div>
        <span class="eyebrow">Rencanakan perjalanan</span>
        <h2>Temukan destinasi yang ingin Anda kunjungi</h2>
        <p>Lihat pilihan paket wisata yang tersedia atau hubungi tim kami untuk informasi lebih lanjut.</p>
    </div>
    <a class="button" href="{{ route('packages.index') }}">Lihat paket wisata</a>
</section>
@endsection
