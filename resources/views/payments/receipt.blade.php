@extends('layouts.app')
@section('title', 'Invoice '.$invoice->invoice_number)
@section('content')
<article class="receipt">
    <div class="receipt-head"><div class="brand"><span class="brand-mark">IA</span><span>INNES ARSEN<small>WISATA & TRAVEL</small></span></div><div class="receipt-title"><span class="eyebrow">{{ $invoice->status === 'paid' ? 'BUKTI PEMBAYARAN' : 'TAGIHAN PEMESANAN' }}</span><h1>{{ $invoice->invoice_number }}</h1><span>{{ $invoice->issued_at?->format('d M Y') }}</span></div></div>
    <hr><div class="receipt-columns"><div><span class="muted small">DITERBITKAN KEPADA</span><h3>{{ $invoice->booking->user->name }}</h3><p>{{ $invoice->booking->user->email }}<br>{{ $invoice->booking->user->phone }}</p></div><div><span class="muted small">STATUS PEMBAYARAN</span><h3>{{ strtoupper($invoice->status) }}</h3>@if($invoice->paid_at)<p>Dibayar {{ $invoice->paid_at->format('d M Y H:i') }}</p>@endif</div></div>
    <div class="table-wrap"><table><thead><tr><th>Deskripsi</th><th>Keberangkatan</th><th>Peserta</th><th class="right">Jumlah</th></tr></thead><tbody><tr><td>{{ $invoice->booking->paket->title }}<small class="block muted">{{ $invoice->booking->paket->wisata->name }}</small></td><td>{{ $invoice->booking->departure_date->format('d M Y') }}</td><td>{{ $invoice->booking->travelers }}</td><td class="right">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td></tr></tbody></table></div>
    <div class="receipt-total"><span>Total</span><strong>Rp {{ number_format($invoice->amount, 0, ',', '.') }}</strong></div>
    @if($invoice->status !== 'paid')<p class="notice">Dokumen ini merupakan invoice pemesanan, bukan bukti dana diterima. Kuitansi pembayaran sah setelah status diverifikasi admin.</p>@else<p class="receipt-thanks">Terima kasih telah memilih PT. Innes Arsen Wisata.</p>@endif
    <div class="receipt-actions no-print"><button class="button" onclick="window.print()">Cetak / Simpan PDF</button><a class="button button-light" href="{{ route('invoices.show', $invoice) }}">Kembali ke pembayaran</a></div>
</article>
@endsection
