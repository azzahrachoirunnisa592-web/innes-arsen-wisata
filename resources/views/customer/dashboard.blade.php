@extends('layouts.app')
@section('title', 'Pesanan saya')
@section('content')
<section class="page-heading page-heading-row"><div><span class="eyebrow">Ruang pelanggan</span><h1>Halo, {{ auth()->user()->name }}</h1><p>Pantau pemesanan, pembayaran, dan dokumen perjalanan Anda.</p></div><div class="action-row"><a class="button button-light" href="{{ route('customer.profile.edit') }}">Profil saya</a><a class="button button-light" href="{{ route('mous.index') }}">Pengajuan MoU</a></div></section>
<section class="section section-tight"><div class="section-heading"><h2>Riwayat pemesanan</h2><a class="text-link" href="{{ route('packages.index') }}">Cari paket wisata →</a></div>
    <div class="table-wrap"><table><thead><tr><th>Invoice</th><th>Paket</th><th>Keberangkatan</th><th>Peserta</th><th>Status</th><th></th></tr></thead><tbody>
    @forelse($bookings as $booking)<tr><td><a class="text-link" href="{{ route('invoices.show', $booking->invoice) }}">{{ $booking->invoice?->invoice_number }}</a></td><td><a class="text-link" href="{{ route('packages.show', $booking->paket) }}">{{ $booking->paket->title }}</a></td><td>{{ $booking->departure_date->format('d M Y') }}</td><td>{{ $booking->travelers }}</td><td><a class="badge" href="{{ route('invoices.show', $booking->invoice) }}">{{ str_replace('_', ' ', $booking->status) }}</a></td><td><a class="text-link" href="{{ route('invoices.show', $booking->invoice) }}">Detail</a></td></tr>
    @empty<tr><td colspan="6" class="empty-cell">Belum ada pesanan. <a href="{{ route('packages.index') }}">Jelajahi paket wisata.</a></td></tr>@endforelse
    </tbody></table></div>
</section>
<section class="section section-tight"><div class="section-heading"><h2>Pengajuan kerja sama</h2><a class="text-link" href="{{ route('mous.index') }}">Kelola MoU →</a></div>
    <div class="table-wrap"><table><thead><tr><th>Instansi</th><th>Tanggal</th><th>Status</th><th>Catatan admin</th></tr></thead><tbody>
    @forelse($mous as $mou)<tr><td>{{ $mou->organization_name }}</td><td>{{ $mou->created_at->format('d M Y') }}</td><td><span class="badge">{{ str_replace('_', ' ', $mou->status) }}</span></td><td>{{ $mou->admin_notes ?: '—' }}</td></tr>
    @empty<tr><td colspan="4" class="empty-cell">Belum ada pengajuan MoU.</td></tr>@endforelse
    </tbody></table></div>
</section>
@endsection
