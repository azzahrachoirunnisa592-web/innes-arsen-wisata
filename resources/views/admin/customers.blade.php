@extends('layouts.app')
@section('title', 'Data konsumen')
@section('content')
@include('admin._nav')
<section class="page-heading"><span class="eyebrow">Data master</span><h1>Data konsumen</h1><p>Daftar akun pelanggan yang terdaftar dan jumlah pemesanannya.</p></section>
<div class="table-wrap"><table><thead><tr><th>Nama</th><th>Email</th><th>Telepon</th><th>Terdaftar</th><th>Pesanan</th></tr></thead><tbody>
@forelse($customers as $customer)<tr><td>{{ $customer->name }}</td><td>{{ $customer->email }}</td><td>{{ $customer->phone ?: '—' }}</td><td>{{ $customer->created_at->format('d M Y') }}</td><td>{{ $customer->bookings_count }}</td></tr>@empty<tr><td colspan="5" class="empty-cell">Belum ada konsumen.</td></tr>@endforelse
</tbody></table></div>
@endsection
