<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MoUController extends Controller
{
    public function index(Request $request): View
    {
        return view('mous.index', ['mous' => $request->user()->mous()->latest()->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'organization_name' => ['required', 'string', 'max:180'],
            'contact_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'description' => ['required', 'string', 'max:5000'],
            'document' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        $documentPath = $request->file('document')?->store('mou-documents');
        unset($validated['document']);

        $request->user()->mous()->create([
            ...$validated,
            'status' => 'submitted',
            'document_path' => $documentPath,
        ]);

        return redirect()->route('mous.index')->with('success', 'Pengajuan kerja sama berhasil dikirim.');
    }
}
