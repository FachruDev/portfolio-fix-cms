<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_public_password_reset_routes_are_closed(): void
    {
        $this->get('/forgot-password')->assertNotFound();
        $this->post('/forgot-password', ['email' => 'user@example.com'])->assertNotFound();
        $this->get('/reset-password/token')->assertNotFound();
        $this->post('/reset-password', [
            'token' => 'token',
            'email' => 'user@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertNotFound();
    }
}
