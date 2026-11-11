<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function show(Request $request, Invoice $invoice): View
    {
        $this->authorizeInvoice($request, $invoice);

        return view('payments.show', [
            'invoice' => $invoice->load('booking.paket.wisata', 'booking.user', 'payments.paymentMethod'),
            'paymentMethods' => PaymentMethod::where('active', true)->orderBy('name')->get(),
            'paymentSimulationEnabled' => $this->paymentSimulationEnabled(),
        ]);
    }

    public function simulatePayment(Request $request, Invoice $invoice): JsonResponse
    {
        abort_unless($this->paymentSimulationEnabled(), 404);
        $this->authorizeInvoice($request, $invoice);

        $payment = DB::transaction(function () use ($invoice) {
            $lockedInvoice = Invoice::query()->lockForUpdate()->findOrFail($invoice->getKey());
            $booking = $lockedInvoice->booking()->lockForUpdate()->firstOrFail();

            abort_unless(
                $lockedInvoice->status === 'pending' && $booking->status === 'awaiting_payment',
                422,
                'Simulasi hanya tersedia untuk invoice yang menunggu pembayaran.',
            );

            $paymentMethod = PaymentMethod::query()
                ->where('active', true)
                ->orderBy('id')
                ->first();
            abort_if(!$paymentMethod, 422, 'Aktifkan setidaknya satu metode pembayaran sebelum menjalankan simulasi.');

            $now = now();
            $lockedInvoice->update(['status' => 'paid', 'paid_at' => $now]);
            $booking->update(['status' => 'confirmed']);

            return $lockedInvoice->payments()->create([
                'payment_method_id' => $paymentMethod->id,
                'reference' => 'SIM-'.$lockedInvoice->invoice_number,
                'customer_note' => 'Simulasi pembayaran untuk pengujian lokal/testing.',
                'status' => 'paid',
                'reviewed_at' => $now,
            ])->load('paymentMethod');
        });

        return response()->json([
            'message' => 'Simulasi berhasil. Invoice ditandai lunas untuk pengujian.',
            'invoice_status' => 'paid',
            'booking_status' => 'confirmed',
            'paid_at' => $payment->reviewed_at->format('d M Y H:i'),
            'payment' => [
                'method' => $payment->paymentMethod->name,
                'reference' => $payment->reference,
                'description' => 'Pembayaran simulasi terverifikasi',
                'verified_at' => $payment->reviewed_at->format('d M Y H:i'),
            ],
        ]);
    }

    public function submit(Request $request, Invoice $invoice): RedirectResponse
    {
        $this->authorizeInvoice($request, $invoice);
        abort_unless($invoice->status !== 'paid' && $invoice->booking->status === 'awaiting_payment', 422, 'Pembayaran hanya dapat dikonfirmasi setelah negosiasi disetujui admin.');

        $validated = $request->validate([
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'reference' => ['nullable', 'string', 'max:100'],
            'customer_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $method = PaymentMethod::where('active', true)->findOrFail($validated['payment_method_id']);
        $path = $request->file('proof')->store('payment-proofs');

        DB::transaction(function () use ($invoice, $validated, $method, $path): void {
            $invoice->payments()->create([
                'payment_method_id' => $method->id,
                'proof_path' => $path,
                'reference' => $validated['reference'] ?? null,
                'customer_note' => $validated['customer_note'] ?? null,
                'status' => 'submitted',
            ]);

            $invoice->update(['status' => 'submitted']);
            $invoice->booking()->update(['status' => 'payment_review']);
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'Konfirmasi dan bukti pembayaran berhasil dikirim untuk diperiksa admin.');
    }

    public function receipt(Request $request, Invoice $invoice): View
    {
        $this->authorizeInvoice($request, $invoice);

        return view('payments.receipt', [
            'invoice' => $invoice->load('booking.paket.wisata', 'booking.user'),
        ]);
    }

    private function authorizeInvoice(Request $request, Invoice $invoice): void
    {
        abort_unless($request->user()->isAdmin() || $invoice->booking->user_id === $request->user()->id, 403);
    }

    private function paymentSimulationEnabled(): bool
    {
        return (bool) config('travel.payment.simulation_enabled')
            && app()->environment(['local', 'testing']);
    }
}
