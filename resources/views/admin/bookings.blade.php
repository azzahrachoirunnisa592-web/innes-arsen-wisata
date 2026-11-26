@extends('layouts.app')
@section('title', 'Pesanan & pembayaran')
@section('content')
@include('admin._nav')
<section class="page-heading"><span class="eyebrow">Layanan pelanggan</span><h1>Pesanan & pembayaran</h1><p>Tinjau permintaan, tetapkan nominal hasil negosiasi, dan verifikasi bukti transfer.</p></section>
<div class="admin-list">
@forelse($bookings as $booking)<details id="booking-{{ $booking->id }}" class="admin-item" @if($loop->first) open @endif>
    <summary><span><strong>{{ $booking->invoice?->invoice_number }} · {{ $booking->paket->title }}</strong><small>{{ $booking->user->name }} · {{ $booking->departure_date->format('d M Y') }} · {{ $booking->travelers }} peserta</small></span><span class="badge">{{ str_replace('_', ' ', $booking->status) }}</span></summary>
    <div class="admin-item-content"><div class="summary-row"><span>Email / telepon</span><strong>{{ $booking->user->email }} · {{ $booking->user->phone ?: '—' }}</strong></div><div class="summary-row"><span>Nilai paket awal</span><strong>Rp {{ number_format($booking->proposed_price, 0, ',', '.') }}</strong></div><div class="summary-row"><span>Catatan konsumen</span><strong>{{ $booking->customer_notes ?: '—' }}</strong></div>
        @if(in_array($booking->status, ['pending_negotiation', 'awaiting_payment'], true))
        <form method="post" action="{{ route('admin.bookings.negotiate', $booking) }}" class="form-grid admin-edit">@csrf @method('PUT')
            <label>Keputusan<select name="decision"><option value="awaiting_payment">Setujui dan buka pembayaran</option><option value="cancelled">Batalkan pesanan</option></select></label>
            <label>Nominal final (Rp)<input type="number" name="proposed_price" min="1" step="1000" value="{{ $booking->invoice?->amount }}" required></label>
            <label class="span-2">Catatan / hasil negosiasi<textarea name="admin_notes" rows="2">{{ $booking->admin_notes }}</textarea></label><div class="span-2"><button class="button button-small" type="submit">Simpan hasil negosiasi</button></div>
        </form>@endif
        @foreach($booking->invoice?->payments ?? [] as $payment)
            <div class="payment-review"><div><strong>{{ $payment->paymentMethod->name }}</strong><small>Referensi: {{ $payment->reference ?: '—' }} · Status: <span data-payment-status>{{ $payment->status }}</span></small>@if($payment->proof_path)<a href="{{ route('documents.payment-proof', $payment) }}">Unduh bukti pembayaran</a>@endif</div>
                @if($payment->status === 'submitted')<form method="post" action="{{ route('admin.payments.review', $payment) }}" class="inline-actions" data-payment-review>@csrf @method('PUT')<button class="button button-small" name="decision" value="paid" type="submit">Verifikasi diterima</button><button class="button button-danger button-small" name="decision" value="rejected" type="submit">Tolak bukti</button></form>@endif
            </div>
        @endforeach
    </div>
</details>@empty<div class="empty-state">Belum ada pesanan masuk.</div>@endforelse
</div>
<p class="review-feedback" data-review-feedback role="status" aria-live="polite"></p>
@endsection
@push('scripts')
<script>
document.querySelectorAll('[data-payment-review]').forEach((form) => {
    form.addEventListener('submit', async (event) => {
        const submitter = event.submitter;
        if (!submitter || submitter.value !== 'paid') {
            return;
        }

        event.preventDefault();
        const feedback = document.querySelector('[data-review-feedback]');
        const buttons = form.querySelectorAll('button[type="submit"]');
        let audioContext = null;
        let audioReady = Promise.resolve(false);
        const AudioContext = window.AudioContext || window.webkitAudioContext;

        if (AudioContext) {
            try {
                audioContext = new AudioContext();
                audioReady = audioContext.resume().then(() => true, () => false);
            } catch {
                audioContext = null;
            }
        }

        buttons.forEach((button) => { button.disabled = true; });
        if (feedback) {
            feedback.textContent = 'Memverifikasi pembayaran...';
            feedback.classList.remove('is-error');
        }

        const formData = new FormData(form);
        formData.set('decision', submitter.value);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Status pembayaran gagal diperbarui.');
            }

            const paymentStatus = form.closest('.payment-review')?.querySelector('[data-payment-status]');
            if (paymentStatus) {
                paymentStatus.textContent = 'paid';
            }
            form.remove();

            let soundPlayed = false;
            if (audioContext && await audioReady && audioContext.state === 'running') {
                try {
                    const startAt = audioContext.currentTime;
                    [660, 880].forEach((frequency, index) => {
                        const oscillator = audioContext.createOscillator();
                        const gain = audioContext.createGain();
                        const start = startAt + index * 0.13;
                        oscillator.frequency.value = frequency;
                        gain.gain.setValueAtTime(0.0001, start);
                        gain.gain.exponentialRampToValueAtTime(0.14, start + 0.02);
                        gain.gain.exponentialRampToValueAtTime(0.0001, start + 0.22);
                        oscillator.connect(gain);
                        gain.connect(audioContext.destination);
                        oscillator.start(start);
                        oscillator.stop(start + 0.23);
                    });
                    soundPlayed = true;
                } catch {
                    soundPlayed = false;
                }
            }

            if (feedback) {
                feedback.textContent = result.message + (soundPlayed ? '' : ' (Audio tidak tersedia atau diblokir browser.)');
            }
            window.setTimeout(() => window.location.reload(), 1200);
        } catch (error) {
            buttons.forEach((button) => { button.disabled = false; });
            if (feedback) {
                feedback.textContent = error.message;
                feedback.classList.add('is-error');
            }
        }
    });
});
</script>
@endpush
