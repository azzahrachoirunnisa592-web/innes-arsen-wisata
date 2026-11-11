<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Paket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        return view('customer.dashboard', [
            'bookings' => $request->user()->bookings()->with('paket.wisata', 'invoice')->latest()->get(),
            'mous' => $request->user()->mous()->latest()->get(),
        ]);
    }

    public function store(Request $request, Paket $paket): RedirectResponse
    {
        abort_unless($paket->published, 404);

        $validated = $request->validate([
            'departure_date' => ['required', 'date', 'after_or_equal:today'],
            'travelers' => ['required', 'integer', 'min:1', 'max:100'],
            'customer_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $invoice = DB::transaction(function () use ($request, $paket, $validated): Invoice {
            $booking = Booking::create([
                ...$validated,
                'user_id' => $request->user()->id,
                'paket_id' => $paket->id,
                'status' => 'pending_negotiation',
                'proposed_price' => $paket->price * $validated['travelers'],
            ]);

            return $booking->invoice()->create([
                'invoice_number' => 'INV-'.now()->format('Ymd').'-'.Str::upper(Str::random(8)),
                'amount' => $booking->proposed_price,
                'status' => 'pending',
                'issued_at' => now(),
            ]);
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'Permintaan pemesanan dikirim. Admin akan menghubungi Anda untuk negosiasi.');
    }
}
