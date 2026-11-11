@extends('layouts.app')
@section('title', $article['title'])
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/editorial.css') }}">
@endpush

@section('content')
@php
    $sectionRoutes = [
        'jelajah' => 'editorial.explore',
        'budaya' => 'editorial.culture',
        'kuliner' => 'editorial.culinary',
        'advertorial' => 'editorial.advertorial',
        'berita' => 'editorial.news',
    ];
@endphp
<nav class="editorial-breadcrumb" aria-label="Breadcrumb">
    <a href="{{ route('home') }}">Beranda</a>
    <span aria-hidden="true">/</span>
    <a href="{{ route($sectionRoutes[$sectionKey]) }}">{{ $section['name'] }}</a>
    <span aria-hidden="true">/</span>
    <span aria-current="page">{{ $article['title'] }}</span>
</nav>

<article class="editorial-detail">
    <header class="editorial-detail-header">
        <span class="eyebrow">{{ $section['name'] }}</span>
        <h1>{{ $article['title'] }}</h1>
        <p class="editorial-lead">{{ $article['summary'] }}</p>
    </header>
    <img class="editorial-cover" src="{{ $article['image'] }}" alt="{{ $article['alt'] }}">
    <div class="editorial-body">
        @foreach($article['body'] as $paragraph)
            <p>{{ $paragraph }}</p>
        @endforeach
    </div>
    <a class="text-link" href="{{ route($sectionRoutes[$sectionKey]) }}">← Kembali ke {{ $section['name'] }}</a>
</article>
@endsection
