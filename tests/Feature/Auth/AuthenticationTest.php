<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_public_fortify_login_routes_are_closed(): void
    {
        $this->get('/login')->assertNotFound();
        $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ])->assertNotFound();
    }

    public function test_public_fortify_logout_route_is_closed(): void
    {
        $this->post('/logout')->assertNotFound();
    }

    public function test_filament_login_route_remains_available(): void
    {
        $this->get('/admin/login')->assertOk();
    }
}
