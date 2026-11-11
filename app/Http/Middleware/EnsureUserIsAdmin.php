<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        if (!$request->user()?->isAdmin()) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
