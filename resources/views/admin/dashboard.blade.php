@extends('layouts.app')
@section('title', 'Dashboard admin')
@section('content')
@include('admin._nav')
<section class="page-heading"><span class="eyebrow">Panel administrasi</span><h1>Dashboard</h1><p>Ringkasan aktivitas layanan PT. Innes Arsen Wisata.</p></section>
<div class="stats-grid">
    <div class="stat-card"><span>Konsumen</span><strong>{{ $counts['customers'] }}</strong><a href="{{ route('admin.customers.index') }}">Lihat data →</a></div>
    <div class="stat-card"><span>Paket wisata</span><strong>{{ $counts['packages'] }}</strong><a href="{{ route('admin.packages.index') }}">Kelola paket →</a></div>
    <div class="stat-card"><span>Total pesanan</span><strong>{{ $counts['bookings'] }}</strong><a href="{{ route('admin.bookings.index') }}">Kelola pesanan →</a></div>
    <div class="stat-card stat-attention"><span>Pembayaran menunggu review</span><strong>{{ $counts['payments'] }}</strong><a href="{{ route('admin.bookings.index') }}">Periksa bukti →</a></div>
    <div class="stat-card stat-attention"><span>Pengajuan MoU baru</span><strong>{{ $counts['mous'] }}</strong><a href="{{ route('admin.mous.index') }}">Periksa pengajuan →</a></div>
</div>
<section class="section"><div class="section-heading"><h2>Pesanan terbaru</h2><a class="text-link" href="{{ route('admin.bookings.index') }}">Semua pesanan →</a></div>
    <div class="table-wrap"><table><thead><tr><th>Invoice</th><th>Konsumen</th><th>Paket</th><th>Keberangkatan</th><th>Status</th></tr></thead><tbody>
    @forelse($recentBookings as $booking)<tr><td><a class="text-link" href="{{ route('admin.bookings.index') }}#booking-{{ $booking->id }}">{{ $booking->invoice?->invoice_number }}</a></td><td>{{ $booking->user->name }}</td><td><a class="text-link" href="{{ route('packages.show', $booking->paket) }}">{{ $booking->paket->title }}</a></td><td>{{ $booking->departure_date->format('d M Y') }}</td><td><a class="badge" href="{{ route('admin.bookings.index') }}#booking-{{ $booking->id }}">{{ str_replace('_', ' ', $booking->status) }}</a></td></tr>
    @empty<tr><td colspan="5" class="empty-cell">Belum ada pesanan.</td></tr>@endforelse
    </tbody></table></div>
</section>
@endsection
