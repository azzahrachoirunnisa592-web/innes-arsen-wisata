@extends('layouts.app')
@section('title', 'Pembayaran '.$invoice->invoice_number)
@section('content')
<div class="breadcrumb"><a href="{{ route('customer.dashboard') }}">Pesanan saya</a> / {{ $invoice->invoice_number }}</div>
<section class="page-heading"><span class="eyebrow">Tagihan perjalanan</span><h1>Instruksi pembayaran</h1><p>Invoice {{ $invoice->invoice_number }} · Status <span class="badge" data-booking-status>{{ $invoice->booking->status === 'confirmed' ? 'Terkonfirmasi' : str_replace('_', ' ', $invoice->booking->status) }}</span></p></section>
<div class="payment-layout">
    <section class="panel">
        <h2>Ringkasan pesanan</h2><div class="summary-row"><span>Paket wisata</span><strong>{{ $invoice->booking->paket->title }}</strong></div>
        <div class="summary-row"><span>Keberangkatan</span><strong>{{ $invoice->booking->departure_date->format('d M Y') }}</strong></div>
        <div class="summary-row"><span>Jumlah peserta</span><strong>{{ $invoice->booking->travelers }} orang</strong></div>
        <div class="summary-row summary-total"><span>Total tagihan</span><strong>Rp {{ number_format($invoice->amount, 0, ',', '.') }}</strong></div>
        @if($invoice->booking->admin_notes)<div class="notice"><strong>Catatan admin:</strong> {{ $invoice->booking->admin_notes }}</div>@endif
        <p class="small muted">Status invoice: <span data-invoice-status>{{ $invoice->status === 'paid' ? 'Lunas' : str_replace('_', ' ', $invoice->status) }}</span>.</p>
        <p class="small muted" data-paid-at @if(!$invoice->paid_at) hidden @endif>@if($invoice->paid_at)Waktu pembayaran: {{ $invoice->paid_at->format('d M Y H:i') }}@endif</p>
        <a class="button button-light" href="{{ route('invoices.receipt', $invoice) }}">Cetak invoice / kuitansi</a>
    </section>
    <section class="panel payment-panel">
        <span class="eyebrow">Pembayaran digital</span><h2>Bayar melalui QRIS / transfer</h2>
        @if(file_exists(public_path('images/qris-dana.png')))
            <img class="qris-image" src="{{ asset('images/qris-dana.png') }}" alt="Kode QR profil DANA yang disediakan untuk pembayaran">
            <p class="small muted">Pindai kode dengan aplikasi DANA, lalu masukkan nominal Rp {{ number_format($invoice->amount, 0, ',', '.') }}. Kode pada lampiran adalah kode profil DANA; pastikan nama penerima dan nominal cocok dengan informasi resmi perusahaan sebelum mengirim. Pembayaran akan diperiksa admin secara manual.</p>
        @else
            <div class="qris-missing"><strong>Kode pembayaran DANA belum tersedia</strong><span>Tempatkan gambar kode QR di <code>public/images/qris-dana.png</code>.</span></div>
        @endif
        @if($paymentSimulationEnabled && $invoice->status === 'pending' && in_array($invoice->booking->status, ['pending_negotiation', 'awaiting_payment'], true))
            <div class="simulation-box">
                <p class="small muted">Mode lokal/testing saja. Simulasi tidak memproses atau membuktikan transfer uang sungguhan.</p>
                <button class="button button-light button-block" type="button" data-simulate-payment data-endpoint="{{ route('payments.simulate', $invoice) }}">Simulasi Bayar Berhasil (Test)</button>
            </div>
        @endif
        <div class="alert alert-success" data-simulation-success role="status" hidden></div>
        <div class="alert alert-error" data-simulation-error role="alert" hidden></div>
        @if($paymentMethods->isNotEmpty())
            <div class="method-list">
                @foreach($paymentMethods as $method)
                    <div class="method-card"><div><strong>{{ $method->name }}</strong>@if($method->account_name)<small>{{ $method->account_name }}</small>@endif @if($method->account_number)<span class="account-number">{{ $method->account_number }}</span>@if(str_contains($method->account_number, '*'))<small>Nomor tersamarkan; gunakan QR dan pastikan nama penerima di aplikasi pembayaran.</small>@endif @endif</div>
                        @if($method->account_number && !str_contains($method->account_number, '*'))<button class="button button-light button-small" type="button" data-copy="{{ $method->account_number }}">Salin nomor</button>@endif
                        @if($method->instructions)<p class="method-instructions">{{ $method->instructions }}</p>@endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="notice">Metode pembayaran belum dikonfigurasi. Admin dapat mengatur DANA atau transfer melalui menu Pengaturan.</div>
        @endif
        @if($invoice->booking->status === 'awaiting_payment' && $invoice->status !== 'paid' && $paymentMethods->isNotEmpty())
            <form method="post" action="{{ route('payments.submit', $invoice) }}" enctype="multipart/form-data" class="form-stack payment-form">
                @csrf
                <h3>Konfirmasi pembayaran</h3>
                <label>Metode pembayaran<select name="payment_method_id" required>@foreach($paymentMethods as $method)<option value="{{ $method->id }}" @selected(old('payment_method_id') == $method->id)>{{ $method->name }}</option>@endforeach</select></label>
                <label>Bukti pembayaran (JPG, PNG, PDF; maks. 5 MB)<input type="file" name="proof" accept=".jpg,.jpeg,.png,.pdf" required></label>
                <label>Nomor referensi transaksi (opsional)<input type="text" name="reference" maxlength="100" value="{{ old('reference') }}"></label>
                <label>Catatan (opsional)<textarea name="customer_note" rows="2" maxlength="1000">{{ old('customer_note') }}</textarea></label>
                <button class="button button-block" type="submit">Kirim konfirmasi pembayaran</button>
                <p class="small muted">Konfirmasi tidak memproses transfer otomatis. Admin memeriksa bukti dan mengubah status setelah dana diterima.</p>
            </form>
        @elseif($invoice->booking->status === 'pending_negotiation')
            <div class="notice">Pembayaran akan tersedia setelah admin menyetujui hasil negosiasi dan nominal tagihan.</div>
        @elseif($invoice->status === 'submitted')
            <div class="notice">Bukti pembayaran Anda sedang ditinjau admin.</div>
        @elseif($invoice->status === 'paid')
            <div class="alert alert-success">Pembayaran terverifikasi. Pesanan Anda telah dikonfirmasi.</div>
        @endif
        <div data-payment-history @if($invoice->payments->isEmpty()) hidden @endif>
            <h3>Riwayat konfirmasi</h3>
            <div data-payment-history-rows>
                @foreach($invoice->payments as $payment)<div class="history-row"><span>{{ $payment->paymentMethod->name }} · {{ $payment->created_at->format('d M Y H:i') }} · {{ $payment->status }}@if($payment->reference) · Ref: {{ $payment->reference }}@endif</span>@if($payment->proof_path)<a href="{{ route('documents.payment-proof', $payment) }}">Lihat bukti</a>@endif</div>@endforeach
            </div>
        </div>
    </section>
