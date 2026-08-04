<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_public_password_confirmation_routes_are_closed(): void
    {
        $this->get('/user/confirm-password')->assertNotFound();
        $this->post('/user/confirm-password', ['password' => 'password'])->assertNotFound();
        $this->get('/user/confirmed-password-status')->assertNotFound();
    }
}
