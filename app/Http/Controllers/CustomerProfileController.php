<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('customer.profile', ['customer' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $customer = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($customer->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'current_password' => ['required_with:password', 'current_password'],
        ]);

        $customer->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ]);

        if (filled($data['password'] ?? null)) {
            $customer->password = $data['password'];
        }

        $customer->save();

        return redirect()->route('customer.profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}
