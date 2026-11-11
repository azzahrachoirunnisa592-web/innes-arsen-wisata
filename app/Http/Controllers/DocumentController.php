<?php

namespace App\Http\Controllers;

use App\Models\Mou;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function paymentProof(Request $request, Payment $payment): StreamedResponse
    {
        abort_unless($request->user()->isAdmin() || $payment->invoice->booking->user_id === $request->user()->id, 403);
        abort_unless($payment->proof_path && Storage::disk('local')->exists($payment->proof_path), 404);

        return Storage::disk('local')->download($payment->proof_path);
    }

    public function mouFile(Request $request, Mou $mou, string $kind): StreamedResponse
    {
        abort_unless(in_array($kind, ['customer', 'company'], true), 404);
        abort_unless($request->user()->isAdmin() || $mou->user_id === $request->user()->id, 403);

        $path = $kind === 'customer' ? $mou->document_path : $mou->admin_document_path;
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path);
    }
}
