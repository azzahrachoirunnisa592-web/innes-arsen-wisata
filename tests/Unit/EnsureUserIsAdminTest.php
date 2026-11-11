<?php

namespace Tests\Unit;

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class EnsureUserIsAdminTest extends TestCase
{
    public function test_guest_is_redirected_to_home(): void
    {
        $response = (new EnsureUserIsAdmin)->handle(
            Request::create('/admin'),
            fn () => new Response('authorized'),
        );

        $this->assertSame(route('home'), $response->getTargetUrl());
    }

    public function test_non_admin_is_redirected_to_home(): void
    {
        $request = Request::create('/admin');
        $request->setUserResolver(fn () => User::factory()->make(['role' => 'customer']));

        $response = (new EnsureUserIsAdmin)->handle(
            $request,
            fn () => new Response('authorized'),
        );

        $this->assertSame(route('home'), $response->getTargetUrl());
    }

    public function test_admin_is_allowed_to_continue(): void
    {
        $request = Request::create('/admin');
        $request->setUserResolver(fn () => User::factory()->make(['role' => 'admin']));
        $response = (new EnsureUserIsAdmin)->handle(
            $request,
            fn () => new Response('authorized'),
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('authorized', $response->getContent());
    }
}