</div>
@endsection
@if($paymentSimulationEnabled)
    @push('scripts')
    <script>
    const simulationButton = document.querySelector('[data-simulate-payment]');
    const paymentSuccessAudio = new Audio('{{ asset('audio/pembayaran-berhasil.mp3') }}');
    paymentSuccessAudio.preload = 'auto';

    if (simulationButton) {
        simulationButton.addEventListener('click', async () => {
            paymentSuccessAudio.muted = true;
            try {
                await paymentSuccessAudio.play();
                paymentSuccessAudio.pause();
                paymentSuccessAudio.currentTime = 0;
            } catch {
            }
            paymentSuccessAudio.muted = false;

            const successMessage = document.querySelector('[data-simulation-success]');
            const errorMessage = document.querySelector('[data-simulation-error]');
            simulationButton.disabled = true;
            successMessage.hidden = true;
            errorMessage.hidden = true;

            try {
                const response = await fetch(simulationButton.dataset.endpoint, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'Simulasi pembayaran gagal.');
                }

                document.querySelector('[data-invoice-status]').textContent = 'Lunas';
                document.querySelector('[data-booking-status]').textContent = 'Terkonfirmasi';
                const paidAt = document.querySelector('[data-paid-at]');
                paidAt.textContent = `Waktu pembayaran: ${result.paid_at}`;
                paidAt.hidden = false;
                document.querySelector('.payment-form')?.remove();
                simulationButton.remove();

                successMessage.textContent = result.message + ' Pesanan telah dikonfirmasi.';
                successMessage.hidden = false;

                const history = document.querySelector('[data-payment-history]');
                const historyRows = document.querySelector('[data-payment-history-rows]');
                const row = document.createElement('div');
                const details = document.createElement('span');
                row.className = 'history-row';
                details.textContent = `${result.payment.method} · ${result.payment.verified_at} · ${result.payment.description} · Ref: ${result.payment.reference}`;
                row.append(details);
                historyRows.prepend(row);
                history.hidden = false;

                paymentSuccessAudio.currentTime = 0;
                paymentSuccessAudio.play().catch(() => {
                    successMessage.textContent += ' Audio ditahan oleh browser.';
                });
            } catch (error) {
                errorMessage.textContent = error.message;
                errorMessage.hidden = false;
                simulationButton.disabled = false;
            }
        });
    }
    </script>
    @endpush
@endif
