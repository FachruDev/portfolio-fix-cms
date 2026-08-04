<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_public_registration_routes_are_closed(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertNotFound();
    }
}
