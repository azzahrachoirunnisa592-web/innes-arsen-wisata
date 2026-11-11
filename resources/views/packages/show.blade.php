@extends('layouts.app')
@section('title', $paket->title)
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/travel-cards.css') }}">
@endpush
@section('content')
<div class="breadcrumb"><a href="{{ route('packages.index') }}">Paket wisata</a> / {{ $paket->title }}</div>
<div class="detail-grid">
    <article class="detail-main">
        <div class="package-art package-art-large has-image">
            <img class="package-art-image" src="{{ $paket->wisata->image_url }}" alt="Pemandangan {{ $paket->wisata->name }}">
            <span>{{ $paket->category }}</span>
        </div>
        <span class="eyebrow">{{ $paket->wisata->name }}{{ $paket->wisata->province ? ' · '.$paket->wisata->province : '' }}</span>
        <h1>{{ $paket->title }}</h1><div class="package-meta">{{ $paket->duration_days }} hari · {{ $paket->duration_nights }} malam · Kuota {{ $paket->quota }} orang</div>
        <h2>Ringkasan perjalanan</h2><p class="long-copy">{{ $paket->description }}</p>
        @if($paket->includes)<h3>Termasuk</h3><p class="long-copy">{{ $paket->includes }}</p>@endif
        @if($paket->excludes)<h3>Tidak termasuk</h3><p class="long-copy">{{ $paket->excludes }}</p>@endif
    </article>
    <aside class="booking-card"><span class="eyebrow">Harga mulai dari</span><div class="price-large">Rp {{ number_format($paket->price, 0, ',', '.') }}<small> / orang</small></div>
        @auth
            @if(auth()->user()->isAdmin())<p class="muted">Admin tidak dapat membuat pesanan pelanggan. Gunakan akun konsumen untuk mencoba alur pemesanan.</p>
            @else
                <form method="post" action="{{ route('bookings.store', $paket) }}" class="form-stack">
                    @csrf
                    <label>Tanggal keberangkatan<input type="date" name="departure_date" min="{{ now()->toDateString() }}" value="{{ old('departure_date') }}" required></label>
                    <label>Jumlah peserta<input type="number" name="travelers" min="1" max="100" value="{{ old('travelers', 1) }}" required></label>
                    <label>Catatan<textarea name="customer_notes" rows="3" maxlength="2000" placeholder="Kebutuhan khusus atau pertanyaan">{{ old('customer_notes') }}</textarea></label>
                    <button class="button button-block" type="submit">Ajukan pemesanan</button>
                </form>
                <p class="small muted">Pemesanan akan dikonfirmasi admin sebelum pembayaran dilakukan.</p>
            @endif
        @else
            <p>Masuk atau daftar untuk memesan paket ini.</p><a class="button button-block" href="{{ route('login') }}">Masuk untuk memesan</a>
        @endauth
    </aside>
</div>
@endsection
